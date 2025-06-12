<?php
 
namespace App\Http\Controllers;
 
use App\Models\Product;
use App\Models\Category;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Traits\PhotoFileStorage;
 
class ProductController extends Controller
{
    use PhotoFileStorage;
    public function index(Request $request): View
    {
        $filterByName = $request->get('name');
        $orderPrice = $request->get('order_price');
        $orderStock = $request->get('order_stock');
 
        $productQuery = Product::withTrashed()->with('category');
 
        if ($filterByName) {
            $productQuery->where(function ($query) use ($filterByName) {
                $query->where('name', 'LIKE', "%$filterByName%")
                    ->orWhereHas('category', function ($query) use ($filterByName) {
                        $query->where('name', 'LIKE', "%$filterByName%");
                    });
            });
        }
 
        if (in_array($orderPrice, ['asc', 'desc'])) {
            $productQuery->orderByRaw("CASE WHEN discount > 0 THEN price - discount ELSE price END {$orderPrice}");
        }
 
        if (in_array($orderStock, ['asc', 'desc'])) {
            $productQuery->orderBy('stock', $orderStock);
        }
 
        $allProducts = $productQuery->paginate(20)->withQueryString();
 
        $tr = new GoogleTranslate('en');
        foreach ($allProducts as $product) {
            $product->description_translated = $tr->translate($product->description);
        }
 
        $userType = auth()->check() ? auth()->user()->type : 'guest';
 
        return view('products.index', compact('allProducts', 'orderPrice', 'orderStock', 'filterByName', 'userType'));
    }
 
    public function create(): View
    {
        $categories = Category::all();
        return view('products.create', [
            'mode' => 'create',
            'product' => new Product(),
            'categories' => $categories
        ]);
    }
 
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);
        
        // Validação adicional específica do produto
        $errorMessage = $this->validateProductData($validated);
        if ($errorMessage !== null) {
            return back()
                ->withInput()
                ->with('alert-type', 'danger')
                ->with('alert-msg', $errorMessage);
        }
 
        $product = Product::create($validated);
        
        if ($request->hasFile('photo')) {
            $this->storePhoto($request->file('photo'), $product, 'photo', 'products');
        }
        
        // Verifica se precisa criar supply order
        $this->createSupplyOrderIfNeeded($product);
 
        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso.');
    }
 
    public function edit(Product $product): View
    {
        $categories = Category::all();
        $userType = auth()->check() ? auth()->user()->type : 'guest';
 
        $tr = new GoogleTranslate('en');
        $product->description_translated = $tr->translate($product->description);
 
        return view('products.edit', [
            'mode' => 'edit',
            'product' => $product,
            'categories' => $categories,
            'userType' => $userType
        ]);
    }
 
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product->id);
        
        // Validação adicional específica do produto
        $errorMessage = $this->validateProductData($validated);
        if ($errorMessage !== null) {
            return back()
                ->withInput()
                ->with('alert-type', 'danger')
                ->with('alert-msg', $errorMessage);
        }
 
        if ($request->hasFile('photo')) {
            $this->deletePhoto($product, 'photo', 'products');
            $this->storePhoto($request->file('photo'), $product, 'photo', 'products');
            
            unset($validated['photo']);
        }
 
        $product->update($validated);
        
        // Verifica se precisa criar supply order
        $this->createSupplyOrderIfNeeded($product);
 
        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }
 
    public function destroy(Product $product): RedirectResponse
    {
        try {
            // Verifica se o produto já foi vendido (existe na tabela items_orders)
            $hasSales = \DB::table('items_orders')
                ->where('product_id', $product->id)
                ->exists();
 
            if ($hasSales) {
                // Se já foi vendido, faz soft delete
                $product->delete();
                $alertType = 'success';
                $alertMsg = "O produto {$product->name} já tem vendas associadas e foi movido para a lixeira.";
            } else {
                // Se nunca foi vendido, remove a foto e deleta permanentemente
                $this->deletePhoto($product, 'photo', 'products');
                $product->forceDelete();
                $alertType = 'success';
                $alertMsg = "O produto {$product->name} foi eliminado permanentemente.";
            }
        } catch (\Exception $e) {
            $alertType = 'danger';
            $alertMsg = "Erro ao tentar eliminar o produto {$product->name}: " . $e->getMessage();
        }
 
        return back()
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
 
    public function showcase(): View
    {
        $this->authorize('viewShowCase', Product::class);
        return view('products.showcase');
    }
 
    public function show(Product $product): View
    {
        $categories = Category::all();
 
        $tr = new GoogleTranslate('en');
        $product->description_translated = $tr->translate($product->description);
 
        return view('products.show', [
            'product' => $product,
            'categories' => $categories,
            'mode' => 'show'
        ]);
    }
 
    public function restore(int $id): RedirectResponse
    {
        $product = Product::withTrashed()->findOrFail($id);
        
        try {
            $product->restore();
            $alertType = 'success';
            $alertMsg = "O produto {$product->name} foi restaurado com sucesso.";
        } catch (\Exception $e) {
            $alertType = 'danger';
            $alertMsg = "Erro ao tentar restaurar o produto {$product->name}: " . $e->getMessage();
        }
 
        return back()
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
 
    private function validateProduct(Request $request, ?int $productId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_lower_limit' => ['nullable', 'integer', 'min:0'],
            'stock_upper_limit' => ['nullable', 'integer', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'discount_min_qty' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image'],
        ]);
    }
 
    /**
     * Validate stock against upper limit
     */
    private function validateStockAgainstLimit(int $stock, int $upperLimit): bool
    {
        return $stock <= $upperLimit;
    }
 
    /**
     * Validate if discount can be applied based on stock
     */
    private function validateDiscountStock(int $stock, ?int $minQty, ?float $discount): bool
    {
        if (!$discount) {
            return true;
        }
 
        return $stock >= ($minQty ?? 0);
    }
 
    /**
     * Validate product data before saving
     */
    private function validateProductData(array $data): ?string
    {
        $stock = (int)($data['stock'] ?? 0);
        $upperLimit = (int)($data['stock_upper_limit'] ?? 0);
        $minQty = (int)($data['discount_min_qty'] ?? 0);
        $discount = (float)($data['discount'] ?? 0);
 
        // Validate stock upper limit
        if (!$this->validateStockAgainstLimit($stock, $upperLimit)) {
            return "The stock ({$stock}) cannot exceed the maximum stock limit ({$upperLimit}) for this product.";
        }
 
        // Validate discount conditions
        if (!$this->validateDiscountStock($stock, $minQty, $discount)) {
            return "Discounts can only be applied if stock is greater than or equal to minimum quantity.";
        }
 
        return null;
    }
 
    /**
     * Create a supply order if stock is below or equal to lower limit
     */
    private function createSupplyOrderIfNeeded(Product $product): void
    {
        if ($product->stock <= $product->stock_lower_limit) {
            // Verifica se já existe uma supply order pendente
            $existingOrder = \App\Models\SupplyOrder::where('product_id', $product->id)
                ->where('status', 'requested')
                ->exists();
 
            if (!$existingOrder) {
                $quantityToOrder = $product->stock_upper_limit - $product->stock;
 
                if ($quantityToOrder > 0) {
                    \App\Models\SupplyOrder::create([
                        'product_id' => $product->id,
                        'quantity' => $quantityToOrder,
                        'status' => 'requested',
                        'registered_by_user_id' => auth()->id()
                    ]);
                }
            }
        }
    }
}
 
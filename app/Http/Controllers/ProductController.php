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
    
    public function __construct()
    {
        // Only authorize for non-public routes
        // We'll handle authorization for index and show separately to allow public access
        $this->authorizeResource(Product::class, 'product', [
            'except' => ['index', 'show']
        ]);
    }
    
    public function index(Request $request): View
    {
        // Skip policy check for public access
        if (!auth()->check()) {
            // No need to authorize for guests
        } else {
            $this->authorize('viewAny', Product::class);
        }
    
        $filterByName = $request->get('name');
        $orderPrice = $request->get('order_price');
        $orderStock = $request->get('order_stock');
 
        // Only show trashed products to staff members and on non-public routes
        $userType = auth()->check() ? auth()->user()->type : 'member';
        $isPublicView = request('view') === 'public';
        $productQuery = Product::query()->with('category');
        
        // Nunca mostrar produtos soft-deleted na rota pública
        if (in_array($userType, ['employee', 'board']) && !$isPublicView) {
            $productQuery->withTrashed();
        }
 
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
 
        return view('products.index', compact('allProducts', 'orderPrice', 'orderStock', 'filterByName', 'userType'));
    }
 
    public function create(): View
    {
        $categories = Category::all();
        $product = new Product();
        
        // Prepare variables for the view
        $mode = 'create';
        $readonly = false;
        $isCreate = true;
        $isEdit = false;
        
        $userType = auth()->user()->type ?? 'guest';
        $canEditAll = !$readonly && $userType === 'board';
        $canEditStockOnly = !$readonly && $userType === 'employee';
        $forceReadonly = $readonly || (!$canEditAll && !$canEditStockOnly);
        $isEmployeeEditing = $userType === 'employee' && $mode === 'edit';
        
        return view('products.create', [
            'mode' => $mode,
            'product' => $product,
            'categories' => $categories,
            'readonly' => $readonly,
            'isCreate' => $isCreate,
            'isEdit' => $isEdit,
            'userType' => $userType,
            'canEditAll' => $canEditAll,
            'canEditStockOnly' => $canEditStockOnly,
            'forceReadonly' => $forceReadonly,
            'isEmployeeEditing' => $isEmployeeEditing
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
            ->with('success', 'Product created successfully.');
    }
 
    public function edit(Product $product): View
    {
        $categories = Category::all();
        $userType = auth()->check() ? auth()->user()->type : 'guest';
 
        $tr = new GoogleTranslate('en');
        $product->description_translated = $tr->translate($product->description);
        
        // Prepare variables for the view
        $mode = 'edit';
        $readonly = false;
        $isCreate = false;
        $isEdit = true;
        
        $canEditAll = !$readonly && $userType === 'board';
        $canEditStockOnly = !$readonly && $userType === 'employee';
        $forceReadonly = $readonly || (!$canEditAll && !$canEditStockOnly);
        $isEmployeeEditing = $userType === 'employee' && $mode === 'edit';
 
        return view('products.edit', [
            'mode' => $mode,
            'product' => $product,
            'categories' => $categories,
            'userType' => $userType,
            'readonly' => $readonly,
            'isCreate' => $isCreate,
            'isEdit' => $isEdit,
            'canEditAll' => $canEditAll,
            'canEditStockOnly' => $canEditStockOnly,
            'forceReadonly' => $forceReadonly,
            'isEmployeeEditing' => $isEmployeeEditing
        ]);
    }
 
    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);
        
        // For employees, only allow stock updates
        $userType = auth()->user()->type;
        if ($userType === 'employee') {
            $validated = $request->validate([
                'stock' => 'required|integer|min:0',
            ]);
            
            $oldStock = $product->stock;
            $newStock = $validated['stock'];
            
            try {
                // Use a transaction to ensure both operations succeed
                \DB::beginTransaction();
                
                // Update product stock
                $product->stock = $newStock;
                $product->save();
                
                // Record in stock_adjustments table
                $product->stockAdjustments()->create([
                    'registered_by_user_id' => auth()->id(),
                    'quantity_changed' => $newStock - $oldStock
                ]);
                
                \DB::commit();
                
                // Verifica se precisa criar supply order 
                $this->createSupplyOrderIfNeeded($product);
                
                return redirect()->route('products.index')
                    ->with('alert-type', 'success')
                    ->with('alert-msg', 'Stock updated successfully.');
                    
            } catch (\Exception $e) {
                \DB::rollBack();
                return back()
                    ->withInput()
                    ->with('alert-type', 'danger')
                    ->with('alert-msg', 'Error updating stock: ' . $e->getMessage());
            }
        }
        
        // Board user - full product update
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
        
        try {
            \DB::beginTransaction();
            
            // Check if stock is being updated
            $oldStock = $product->stock;
            $newStock = $validated['stock'] ?? $oldStock;
            
            // Update product
            $product->update($validated);                // If stock was changed, record it in stock_adjustments
            if ($oldStock != $newStock) {
                $product->stockAdjustments()->create([
                    'registered_by_user_id' => auth()->id(),
                    'quantity_changed' => $newStock - $oldStock
                ]);
            }
            
            \DB::commit();
            
            // Verifica se precisa criar supply order
            $this->createSupplyOrderIfNeeded($product);
            
            return redirect()->route('products.index')
                ->with('alert-type', 'success')
                ->with('alert-msg', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()
                ->withInput()
                ->with('alert-type', 'danger')
                ->with('alert-msg', 'Error updating product: ' . $e->getMessage());
        }
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
                $alertMsg = "Product {$product->name} has associated sales and has been moved to trash.";
            } else {
                // Se nunca foi vendido, tenta remover permanentemente (mesmo que tenha supply orders)
                try {
                    // Primeiro removemos qualquer ordem de fornecimento associada
                    \DB::table('supply_orders')->where('product_id', $product->id)->delete();
                    
                    // Removemos a foto associada ao produto
                    $this->deletePhoto($product, 'photo', 'products');
                    
                    // Agora podemos excluir permanentemente
                    $product->forceDelete();
                    
                    $alertType = 'success';
                    $alertMsg = "Product {$product->name} has been permanently deleted.";
                } catch (\Exception $e) {
                    // Se ocorrer algum erro na exclusão permanente, fazemos soft delete como fallback
                    $product->delete();
                    $alertType = 'warning';
                    $alertMsg = "Product {$product->name} could not be permanently deleted and was moved to trash. Details: " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $alertType = 'danger';
            $alertMsg = "Error while trying to delete product {$product->name}: " . $e->getMessage();
        }
 
        return back()
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
 
    public function showcase(): View
    {
        // Skip policy check for public access
        if (!auth()->check()) {
            // No need to authorize for guests
        } else {
            $this->authorize('viewShowCase', Product::class);
        }
        
        // Forward to the index view with public view parameter
        $products = Product::with('category')->orderBy('name')->paginate(15);
        return view('products.index', [
            'products' => $products,
            'view' => 'public'
        ]);
    }
 
    public function show(Product $product): View
    {
        // Skip policy check for public access
        if (!auth()->check()) {
            // No need to authorize for guests
        } else {
            $this->authorize('view', $product);
        }
        
        $categories = Category::all();
 
        $tr = new GoogleTranslate('en');
        $product->description_translated = $tr->translate($product->description);
        
        // Prepare variables for the view
        $mode = 'show';
        $readonly = true;
        $isCreate = false;
        $isEdit = false;
        
        $userType = auth()->user()->type ?? 'guest';
        $canEditAll = !$readonly && $userType === 'board';
        $canEditStockOnly = !$readonly && $userType === 'employee';
        $forceReadonly = $readonly || (!$canEditAll && !$canEditStockOnly);
        $isEmployeeEditing = false;
 
        return view('products.show', [
            'product' => $product,
            'categories' => $categories,
            'mode' => $mode,
            'readonly' => $readonly,
            'isCreate' => $isCreate,
            'isEdit' => $isEdit,
            'userType' => $userType,
            'canEditAll' => $canEditAll,
            'canEditStockOnly' => $canEditStockOnly,
            'forceReadonly' => $forceReadonly,
            'isEmployeeEditing' => $isEmployeeEditing
        ]);
    }
 
    public function restore(int $id): RedirectResponse
    {
        $product = Product::withTrashed()->findOrFail($id);
        
        try {
            $product->restore();
            $alertType = 'success';
            $alertMsg = "Product {$product->name} has been successfully restored.";
        } catch (\Exception $e) {
            $alertType = 'danger';
            $alertMsg = "Erro ao tentar restaurar o produto {$product->name}: " . $e->getMessage();
        }
 
        return back()
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
 
    public function forceDestroy(int $id): RedirectResponse
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            
            // Primeiro verificamos e removemos todas as relações com supply_orders
            \DB::table('supply_orders')->where('product_id', $id)->delete();
            
            // Removemos a foto associada ao produto
            $this->deletePhoto($product, 'photo', 'products');
            
            // Agora podemos excluir permanentemente
            $product->forceDelete();
            
            return redirect()->route('products.index')
                ->with('alert-type', 'success')
                ->with('alert-msg', "Product {$product->name} has been permanently deleted.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', "Error while trying to permanently delete the product: " . $e->getMessage());
        }
    }
 
    /**
     * This method is no longer needed as the stock update logic is in the main update method
     */
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
            // Check if a pending supply order already exists
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

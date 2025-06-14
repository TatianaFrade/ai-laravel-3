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
        
        $this->authorizeResource(Product::class, 'product', [
            'except' => ['index', 'show']
        ]);
    }
    
    public function index(Request $request): View
    {
       
        if (!auth()->check()) {
         
        } else {
            $this->authorize('viewAny', Product::class);
        }
    
        $filterByName = $request->get('name');
        $orderPrice = $request->get('order_price');
        $orderStock = $request->get('order_stock');
 
      
        $userType = auth()->check() ? auth()->user()->type : 'member';
        $isPublicView = request('view') === 'public';
        $productQuery = Product::query()->with('category');
        

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
      
        $userType = auth()->user()->type;
        if ($userType === 'employee') {
            $validated = $request->validate([
                'stock' => 'required|integer|min:0',
            ]);
            
            $oldStock = $product->stock;
            $newStock = $validated['stock'];
            
            try {
         
                \DB::beginTransaction();
                
           
                $product->stock = $newStock;
                $product->save();
                
            
                $product->stockAdjustments()->create([
                    'registered_by_user_id' => auth()->id(),
                    'quantity_changed' => $newStock - $oldStock
                ]);
                
                \DB::commit();
                
      
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
        

        $validated = $this->validateProduct($request, $product->id);
        
  
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
            
     
            $oldStock = $product->stock;
            $newStock = $validated['stock'] ?? $oldStock;
            
       
            $product->update($validated);                // If stock was changed, record it in stock_adjustments
            if ($oldStock != $newStock) {
                $product->stockAdjustments()->create([
                    'registered_by_user_id' => auth()->id(),
                    'quantity_changed' => $newStock - $oldStock
                ]);
            }
            
            \DB::commit();
            
       
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
          
            $hasSales = \DB::table('items_orders')
                ->where('product_id', $product->id)
                ->exists();
 
            if ($hasSales) {
             
                $product->delete();
                $alertType = 'success';
                $alertMsg = "Product {$product->name} has associated sales and has been moved to trash.";
            } else {
            
                try {
          
                    \DB::table('supply_orders')->where('product_id', $product->id)->delete();
                    
                
                    $this->deletePhoto($product, 'photo', 'products');
                    
                  
                    $product->forceDelete();
                    
                    $alertType = 'success';
                    $alertMsg = "Product {$product->name} has been permanently deleted.";
                } catch (\Exception $e) {
                  
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
       
        if (!auth()->check()) {
          
        } else {
            $this->authorize('viewShowCase', Product::class);
        }
        

        $products = Product::with('category')->orderBy('name')->paginate(15);
        return view('products.index', [
            'products' => $products,
            'view' => 'public'
        ]);
    }
 
    public function show(Product $product): View
    {
       
        if (!auth()->check()) {
            
        } else {
            $this->authorize('view', $product);
        }
        
        $categories = Category::all();
 
        $tr = new GoogleTranslate('en');
        $product->description_translated = $tr->translate($product->description);
        
   
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
            
     
            \DB::table('supply_orders')->where('product_id', $id)->delete();
            
     
            $this->deletePhoto($product, 'photo', 'products');
            
    
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
 

    private function validateStockAgainstLimit(int $stock, int $upperLimit): bool
    {
        return $stock <= $upperLimit;
    }
 

    private function validateDiscountStock(int $stock, ?int $minQty, ?float $discount): bool
    {
        if (!$discount) {
            return true;
        }
 
        return $stock >= ($minQty ?? 0);
    }
 

    private function validateProductData(array $data): ?string
    {
        $stock = (int)($data['stock'] ?? 0);
        $upperLimit = (int)($data['stock_upper_limit'] ?? 0);
        $minQty = (int)($data['discount_min_qty'] ?? 0);
        $discount = (float)($data['discount'] ?? 0);
 
    
        if (!$this->validateStockAgainstLimit($stock, $upperLimit)) {
            return "The stock ({$stock}) cannot exceed the maximum stock limit ({$upperLimit}) for this product.";
        }
 
 
        if (!$this->validateDiscountStock($stock, $minQty, $discount)) {
            return "Discounts can only be applied if stock is greater than or equal to minimum quantity.";
        }
 
        return null;
    }
 

    private function createSupplyOrderIfNeeded(Product $product): void
    {
        if ($product->stock <= $product->stock_lower_limit) {
         
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

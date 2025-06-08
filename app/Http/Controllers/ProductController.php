<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ProductFormRequest;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Category;
use App\Models\ShippingCost;
use App\Models\SupplyOrder;
use App\Models\StockAdjustment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\PhotoFileStorage;



class ProductController extends Controller
{
    use AuthorizesRequests;
    use PhotoFileStorage;

    // public function __construct() 
    // { 
    //     $this->authorizeResource(Product::class, 'product');
    // }


    public function index(Request $request): View
    {
        $filterByName = $request->input('name');
        $orderPrice = $request->input('order_price'); 
        $orderStock = $request->input('order_stock');

        $productQuery = Product::withTrashed()->withCount('category');

        if ($filterByName !== null) {
            $productQuery->where('name', 'LIKE', $filterByName . '%');
        }

        if (in_array($orderPrice, ['asc', 'desc'])) {
            $direction = $orderPrice;
            $productQuery->orderByRaw("CASE WHEN discount > 0 THEN price - discount ELSE price END {$direction}");
        }

        if (in_array($orderStock, ['asc', 'desc'])) {
            $productQuery->orderBy('stock', $orderStock);
        }

        $allProducts = $productQuery
            ->paginate(20)
            ->withQueryString();

        $tr = new GoogleTranslate('en');
        foreach ($allProducts as $product) {
            $product->description_translated = $tr->translate($product->description);
        }

        $userType = auth()->check() ? auth()->user()->type : 'guest';

        return view('products.index', compact('allProducts', 'orderPrice', 'orderStock', 'filterByName', 'userType'));
    }

    public function showCase(): View
    {
        return view('products.showcase');
    }

    public function show(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        $tr = new GoogleTranslate('en'); 
        $product->description_translated = $tr->translate($product->description);

        return view('products.show', [
            'product' => $product,
            'categories' => $categories,
            'mode' => 'show'
        ]);
    }

    
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create')->with('categories', $categories);
    }



    public function store(ProductFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($data['stock'] < ($data['discount_min_qty'] ?? 0)) {
            $data['discount'] = null;
        }

        if (!$request->filled('discount')) {
            $data['discount'] = null;
        }

        $product = new Product($data);
        $product->save(); 

        if ($request->hasFile('photo')) {
            $this->storePhoto($request->file('photo'), $product, 'photo', 'products');
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }



    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        $tr = new GoogleTranslate('en'); 
        $product->description_translated = $tr->translate($product->description);

        $userType = auth()->check() ? auth()->user()->type : 'guest';

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
            'mode' => 'edit',
            'userType' => $userType,
        ]);
    }


    public function update(ProductFormRequest $request, Product $product): RedirectResponse
    {
        $user = auth()->user();
        $userType = $user->type;

        $data = $request->validated();
        $oldStock = $product->stock;

        if ($data['stock'] < ($data['discount_min_qty'] ?? 0)) {
            $data['discount'] = null; // desativa desconto se estoque insuficiente
        }

        // Se o campo discount estiver vazio ou não enviado, definir como null
        
        if (!$request->filled('stock')) {
            $data['stock'] = null;
        }
        
        if (!$request->filled('discount')) {
            $data['discount'] = null;
        }

          if ($request->hasFile('photo')) {
                $this->deletePhoto($product, 'photo', 'products');
                $this->storePhoto($request->file('photo'), $product, 'photo', 'products');

                unset($data['photo']);
            }

            $product->update($data);


        

        $newStock = $product->stock;
        $stockChanged = $newStock - $oldStock;

        if ($stockChanged !== 0) {
            StockAdjustment::create([
                'product_id' => $product->id,
                'registered_by_user_id' => $user->id,
                'quantity_changed' => $stockChanged,
            ]);
        }

        if ($product->stock <= $product->stock_lower_limit) {
            $existingOrder = SupplyOrder::where('product_id', $product->id)
                ->where('status', 'requested')
                ->exists();

            if (!$existingOrder) {
                $quantityToOrder = $product->stock_upper_limit - $product->stock;

                if ($quantityToOrder > 0) {
                    SupplyOrder::create([
                        'product_id' => $product->id,
                        'quantity' => $quantityToOrder,
                        'status' => 'requested',
                        'registered_by_user_id' => $user->id,
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
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
                $alertMsg = "Product <strong>{$product->name}</strong> was sold before, so it was soft deleted.";
            } else {
                $this->deletePhoto($product, 'photo', 'products');

                $product->forceDelete();
                $alertType = 'success';
                $alertMsg = "Product <strong>{$product->name}</strong> deleted permanently.";
            }

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                $alertType = 'danger';
                $alertMsg = "O produto <strong>{$product->name}</strong> tem encomendas de reposição associadas e não pode ser eliminado permanentemente.";
            } else {
                $alertType = 'danger';
                $alertMsg = "Ocorreu um erro ao tentar eliminar o produto <strong>{$product->name}</strong>.";
            }
        } catch (\Exception $e) {
            $alertType = 'danger';
            $alertMsg = "Erro inesperado ao eliminar o produto <strong>{$product->name}</strong>: " . $e->getMessage();
        }

        return redirect()->route('products.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }


    public function forceDestroy(Product $product)
    {
        $this->deletePhoto($product, 'photo', 'products');

        $product->forceDelete();

        return redirect()->route('products.index')
            ->with('success', "Product <strong>{$product->name}</strong> deleted permanently.");
    }


}

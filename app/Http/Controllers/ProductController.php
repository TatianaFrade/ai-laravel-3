<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ProductFormRequest;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Category;
use App\Models\SupplyOrder;
use App\Models\StockAdjustment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class ProductController extends Controller
{
     use AuthorizesRequests;

    public function __construct() 
    { 
        $this->authorizeResource(Product::class, 'product');
    }


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
        return view('products.create', compact('categories'));
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

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/products'), $filename);
            $data['photo'] = $filename;
        }

        Product::create($data);

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
        $userType = auth()->user()->type;
        $data = $request->validated();

        $oldStock = $product->stock;

        if ($userType === "board") {

            if ($data['stock'] < ($data['discount_min_qty'] ?? 0)) {
                $data['discount'] = null;
            }

            if (!$request->filled('discount')) {
                $data['discount'] = null;
            }

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/products'), $filename);

                if ($product->photo && file_exists(public_path('storage/products/' . $product->photo))) {
                    unlink(public_path('storage/products/' . $product->photo));
                }

                $data['photo'] = $filename;
            } else {
                $data['photo'] = $product->photo;
            }

            $product->update($data);

        } elseif ($userType === 'employee') {
            $validatedStock = $request->validate([
                'stock' => 'required|integer|min:0',
            ]);

            $product->stock = $validatedStock['stock'];
            $product->save();

        } else {
            abort(403, 'Acesso não autorizado.');
        }

        $newStock = $product->stock;
        $stockChanged = $newStock - $oldStock;

        if ($stockChanged !== 0) {
            StockAdjustment::create([
                'product_id' => $product->id,
                'registered_by_user_id' => auth()->id(),
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
                        'registered_by_user_id' => auth()->id() ?? 1,
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully and supply order created if necessary.');
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

    public function forceDestroy()
    {
        return $this->forceDelete();
    }


}

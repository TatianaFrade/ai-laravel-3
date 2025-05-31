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


class ProductController extends Controller
{
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

        // Traduzir a descrição
        $tr = new GoogleTranslate('en'); // traduz para inglês
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

        // if ($data['stock'] < ($data['discount_min_qty'] ?? 0)) {
        //     $data['discount'] = null; // desativa desconto se estoque insuficiente
        // }

        // Se o campo discount estiver vazio ou não enviado, definir como null

        if (!$request->filled('stock')) {
            $data['stock'] = null;
        }

         // Se o campo discount estiver vazio ou não enviado, definir como null
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

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
            'mode' => 'edit'
        ]);

    }




    public function update(ProductFormRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        //if ($data['stock'] < ($data['discount_min_qty'] ?? 0)) {
            //$data['discount'] = null; // desativa desconto se estoque insuficiente
        //}

        // Se o campo discount estiver vazio ou não enviado, definir como null
        
        if (!$request->filled('stock')) {
            $data['stock'] = null;
        }
        
        if (!$request->filled('discount')) {
            $data['discount'] = null;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/products'), $filename);

            // Apaga foto antiga se existir
            if ($product->photo && file_exists(public_path('storage/products/' . $product->photo))) {
                unlink(public_path('storage/products/' . $product->photo));
            }

            $data['photo'] = $filename;
        } else {
            // Mantém a foto antiga
            $data['photo'] = $product->photo;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            
          
            $hasSales = \DB::table('items_orders')
                ->where('product_id', $product->id)
                ->exists();

            if ($hasSales) {
                // Soft delete
                $product->delete();
                $alertType = 'success';
                $alertMsg = "Product <strong>{$product->name}</strong> was sold before, so it was soft deleted.";
            } else {
                // Delete permanente
                $product->forceDelete();
                $alertType = 'success';
                $alertMsg = "Product <strong>{$product->name}</strong> deleted permanently.";
            }

        } catch (\Exception $e) {
            $alertType = 'danger';
            $alertMsg = "Error deleting product <strong>{$product->name}</strong>: " . $e->getMessage();
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

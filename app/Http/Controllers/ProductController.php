<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ProductFormRequest;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Category;


class ProductController extends Controller
{
    public function index(): View
    {
        $allProducts = Product::withTrashed()->paginate(15); // exemplo com paginação


        $tr = new GoogleTranslate('en'); // traduz para inglês


        foreach ($allProducts as $product) {
            $product->description_translated = $tr->translate($product->description);
        }


        return view('products.index')->with('allProducts', $allProducts);
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

        return redirect()->route('products.index')->with('success', 'Porduct updated successfully.');
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

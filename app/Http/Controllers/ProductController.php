<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ProductFormRequest;

class ProductController extends Controller
{
    public function index(): View
    {
        $allProducts = Product::paginate(20);
        return view('products.index')->with('allProducts', $allProducts);
    }

    public function showCase(): View
    {
        return view('products.showcase');
    }

    public function show(Product $product): View
    {
        return view('products.show')->with('product', $product);
    }

    public function create(): View
    {
        $newProduct = new Product();
        return view('products.create')->with('product', $newProduct);
    }

    public function store(ProductFormRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit')->with('product', $product);
    }

    public function update(ProductFormRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.index')
            ->with('success', "Product <strong>{$product->name}</strong> updated successfully.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $product->delete();
            $alertType = 'success';
            $alertMsg = "Product <strong>{$product->name}</strong> deleted successfully.";
        } catch (\Exception $e) {
            $alertType = 'danger';
            $alertMsg = "Error deleting product <strong>{$product->name}</strong>.";
        }

        return redirect()->route('products.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
}

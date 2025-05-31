<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CategoryFormRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 
use App\Traits\PhotoFileStorage;

class CategoryController extends Controller
{

    use AuthorizesRequests;
    use PhotoFileStorage;

    public function __construct() 
    { 
        $this->authorizeResource(Category::class, 'category');
    } 
    
    public function index(Request $request): View
    {
        $categoryQuery = Category::withTrashed()->withCount('products');

        $filterByName = $request->name;
        $orderName = $request->order;
        $orderProducts = $request->order_products;

        if ($filterByName !== null) {
            $categoryQuery->where('name', 'LIKE', $filterByName . '%');
        }

        // Ordenações
        if ($orderName === 'name_asc') {
            $categoryQuery->orderBy('name', 'asc');
        } elseif ($orderName === 'name_desc') {
            $categoryQuery->orderBy('name', 'desc');
        }

        if ($orderProducts === 'most') {
            $categoryQuery->orderBy('products_count', 'desc');
        } elseif ($orderProducts === 'least') {
            $categoryQuery->orderBy('products_count', 'asc');
        }

        $allCategories = $categoryQuery->paginate(20)->withQueryString();

        return view('categories.index', compact('allCategories', 'orderName', 'orderProducts', 'filterByName'));
    }



    public function show(Category $category): View
    {
        return view('categories.show', [
            'category' => $category,
            'mode' => 'show'
        ]);
    }


    public function create(): View
    {
        $category = new Category();
        return view('categories.create')->with('category', $category);
    }

    public function store(CategoryFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $category = new Category($data);
        $category->save();  

        if ($request->hasFile('image')) {
            $this->storePhoto($request->file('image'), $category, 'image', 'categories');
        }

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }



    public function edit(Category $category): View
    {
       
        return view('categories.edit')->with('category', $category);
    }

    public function update(CategoryFormRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

       if ($request->hasFile('image')) {
            $this->deletePhoto($category, 'image', 'categories');
            $this->storePhoto($request->file('image'), $category, 'image', 'categories');

            unset($data['image']);
        }

        $category->update($data);



        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }



    public function destroy(Category $category): RedirectResponse
    {
        try {
            $hasProducts = $category->products()->exists();

            if ($hasProducts) {
                $category->delete();

                $alertType = 'success';
                $alertMsg = "Category <strong>{$category->name}</strong> soft deleted successfully because it has linked products.";
            } else {
                $this->deletePhoto($category, 'image', 'categories');
                $category->forceDelete();

                $alertType = 'success';
                $alertMsg = "Category <strong>{$category->name}</strong> permanently deleted because it has no linked products.";
            }
        } catch (\Exception $e) {
            $alertType = 'danger';
            $alertMsg = "Error deleting Category <strong>{$category->name}</strong>: " . $e->getMessage();
        }

        return redirect()->route('categories.index')->with('alert-type', $alertType)->with('alert-msg', $alertMsg);
    }


    
    public function forceDestroy(Category $category)
    {
        if ($category->trashed()) {
            $this->deletePhoto($category, 'image', 'categories');
            $category->forceDelete();

            $url = route('categories.index');
            $htmlMessage = "Category <a href='$url'><strong>{$category->name}</strong></a> permanently deleted!";

            return redirect()->back()->with('alert-type', 'danger')->with('alert-msg', $htmlMessage);
        }

        return redirect()->back()->with('alert-type', 'warning')->with('alert-msg', 'Only deleted categories can be force deleted.');
    }


}

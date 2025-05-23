<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CategoryFormRequest;

class CategoryController extends Controller
{
    public function index(): View
    {
        $allCategories = Category::paginate(20);
        return view('categories.index')->with('allCategories', $allCategories);
    }

    public function show(Category $Category): View
    {
        return view('categories.show')->with('Category', $Category);
    }

    public function create(): View
    {
        $newCategory = new Category();
        return view('categories.create')->with('Category', $newCategory);
    }

    public function store(CategoryFormRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $Category): View
    {
        return view('categories.edit')->with('Category', $Category);
    }

    public function update(CategoryFormRequest $request, Category $Category): RedirectResponse
    {
        $Category->update($request->validated());

        return redirect()->route('categories.index')
            ->with('success', "Category <strong>{$Category->name}</strong> updated successfully.");
    }

    public function destroy(Category $Category): RedirectResponse
    {
        try {
            $Category->delete();
            $alertType = 'success';
            $alertMsg = "Category <strong>{$Category->name}</strong> deleted successfully.";
        } catch (\Exception $e) {
            $alertType = 'danger';
            $alertMsg = "Error deleting Category <strong>{$Category->name}</strong>.";
        }

        return redirect()->route('categories.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }

    
    public function forceDestroy($id)
    {
        $category = category::withTrashed()->findOrFail($id);
        $category->forceDelete();

        $url = route('categories.index', ['category' => $category]);

        $htmlMessage = "category <a href='$url'><strong>{$category->name}</strong></a> deleted successfully!";

        return redirect()->back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', $htmlMessage);
    }

}

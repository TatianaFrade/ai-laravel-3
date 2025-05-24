<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CategoryFormRequest;

class CategoryController extends Controller
{
  public function index(Request $request): View
    {
        $categoryQuery = Category::withTrashed()->withCount('products');

        $filterByName = $request->name;
        $order = $request->order;
        $orderProducts = $request->order_products;

        if ($filterByName !== null) {
            $categoryQuery->where('name', 'LIKE', $filterByName . '%');
        }

        // Ordenações
        if ($order === 'name_asc') {
            $categoryQuery->orderBy('name', 'asc');
        } elseif ($order === 'name_desc') {
            $categoryQuery->orderBy('name', 'desc');
        }

        if ($orderProducts === 'most') {
            $categoryQuery->orderBy('products_count', 'desc');
        } elseif ($orderProducts === 'least') {
            $categoryQuery->orderBy('products_count', 'asc');
        }

        $allCategories = $categoryQuery
            ->paginate(20)
            ->withQueryString();

        return view('categories.index', compact('allCategories', 'order', 'orderProducts', 'filterByName'));
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

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Pega o nome original ou cria um nome único
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move o arquivo para a pasta 'public/storage/categories'
            $file->move(public_path('storage/categories'), $filename);

            // Salva só o nome no array para inserir no banco
            $data['image'] = $filename;
        }

        Category::create($data);

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
            $file = $request->file('image');

            // Gera nome único para o ficheiro
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move para a pasta pública correta (igual ao store)
            $file->move(public_path('storage/categories'), $filename);

            // Se já havia imagem antiga, remove para não acumular
            if ($category->image && file_exists(public_path('storage/categories/' . $category->image))) {
                unlink(public_path('storage/categories/' . $category->image));
            }

            // Atualiza o nome do ficheiro na variável de dados
            $data['image'] = $filename;
        } else {
            // Se não enviou nova imagem, mantém o nome antigo para não apagar do banco
            $data['image'] = $category->image;
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }



    public function destroy(Category $category): RedirectResponse
    {
        try {
            // Verifica se a categoria tem produtos
            $hasProducts = $category->products()->exists();

            if ($hasProducts) {
                // Faz soft delete
                $category->delete();

                $alertType = 'success';
                $alertMsg = "Category <strong>{$category->name}</strong> soft deleted successfully because it has linked products.";
            } else {
                // Faz delete permanente
                $category->forceDelete();

                $alertType = 'success';
                $alertMsg = "Category <strong>{$category->name}</strong> permanently deleted because it has no linked products.";
            }
        } catch (\Exception $e) {
            $alertType = 'danger';
            $alertMsg = "Error deleting Category <strong>{$category->name}</strong>: " . $e->getMessage();
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

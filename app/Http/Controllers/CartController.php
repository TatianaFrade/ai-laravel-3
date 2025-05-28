<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\User;
use App\Http\Requests\CartConfirmationFormRequest;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function show(): View
    {
        $cart = session('cart', null);
        return view('cart.show', compact('cart'));
    }

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        $cart = session('cart', collect()); // Garante que o carrinho seja uma coleção

        // Verifica se o produto já existe no carrinho
        $existingProduct = $cart->firstWhere('id', $product->id);

        if ($existingProduct) {
            // Se já estiver no carrinho, aumenta a quantidade
            $existingProduct->quantity++;
        } else {
            // Caso contrário, adiciona o produto como um objeto e cria um atributo "quantity"
            $product->quantity = 1;
            $cart->push($product);
        }

        // Atualiza a sessão do carrinho
        $request->session()->put('cart', $cart);

        $alertType = 'success';
        $url = route('products.show', ['product' => $product]);
        $htmlMessage = "Product <a href='$url'>#{$product->id}
            <strong>\"{$product->name}\"</strong></a> foi adicionado ao carrinho.";

        return back()
            ->with('alert-msg', $htmlMessage)
            ->with('alert-type', $alertType);
    }

    public function increaseQuantity(Request $request, Product $product): RedirectResponse
    {
        $cart = session('cart', collect());

        $existingProduct = $cart->firstWhere('id', $product->id);

        if ($existingProduct) {
            $existingProduct->quantity++;
            $request->session()->put('cart', $cart);
        }

        return back()->with('alert-msg', "Quantidade de \"{$product->name}\" aumentada para {$existingProduct->quantity}.")
            ->with('alert-type', 'success');
    }

    public function decreaseQuantity(Request $request, Product $product): RedirectResponse
    {
        $cart = session('cart', collect());

        $existingProduct = $cart->firstWhere('id', $product->id);

        if ($existingProduct && $existingProduct->quantity > 1) {
            $existingProduct->quantity--;
            $request->session()->put('cart', $cart);
        } elseif ($existingProduct) {
            // Se a quantidade for 1, remove o produto do carrinho
            $cart = $cart->reject(fn($item) => $item->id === $product->id);
            $request->session()->put('cart', $cart);
        }

        return back()->with('alert-msg', "Quantidade de \"{$product->name}\" diminuída para " . (isset($existingProduct->quantity) ? $existingProduct->quantity : 0) . ".")
            ->with('alert-type', 'warning');
    }

    public function removeFromCart(Request $request, Product $product): RedirectResponse
    {
        $url = route('products.show', ['product' => $product]);
        $cart = session('cart', collect());
        if ($cart->isEmpty()) {
            $alertType = 'warning';
            $htmlMessage = "Product <a href='$url'>#{$product->id}</a>
                <strong>\"{$product->name}\"</strong> was not removed from the cart 
                because cart is empty!";
            return back()
                ->with('alert-msg', $htmlMessage)
                ->with('alert-type', $alertType);
        } else {
            $element = $cart->firstWhere('id', $product->id);
            if ($element) {
                $cart = $cart->reject(fn($item) => $item->id === $product->id);
                if ($cart->count() == 0) {
                    $request->session()->forget('cart');
                } else {
                    $request->session()->put('cart', $cart);
                }
                $alertType = 'success';
                $htmlMessage = "Product <a href='$url'>#{$product->id}</a>
                <strong>\"{$product->name}\"</strong> was removed from the cart.";
                return back()
                    ->with('alert-msg', $htmlMessage)
                    ->with('alert-type', $alertType);
            } else {
                $alertType = 'warning';
                $htmlMessage = "Product <a href='$url'>#{$product->id}</a>
                <strong>\"{$product->name}\"</strong> was not removed from the cart 
                because cart does not include it!";
                return back()
                    ->with('alert-msg', $htmlMessage)
                    ->with('alert-type', $alertType);
            }
        }
    }


    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');
        return back()
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Shopping Cart has been cleared');
    }


    public function confirm(CartConfirmationFormRequest $request): RedirectResponse
    {
        $cart = session('cart', null);
        if (!$cart || ($cart->count() == 0)) {
            return back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', "Cart was not confirmed, because cart is empty!");
        } else {
            $user = User::where('number', $request->validated()['student_number'])->first();
            if (!$user) {
                return back()
                    ->with('alert-type', 'danger')
                    ->with('alert-msg', "User number does not exist on the database!");
            }
            $insertProducts = [];
            $productsOfUser = $user->products;
            $ignored = 0;
            foreach ($cart as $product) {
                $exist = $productsOfUser->where('id', $product->id)->count();
                if ($exist) {
                    $ignored++;
                } else {
                    $insertProducts[$product->id] = [
                        "product_id" => $product->id,
                        "repeating" => 0,
                        "grade" => null,
                    ];
                }
            }
            $ignoredStr = match ($ignored) {
                0 => "",
                1 => "<br>(1 product was ignored because user already has it)",
                default => "<br>($ignored products were ignored because user already has them)"
            };
            $totalInserted = count($insertProducts);
            $totalInsertedStr = match ($totalInserted) {
                0 => "",
                1 => "1 product was added to the user",
                default => "$totalInserted products were added to the user",
            };
            if ($totalInserted == 0) {
                $request->session()->forget('cart');
                return back()
                    ->with('alert-type', 'danger')
                    ->with('alert-msg', "No product was added to the user!$ignoredStr");
            } else {
                DB::transaction(function () use ($user, $insertProducts) {
                    $user->products()->attach($insertProducts);
                });
                $request->session()->forget('cart');
                if ($ignored == 0) {
                    return redirect()->route('users.show', ['user' => $user])
                        ->with('alert-type', 'success')
                        ->with('alert-msg', "$totalInsertedStr.");
                } else {
                    return redirect()->route('users.show', ['user' => $user])
                        ->with('alert-type', 'warning')
                        ->with('alert-msg', "$totalInsertedStr. $ignoredStr");
                }
            }
        }
    }
}

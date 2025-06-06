<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\User;
use App\Models\ShippingCost;
use App\Http\Requests\CartConfirmationFormRequest;
use Illuminate\Support\Facades\DB;
use App\Models\ItemOrder;
use App\Models\Order;

class CartController extends Controller
{
    public function show(): View
    {
        $cart = session('cart', null);

        $shippingCosts = ShippingCost::all();

        return view('cart.show', compact('cart', 'shippingCosts'));
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
        //dd("A função confirm() foi chamada!");



        $cart = session('cart', null);
        if (!$cart || $cart->isEmpty()) {
            return back()->with('alert-type', 'danger')->with('alert-msg', "O carrinho está vazio!");
        }
        //dd($cart);
        // Buscar utilizador e verificar se é membro do clube
        $user = User::where('nif', $request->validated()['nif'])->first();
        if (!$user || !$user->isRegular()) {
            return $user ? back()->with('alert-type', 'danger')->with('alert-msg', "Apenas membros do clube podem fazer compras.")
                : redirect()->route('login')->with('alert-msg', "Precisas de iniciar sessão para confirmar a compra.");
        }
        //dd($user);

        $virtualCard = Card::where('id', $user->id)->first();

        if (!$virtualCard) {
            return back()->with('alert-type', 'danger')->with('alert-msg', "Cartão virtual não encontrado para este utilizador.");
        }

        $cardBalance = $virtualCard->balance;
        //dd($cardBalance);
        // Calcular o total dos itens com desconto aplicado
        $totalItems = $cart->sum(fn($product) => ($product->price - ($product->discount ?? 0)) * $product->quantity);

        $totalCartItems = $cart->sum(fn($product) => $product->quantity);

        // Obter o custo de envio correto da tabela com base no total da compra
        $shippingCosts = ShippingCost::where('min_value_threshold', '<=', $totalItems)
            ->where('max_value_threshold', '>=', $totalItems)
            ->value('shipping_cost') ?? 0.00; // Definir como 0 se não houver correspondência
        //dd($shippingCosts);
        // Calcular total do pedido
        $totalOrder = $totalItems + $shippingCosts;

        if ($cardBalance < $totalOrder) {
            return back()->with('alert-type', 'danger')->with('alert-msg', "Saldo insuficiente no cartão virtual.");
        }

        // Criar o pedido
        DB::transaction(function () use ($user, $cart, $totalCartItems, $shippingCosts, $totalOrder, $virtualCard) {
            $order = Order::create([
                'member_id' => $user->id,
                'status' => 'pending',
                'date' => now()->toDateString(),
                'total_items' => $totalCartItems,
                'shipping_cost' => $shippingCosts,
                'total' => $totalOrder,
                'nif' => $user->nif,
                'delivery_address' => $user->default_delivery_address,
            ]);

            foreach ($cart as $product) {
                ItemOrder::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $product->quantity,
                    'unit_price' => $product->price,
                    'discount' => $product->discount ?? 0,
                    'subtotal' => ($product->price - ($product->discount ?? 0)) * $product->quantity,
                ]);
            }

            // Debitar saldo do cartão virtual
            $virtualCard->balance -= $totalOrder;
            $virtualCard->save();
        });

        // Limpar carrinho e alertar sobre status do pedido
        $request->session()->forget('cart');
        return redirect()->route('products.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', "O pedido foi criado e está a ser preparado!");
    }
}

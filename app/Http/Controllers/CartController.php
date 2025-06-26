<?php
 
namespace App\Http\Controllers;
 
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\User;
use App\Models\Operation;
use App\Models\ShippingCost;
use App\Http\Requests\CartConfirmationFormRequest;
use Illuminate\Support\Facades\DB;
use App\Models\ItemOrder;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\MembershipExpiredMail;
 
class CartController extends Controller
{
    public function show(): View
    {
        $cart = session('cart', null);
        $shippingCosts = ShippingCost::all();

        $cartTotals = null;
        if ($cart && !$cart->isEmpty()) {
            $totalProductPrice = $cart->sum(function ($product) {
                return $product->cart_total;
            });

            $shippingCost = ShippingCost::getShippingCostForTotal($totalProductPrice);

            $user = auth()->user();
            $card = $user->card; // Relação hasOne
            $balance = $card ? $card->balance : 0.0;

            $cartTotals = [
                'totalProductPrice' => $totalProductPrice,
                'shippingCost' => $shippingCost,
                'finalTotal' => $totalProductPrice + $shippingCost,
                'balance' => $balance,
            ];
        }

        $userType = auth()->user()->type ?? 'guest';

        return view('cart.show', compact('cart', 'shippingCosts', 'cartTotals', 'userType'));
    }

 
    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        $cart = session('cart', collect());

        // ⚠️ Substituir maçã por banana 
         if ($product->name === 'Apple') {
             $banana = Product::where('name', 'Banana')->first();
             if ($banana) {
                 $product = $banana;
             }
        }



       


        //so pode adicionar 3 tipos diferentes de produtos ao carrinho por order

        // $quantityProducts =  $cart->count();

        // if($quantityProducts >= 3){
        //     $htmlMessage = " The cart is full";
        //     return back()
        //     ->with('alert-msg', $htmlMessage);
        // }

     



        // nao permite que um produto com stock 0 seja adicionado ao carrinho

        // if($product->stock <= 0){
        //     $htmlMessage = "Product 
        //     <strong>\"{$product->name}\"</strong></a> cannot been added to the cart because it is out of stock.";

        //     return back()
        //     ->with('alert-msg', $htmlMessage);
        // }

        $existingProduct = $cart->firstWhere('id', $product->id);

        if ($existingProduct) {
            $existingProduct->quantity++;
        } else {
            $product->quantity = 1;
            $cart->push($product);
        }

        $request->session()->put('cart', $cart);

        $alertType = 'success';
        $url = route('products.show', ['product' => $product]);


        $htmlMessage = "Product <a href='$url'>#{$product->id}
            <strong>\"{$product->name}\"</strong></a> has been added to the cart.";



        //se adicionar um produto com disconto ao carrinho mostra "Aproveitou um desconto neste produto"

        // if ($product->discount > 0) {
        //     $htmlMessage = "Aproveitou um desconto neste produto!";

        //     return back()
        //         ->with('alert-msg', $htmlMessage);
        // }else{
        //     $htmlMessage = "Product <a href='$url'>#{$product->id}
        //     <strong>\"{$product->name}\"</strong></a> has been added to the cart.";
        // }


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
 
        return back()->with('alert-msg', "Quantity of \"{$product->name}\" increased to {$existingProduct->quantity}.")
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
            $cart = $cart->reject(fn($item) => $item->id === $product->id);
            $request->session()->put('cart', $cart);
        }
 
        return back()->with('alert-msg', "Quantity of \"{$product->name}\" decreased to " . ($existingProduct->quantity ?? 0) . ".")
            ->with('alert-type', 'warning');
    }
 
    public function removeFromCart(Request $request, Product $product): RedirectResponse
    {
        $url = route('products.show', ['product' => $product]);
        $cart = session('cart', collect());
 
        if ($cart->isEmpty()) {
            return back()
                ->with('alert-msg', "Product <a href='$url'>#{$product->id}</a>
                    <strong>\"{$product->name}\"</strong> was not removed from the cart
                    because cart is empty!")
                ->with('alert-type', 'warning');
        }
 
        $element = $cart->firstWhere('id', $product->id);
 
        if ($element) {
            $cart = $cart->reject(fn($item) => $item->id === $product->id);
            $cart->isEmpty()
                ? $request->session()->forget('cart')
                : $request->session()->put('cart', $cart);
 
            return back()
                ->with('alert-msg', "Product <a href='$url'>#{$product->id}</a>
                    <strong>\"{$product->name}\"</strong> was removed from the cart.")
                ->with('alert-type', 'success');
        }
 
        return back()
            ->with('alert-msg', "Product <a href='$url'>#{$product->id}</a>
                <strong>\"{$product->name}\"</strong> was not removed from the cart
                because cart does not include it!")
            ->with('alert-type', 'warning');
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
        if (!$cart || $cart->isEmpty()) {
            return back()->with('alert-type', 'danger')->with('alert-msg', "The cart is empty!");
        }
 
        $user = auth()->user();
 
        if (!$user) {
            return redirect()->route('login')->with('alert-msg', "You need to login to confirm your purchase.");
        }
 
        $virtualCard = Card::where('id', $user->id)->first();
 
        if (!$virtualCard) {
            return redirect()->route('card.create')
                ->with('alert-type', 'info')
                ->with('alert-msg', "Please create a virtual card to proceed with your purchase.")
                ->with('redirect_after', 'cart.confirm');
        }

        // For all user types, check if they've ever paid a membership fee
        if (!$user->hasPaidMembership()) {
            return redirect()->route('membershipfees.index')
                ->with('alert-type', 'danger')
                ->with('alert-msg', 'You need to pay the membership fee before making any purchases.');
        }
        
        // For regular members, also check if membership is expired
        if ($user->type === 'member' && $user->isMembershipExpired()) {
            Mail::to($user->email)->send(new MembershipExpiredMail($user));
            return redirect()->route('membershipfees.index')
                ->with('alert-type', 'danger')
                ->with('alert-msg', 'Your membership has expired. Please renew it before making any purchases.');
        }
 
        $cardBalance = $virtualCard->balance;
        
        // Calculate totals using model methods
        $totalItems = $cart->sum(function($product) {
            return $product->cart_total;
        });

        //se o total do carrinho ultrapassar 100, 10% de desconto
        // if($totalItems > 100){
        //     $totalItems -= $totalItems*0.10;
        // }
        
        $shippingCosts = ShippingCost::getShippingCostForTotal($totalItems);
        $totalOrder = $totalItems + $shippingCosts;
        
        $totalCartItems = $cart->sum(fn($product) => $product->quantity);
 
        if ($cardBalance < $totalOrder) {
            return back()->with('alert-type', 'danger')->with('alert-msg', "Insufficient balance in your virtual card.");
        }
 
        DB::transaction(function () use ($user, $cart, $totalItems, $totalCartItems, $shippingCosts, $totalOrder, $virtualCard, $request) {
            if ($request->filled('default_delivery_address')) {
                $user->update([
                    'default_delivery_address' => $request->default_delivery_address
                ]);
            }
 
            // Create order with precise total
            $order = Order::create([
                'member_id' => $user->id,
                'status' => 'pending',
                'date' => now()->toDateString(),
                'total_items' => round($totalItems, 2), // Total cost of items, not count
                'shipping_cost' => $shippingCosts,
                'total' => $totalOrder, // This is already rounded to 2 decimal places
                'nif' => $user->nif,
                'delivery_address' => $request->default_delivery_address,
            ]);
 
            foreach ($cart as $product) {
                // Calculate subtotal with precise rounding
                $unitPriceAfterDiscount = $product->price - ($product->discount ?? 0);
                $subtotal = round($unitPriceAfterDiscount * $product->quantity, 2);
                
                ItemOrder::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $product->quantity,
                    'unit_price' => $product->price,
                    'discount' => $product->discount ?? 0,
                    'subtotal' => $subtotal,
                ]);
            }
 
            $card = Card::where('id', $virtualCard->id)->lockForUpdate()->first();
            $card->balance -= $totalOrder;
            $card->save();
 
            Operation::create([
                'card_id' => $card->id,
                'type' => 'debit',
                'value' => $totalOrder,
                'date' => now()->format('Y-m-d'),
                'debit_type' => 'order',
                'order_id' => $order->id,
            ]);
        });
 
        $request->session()->forget('cart');
 
        return redirect()->route('products.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', "Your order has been created and is being prepared!");
    }
}
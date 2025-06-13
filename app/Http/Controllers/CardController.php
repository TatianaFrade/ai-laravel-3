<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Services\Payment;
use App\Http\Requests\CardFormRequest;
use App\Models\Operation;
use App\Models\MembershipFee;
use App\Mail\MembershipExpiredOnPurchaseMail;
use Illuminate\Support\Facades\Mail;


class CardController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $card = $user->card;

 
        if ($card) {
            $this->authorize('view', $card);
        }


        return view('card.show', compact('card'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->card) {
            return redirect()->route('card.show');
        }
        
        $this->authorize('create', Card::class);
        
        return view('card.create');
    }

    public function update(CardFormRequest $request)
    {
        $data = $request->validated();
        
        $card = Card::findOrFail(auth()->id());
        $this->authorize('update', $card);
        
        $payment = new Payment(); 

        switch ($data['type']) {
            case 'Visa':
                if (!$payment->payWithVisa($data['card_num'], $data['cvc'])) {
                    return redirect()->back()->withErrors('Error processing Visa payment.');
                }
                break;
            case 'PayPal':
                if (!$payment->payWithPaypal($data['email'])) {
                    return redirect()->back()->withErrors('Error processing PayPal payment.');
                }
                break;
            case 'MB WAY':
                if (!$payment->payWithMBway($data['phone_number'])) {
                    return redirect()->back()->withErrors('Error processing MB WAY payment.');
                }
                break;
            default:
                abort(400, 'Invalid payment method.');
        }

        $card = Card::findOrFail(auth()->id());
        
        // Record credit operation
        $card->operations()->create([
            'card_id' => $card->id,
            'type' => 'credit',
            'value' => $data['amount'],
            'date' => now()->format('Y-m-d'),
            'debit_type' => null,
            'credit_type' => 'payment',
            'payment_type' => $data['type'],
            'payment_reference' => $data['email'] ?? $data['phone_number'] ?? $data['card_num'] ?? null,
            'order_id' => null,
        ]);

        $card->balance += $data['amount'];
        $card->save();

        $membershipFee = MembershipFee::latest()->value('membership_fee');

        // Verify if membership fee has been paid
        $hasMembershipFee = $card->operations()
            ->where('debit_type', 'membership_fee')
            ->exists();
    
        $user = auth()->user();
        // All types of users need to pay the membership fee at least once
        if (!$hasMembershipFee && $card->balance >= $membershipFee) {
            // Deduct membership fee automatically if sufficient balance
            $card->balance -= $membershipFee;
            $card->operations()->create([
                'card_id' => $card->id,
                'type' => 'debit',
                'value' => $membershipFee,
                'date' => now()->format('Y-m-d'),
                'debit_type' => 'membership_fee',
                'credit_type' => null,
                'payment_type' => null,
                'payment_reference' => null,
                'order_id' => null,
            ]);
            $card->save();

            $alertType = 'success';
            $htmlMessage = "Balance updated and membership fee paid successfully!";
            return redirect()->back()
                ->with('alert-msg', $htmlMessage)
                ->with('alert-type', $alertType);
        } else if (!$hasMembershipFee && $card->balance < $membershipFee) {
            $amountNeeded = $membershipFee - $card->balance;
            
            $alertType = 'warning';
            $htmlMessage = "Balance updated, but insufficient to pay the membership fee. To pay the membership fee, please add " . number_format($amountNeeded, 2, '.', ',') . "€ to your card.";
            return redirect()->back()
                ->with('alert-msg', $htmlMessage)
                ->with('alert-type', $alertType);
        }

        $card->save();
        $alertType = 'success';
        $htmlMessage = "Card balance updated successfully!";

        // Check if there was a redirect_after in session
        if (session()->has('redirect_after')) {
            $route = session()->get('redirect_after');
            session()->forget('redirect_after');
            return redirect()->route($route)
                ->with('alert-msg', $htmlMessage)
                ->with('alert-type', $alertType);
        }

        return redirect()->back()
                ->with('alert-msg', $htmlMessage)
                ->with('alert-type', $alertType);
    }

    public function store(CardFormRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();
        
        if ($user->card) {
            return redirect()->route('card.show');
        }
        
        $this->authorize('create', Card::class);

        $payment = new Payment();

        // Validate payment method
        switch ($data['type']) {
            case 'Visa':
                if (!$payment->payWithVisa($data['card_num'], $data['cvc'])) {
                    return redirect()->back()->withErrors('Error processing Visa payment.');
                }
                break;
            case 'PayPal':
                if (!$payment->payWithPaypal($data['email'])) {
                    return redirect()->back()->withErrors('Error processing PayPal payment.');
                }
                break;
            case 'MB WAY':
                if (!$payment->payWithMBway($data['phone_number'])) {
                    return redirect()->back()->withErrors('Error processing MB WAY payment.');
                }
                break;
            default:
                abort(400, 'Invalid payment method.');
        }

        // Create the card
        $card = Card::createForUser($user);
        
        // Create initial balance operation if amount provided
        if (isset($data['amount']) && $data['amount'] > 0) {
            $card->operations()->create([
                'card_id' => $card->id,
                'type' => 'credit',
                'value' => $data['amount'],
                'date' => now()->format('Y-m-d'),
                'debit_type' => null,
                'credit_type' => 'payment',
                'payment_type' => $data['type'],
                'payment_reference' => $data['email'] ?? $data['phone_number'] ?? $data['card_num'] ?? null,
                'order_id' => null,
            ]);

            $card->balance = $data['amount'];
            $card->save();
            
            // Check and process membership fee for all new users
            $membershipFee = MembershipFee::latest()->value('membership_fee');
            
            if ($card->balance >= $membershipFee) {
                // Deduct membership fee
                $card->balance -= $membershipFee;
                $card->operations()->create([
                    'card_id' => $card->id,
                    'type' => 'debit',
                    'value' => $membershipFee,
                    'date' => now()->format('Y-m-d'),
                    'debit_type' => 'membership_fee',
                    'credit_type' => null,
                    'payment_type' => null,
                    'payment_reference' => null,
                    'order_id' => null,
                ]);
                $card->save();
                
                $message = "Virtual card created successfully! Note: Your membership fee (€" . number_format($membershipFee, 2) . ") was automatically deducted from your initial balance. Current balance: €" . number_format($card->balance, 2);
                $redirectRoute = session('redirect_after') ?: 'card.show';
            } else {
                // If balance is insufficient for membership fee, redirect to membership fee payment
                $amountNeeded = $membershipFee - $card->balance;
                $message = "Virtual card created but requires additional €" . number_format($amountNeeded, 2, '.', ',') . " to pay membership fee.";
                $redirectRoute = 'membershipfees.index';
            }
        } else {
            // No initial balance - all users need to pay membership fee
            $message = 'Virtual card created successfully. Please add funds to pay the membership fee.';
            $redirectRoute = 'membershipfees.index';
        }

        return redirect()->route($redirectRoute)
            ->with('alert-type', 'success')
            ->with('alert-msg', $message);
    }
}

// $alertType = 'success';
//         $url = route('products.show', ['product' => $product]);
//         $htmlMessage = "Product <a href='$url'>#{$product->id}
//             <strong>\"{$product->name}\"</strong></a> has been added to cart.";

//         return back()
//             ->with('alert-msg', $htmlMessage)
//             ->with('alert-type', $alertType);
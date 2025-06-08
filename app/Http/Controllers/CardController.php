<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Services\Payment;
use App\Http\Requests\CardFormRequest;
use App\Models\Operation;
use App\Models\MembershipFee;

class CardController extends Controller
{
    public function show($id)
    {
        $card = Card::findOrFail($id); // Busca apenas o cartão, sem carregar operações
        return view('operations.card', compact('card')); // Usa 'cards' como prefixo da subpasta
    }

    public function create()
    {
        return view('card.create'); // Exibe a página de criação
    }

    public function update(CardFormRequest $request)
    {
        $data = $request->validated();

        $payment = new Payment(); //falar com o stor

        switch ($data['type']) {
            case 'Visa':
            if (!$payment->payWithVisa($data['card_num'], $data['cvc'])) {
                return redirect()->back()->withErrors('Erro ao processar pagamento com Visa.');
            }
            break;
            case 'PayPal':
            if (!$payment->payWithPaypal($data['email'])) {
                return redirect()->back()->withErrors('Erro ao processar pagamento com PayPal.');
            }
            break;
            case 'MB WAY':
            if (!$payment->payWithMBway($data['phone_number'])) {
                return redirect()->back()->withErrors('Erro ao processar pagamento com MB WAY.');
            }
            break;
            default:
            abort(400, 'Método de pagamento inválido.');
        }

        // Registra a operação após atualizar o saldo -- continuar aqui
        
        $card = Card::findOrFail(auth()->id());
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

        // Verifica se já existe uma operação de débito de mensalidade
        $hasMembershipFee = $card->operations()
            ->where('debit_type', 'membership_fee')
            ->exists();
    
        
        if (!$hasMembershipFee && $card->balance >= $membershipFee) {
            // Debita a mensalidade
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
            $htmlMessage = "Saldo atualizado e mensalidade paga com sucesso!";
            return redirect()->back()
                ->with('alert-msg', $htmlMessage)
                ->with('alert-type', $alertType);
        } else if (!$hasMembershipFee && $card->balance < $membershipFee) {
            $amountNeeded = $membershipFee - $card->balance;
            
            $alertType = 'warning';
            $htmlMessage = "Saldo atualizado, mas insuficiente para pagar a mensalidade. Para pagar a mensalidade, adicione mais " . number_format($amountNeeded, 2, ',', '.') . "€ ao cartão.";
            return redirect()->back()
                ->with('alert-msg', $htmlMessage)
                ->with('alert-type', $alertType);
        }
        $card->save();

        $alertType = 'success';
        $htmlMessage = "Saldo do cartão atualizado com sucesso!";
        return redirect()->back()
            ->with('alert-msg', $htmlMessage)
            ->with('alert-type', $alertType);
    }
}

// $alertType = 'success';
//         $url = route('products.show', ['product' => $product]);
//         $htmlMessage = "Product <a href='$url'>#{$product->id}
//             <strong>\"{$product->name}\"</strong></a> foi adicionado ao carrinho.";

//         return back()
//             ->with('alert-msg', $htmlMessage)
//             ->with('alert-type', $alertType);
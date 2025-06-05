<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Services\Payment;
use App\Http\Requests\CardFormRequest;
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

        $this->updateBalance(auth()->id(), $data['amount']); // Atualiza o saldo do cartão

        // Registra a operação após atualizar o saldo -- continuar aqui

        $card = Card::findOrFail(auth()->id());
        $card->operations()->create([
            'card_id' => $card->id,
            'type' => 'deposit',
            'value' => $data['amount'],
            'date' => now(),
            'debit_type' => null,
            'credit_type' => null,
            'payment_type' => $data['type'],
            'payment_reference' => null,
            'order_id' => null,
        ]);

        return redirect()->back()->with('success', 'Saldo atualizado com sucesso!');
    }

    protected function updateBalance($id, $amount)
    {
        $card = Card::findOrFail($id);
        $card->balance += $amount;
        $card->save();
    }

}

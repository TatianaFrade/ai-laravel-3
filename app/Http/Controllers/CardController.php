<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Services\Payment;
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

    public function update(Request $request)
    {
        $request->validate([
            //'cardNum' => 'required|numeric|not_regex:/^0/', // 16 dígitos, não pode começar com 0
            //'cvc' => 'required|digits:3|numeric|not_regex:/^0/', // 3 dígitos, não pode começar com 0
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:Visa,PayPal,MB WAY',
        ]);

        // $payment = new Payment(); //falar com o stor

        // switch ($request->input('type')) {
        //     case 'Visa':
        //     if (!$payment->payWithVisa($request->input('amount'))) {
        //         return redirect()->back()->withErrors('Erro ao processar pagamento com Visa.');
        //     }
        //     break;
        //     case 'PayPal':
        //     if (!$payment->payWithPaypal($request->input('amount'))) {
        //         return redirect()->back()->withErrors('Erro ao processar pagamento com PayPal.');
        //     }
        //     break;
        //     case 'MB WAY':
        //     if (!$payment->payWithMBway($request->input('amount'))) {
        //         return redirect()->back()->withErrors('Erro ao processar pagamento com MB WAY.');
        //     }
        //     break;
        //     default:
        //     abort(400, 'Método de pagamento inválido.');
        // }

        $this->updateBalance(auth()->id(), $request->input('amount'));

        return redirect()->back()->with('success', 'Saldo atualizado com sucesso!');
    }

    protected function updateBalance($id, $amount)
    {
        $card = Card::findOrFail($id);
        $card->balance += $amount;
        $card->save();
    }

}

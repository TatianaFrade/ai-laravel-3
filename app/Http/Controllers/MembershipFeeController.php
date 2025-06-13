<?php

namespace App\Http\Controllers;

use App\Models\MembershipFee;
use App\Http\Requests\MembershipFeeFormRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class MembershipFeeController extends Controller
{
    use AuthorizesRequests;

    public function __construct() 
    { 
        $this->authorizeResource(MembershipFee::class, 'membershipfee');
    } 

    
    public function index()
    {
        $membershipfee = MembershipFee::first(); // ou ->latest()->first()

        return view('membershipfees.index', [
            'membershipfee' => $membershipfee,
            'isPublicView' => request()->boolean('view'), // <- isto define se está na vista pública
        ]);
    }


    public function edit(MembershipFee $membershipfee)
    {
        return view('membershipfees.edit')->with('membershipfee', $membershipfee);
    }


    public function update(MembershipFeeFormRequest $request, MembershipFee $membershipfee)
    {
        $membershipfee->update($request->validated());

        return redirect()->route('membershipfees.index')->with('success', 'Membership fee updated successfully.');
    }

    public function pay(MembershipFee $membershipfee)
    {
        $this->authorize('pay', $membershipfee);

        $user = Auth::user();
        $feeValue = $membershipfee->membership_fee ?? 0;

        $card = Card::where('id', $user->id)->first();

        if (!$card) {
            return redirect()->back()->withErrors(['card' => 'Cartão não encontrado.']);
        }

        if ($card->balance < $feeValue) {
            return redirect()->back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', 'Saldo insuficiente no cartão para pagar a taxa de adesão. Saldo atual: €' . number_format($card->balance, 2) . '. Valor necessário: €' . number_format($feeValue, 2));
        }

        DB::transaction(function () use ($card, $feeValue, $user) {
            // Atualiza saldo
            $card->balance -= $feeValue;
            $card->save();

            // Regista operação de débito
            \App\Models\Operation::create([
                'card_id' => $card->id,
                'type' => 'debit',
                'value' => $feeValue,
                'date' => now()->toDateString(),
                'debit_type' => 'membership_fee',
            ]);

            // Desbloqueia o utilizador
            $user->blocked = 0;
            $user->save();
        });

        return redirect()->back()->with('success', 'Taxa de adesão paga com sucesso. A sua conta foi desbloqueada.');
    }


}

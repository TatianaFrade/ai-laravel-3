<?php

namespace App\Http\Controllers;

use App\Models\MembershipFee;
use App\Http\Requests\MembershipFeeFormRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;

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
            return redirect()->back()->withErrors(['card' => 'Saldo insuficiente para pagar a taxa de adesão.']);
        }

        $card->balance -= $feeValue;
        $card->save();

        $user->blocked = 0;
        $user->save();

        return redirect()->back()->with('success', 'Taxa de adesão paga com sucesso. A sua conta foi desbloqueada.');
    }

}

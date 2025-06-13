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
        $membershipfee = MembershipFee::first(); // or ->latest()->first()

        return view('membershipfees.index', [
            'membershipfee' => $membershipfee,
            'isPublicView' => request()->boolean('view'), // <- this defines if it's in the public view
        ]);
    }


    public function edit(MembershipFee $membershipfee)
    {
        $mode = 'edit';
        $readonly = false;
        $isEdit = true;
        $forceReadonly = $isEdit || $readonly;
        
        return view('membershipfees.edit', [
            'membershipfee' => $membershipfee,
            'mode' => $mode,
            'readonly' => $readonly,
            'isEdit' => $isEdit,
            'forceReadonly' => $forceReadonly
        ]);
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
            return redirect()->back()->withErrors(['card' => 'Card not found.']);
        }

        if ($card->balance < $feeValue) {
            return redirect()->back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', 'Insufficient balance in card to pay the membership fee. Current balance: €' . number_format($card->balance, 2) . '. Required amount: €' . number_format($feeValue, 2));
        }

        DB::transaction(function () use ($card, $feeValue, $user) {
            // Update balance
            $card->balance -= $feeValue;
            $card->save();

            // Register debit operation
            \App\Models\Operation::create([
                'card_id' => $card->id,
                'type' => 'debit',
                'value' => $feeValue,
                'date' => now()->toDateString(),
                'debit_type' => 'membership_fee',
            ]);

            // Unblock the user
            $user->blocked = 0;
            $user->save();
        });

        return redirect()->back()->with('success', 'Membership fee paid successfully. Your account has been unblocked.');
    }


}

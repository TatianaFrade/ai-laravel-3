<?php

namespace App\Http\Controllers;

use App\Models\MembershipFee;
use App\Http\Requests\MembershipFeeFormRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MembershipFeeController extends Controller
{
    use AuthorizesRequests;

    public function __construct() 
    { 
        $this->authorizeResource(MembershipFee::class, 'membershipfee');
    } 

    public function index()
    {
        $membershipfees = MembershipFee::all();
        
        return view('membershipfees.index')->with('membershipfees', $membershipfees);
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

}

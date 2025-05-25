<?php

namespace App\Http\Controllers;

use App\Models\MembershipFee;

class MembershipFeeController extends Controller
{
    public function index()
    {
      
        $fees = MembershipFee::all();
        return view('membershipfees.index', compact('fees'));

    }
}

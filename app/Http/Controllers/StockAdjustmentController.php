<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;

class StockAdjustmentController extends Controller
{
    public function index()
    {
      
        $stockadjustments = StockAdjustment::all();
        return view('stockadjustments.index', compact('stockadjustments'));

    }
}

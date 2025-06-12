<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StockAdjustmentController extends Controller
{
    use AuthorizesRequests;

     public function __construct() 
    { 
        $this->authorizeResource(StockAdjustment::class, 'stockadjustment');
    }

    public function index()
    {
        $this->authorize('viewAny', StockAdjustment::class);

        $stockadjustments = StockAdjustment::with('product', 'user')->latest()->paginate(15);

        return view('stockadjustments.index')->with('stockadjustments', $stockadjustments);
    }

}

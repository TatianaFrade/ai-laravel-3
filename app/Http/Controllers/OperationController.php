<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Order;

class OperationController extends Controller
{
    use AuthorizesRequests;
    public function __construct()
    {
        $this->authorizeResource(Operation::class, 'operation');
    }



    public function index()
    {
        $operations = Operation::where('card_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $completedOrders = Order::where('status', 'completed')->get()->pluck('pdf_receipt', 'id');

        foreach ($completedOrders as $orderId => $fileName) {
            $completedOrders[$orderId] = url("/receipt/{$fileName}");
        }
        return view('operations.index', compact('operations', 'completedOrders'));
    }

    public function show($id)
    {
        $card = Card::with('operations')->findOrFail($id); 
        return view('operations.card', compact('card')); 
    }


    public function store(Request $request)
    {

    }
}



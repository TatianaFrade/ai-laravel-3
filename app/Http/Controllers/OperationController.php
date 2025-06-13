<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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

        return view('operations.index', compact('operations'));
    }

    public function show($id)
    {
        $card = Card::with('operations')->findOrFail($id); // Busca o cartão com suas operações
        return view('operations.card', compact('card')); // Passa os dados para a view
    }


    public function store(Request $request)
    {
        // $data = $request->validate([
        //     'card_id' => 'required|exists:cards,id',
        //     'type' => 'required|string',
        //     'value' => 'required|numeric',
        //     'date' => 'required|date',
        //     'debit_type' => 'nullable|string',
        //     'credit_type' => 'nullable|string',
        //     'payment_type' => 'nullable|string',
        //     'payment_reference' => 'nullable|string',
        //     'order_id' => 'nullable|exists:orders,id'
        // ]);

        // $operation = Operation::create($data);
        // return response()->json($operation, 201);
    }
}



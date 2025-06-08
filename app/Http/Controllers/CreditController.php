<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\Payment;

class CreditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function credit($service, $reference, $amount)
{
    if (method_exists(Payment::class, $service)) { // Verifica se o método existe
        if (call_user_func([Payment::class, $service], $reference)) { // Chama o método dinamicamente
            $this->balance += $amount;
            $this->save();

            Transaction::create([
                'virtual_card_id' => $this->id,
                'amount' => $amount,
                'type' => 'credit',
                'status' => 'completed',
                'reference' => json_encode($reference)
            ]);

            return true;
        }
    }

    return false;
}
}

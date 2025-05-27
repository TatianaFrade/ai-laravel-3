<?php

namespace App\Http\Controllers;

use App\Models\SupplyOrder;
use App\Models\Product;
use App\Models\User;
use App\Http\Requests\SupplyOrderFormRequest;
use Illuminate\Support\Facades\Auth;




class SupplyOrderController extends Controller
{
    public string $email = '';
    public function index()
    {

        $userType = auth()->check() ? auth()->user()->type : 'guest';
        $allSupplyorders = SupplyOrder::with('product')->paginate(10);
        return view('supplyorders.index', compact('allSupplyorders', 'userType'));




    }


    

    public function create()
    {
        $userType = auth()->check() ? auth()->user()->type : 'guest';
        return view('supplyorders.create', compact( 'userType'));
    }


    public function store(SupplyOrderFormRequest $request)
    {
        $data = $request->validated();

        // Você pode definir o user autenticado como registered_by_user_id, caso não venha no form
        if (!isset($data['registered_by_user_id'])) {
            $data['registered_by_user_id'] = auth()->id();
        }

        SupplyOrder::create($data);

        return redirect()->route('supplyorders.index')->with('success', 'SupplyOrder created successfully.');
    }




   public function destroy($id)
    {
        
        try {
            
            $supplyorder = SupplyOrder::findOrFail($id);

            if ($supplyorder->status !== 'requested') {
                return redirect()->route('supplyorders.index')
                    ->with('error', 'Only supply orders with status "requested" can be deleted.');
            }

            $supplyorder->delete(); // apagar normalmente

            return redirect()->route('supplyorders.index')
            ->with('success', 'Supply order deleted successfully.');
            

        } catch (\Exception $e) {
             return redirect()->route('supplyorders.index')
            ->with('error', 'Only supply orders with status "requested" can be deleted.');
        }

       


    }


    public function show(SupplyOrder $supplyorder)
    {
        return view('supplyorders.show', ['supplyorder' => $supplyorder]);
    }

    public function edit(SupplyOrder $supplyorder)
    {
        $user = Auth::user(); // pega o utilizador autenticado
        return view('supplyorders.edit', [
            'supplyorder' => $supplyorder,
            'user' => $user,
        ]);
    }

   public function update(SupplyOrderFormRequest $request, SupplyOrder $supplyorder)
    {
        $data = $request->validated();

        $currentStatus = $supplyorder->status;
        $newStatus = $data['status'] ?? $currentStatus;

        $statusChangedToCompleted = $currentStatus === 'requested' && $newStatus === 'completed';

        if ($statusChangedToCompleted) {
            $product = Product::find($supplyorder->product_id);

            if ($product) {
                $product->stock += $supplyorder->quantity;
                $product->save();
            } else {
                \Log::warning('Produto não encontrado para supply order ID: ' . $supplyorder->id);
            }
        }

        $supplyorder->update($data);

        return redirect()->route('supplyorders.index')->with('success', 'Supply order updated successfully.');
    }




}

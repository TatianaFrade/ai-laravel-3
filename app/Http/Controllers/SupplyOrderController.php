<?php

namespace App\Http\Controllers;

use App\Models\SupplyOrder;
use App\Http\Requests\SupplyOrderFormRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class SupplyOrderController extends Controller
{

    use AuthorizesRequests;
    public string $email = '';

    public function __construct() 
    { 
        $this->authorizeResource(SupplyOrder::class, 'supplyorder');
    }
    public function index()
    {         $allSupplyorders = SupplyOrder::with(['product','registeredByUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('supplyorders.index')->with('allSupplyorders', $allSupplyorders);
    }
    
    public function create()
    {
        $userType = auth()->user()->type ?? null;
        return view('supplyorders.create')->with('userType', $userType);
    }


    public function store(SupplyOrderFormRequest $request)
    {
        $data = $request->validated();

        if (!isset($data['registered_by_user_id'])) {
            $data['registered_by_user_id'] = auth()->id();
        }

        SupplyOrder::create($data);

        return redirect()->route('supplyorders.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Supply Order created successfully.');
    }




  public function destroy(SupplyOrder $supplyorder)
    {
        $this->authorize('delete', $supplyorder);

        if ($supplyorder->status !== 'requested') {
            return redirect()->route('supplyorders.index')
                ->with('alert-type', 'error')
                ->with('alert-msg', 'Only supply orders with status "requested" can be deleted.');
        }

        $supplyorder->delete();

        return redirect()->route('supplyorders.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', 'Supply order deleted successfully.');
    }



    public function show(SupplyOrder $supplyorder)
    {
        return view('supplyorders.show', ['supplyorder' => $supplyorder]);
    }



    public function edit(SupplyOrder $supplyorder)
    {
        if ($supplyorder->status !== 'requested') {
        return redirect()->route('supplyorders.index')
            ->with('alert-type', 'error')
            ->with('alert-msg', 'Only supply orders with status "requested" can be edited.');
        }

        $user = auth()->user();
        return view('supplyorders.edit', [
            'supplyorder' => $supplyorder,
            'user' => $user,
        ]);
    }

    public function update(SupplyOrderFormRequest $request, SupplyOrder $supplyorder)
    {
        if ($supplyorder->status !== 'requested') {
            return redirect()->route('supplyorders.index')
                ->with('error', 'Only supply orders with status "requested" can be updated.');
        }

        $data = $request->validated();

        $currentStatus = $supplyorder->status;
        $newStatus = $data['status'] ?? $currentStatus;

        $statusChangedToCompleted = $currentStatus === 'requested' && $newStatus === 'completed';

        if ($statusChangedToCompleted) {
            // Get the related product using the correct relationship
            $product = $supplyorder->product;

            if ($product) {
                // Check if adding the supply quantity would exceed the upper limit
                $newStock = $product->stock + $supplyorder->quantity;
                
                if ($product->stock_upper_limit && $newStock > $product->stock_upper_limit) {
                    return redirect()->route('supplyorders.index')
                        ->with('alert-type', 'error')
                        ->with('alert-msg', "Cannot complete this supply order: The resulting stock ({$newStock}) would exceed the product's upper limit ({$product->stock_upper_limit}).");
                }
                
                // Update the product stock
                $product->stock = $newStock;
                $product->save();
            }
        }

        $supplyorder->update($data);

        return redirect()->route('supplyorders.index')
        ->with('alert-type', 'success')
        ->with('alert-msg', 'Supply order updated successfully.');
    }


}

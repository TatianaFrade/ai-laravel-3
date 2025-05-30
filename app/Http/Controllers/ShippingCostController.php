<?php

namespace App\Http\Controllers;

use App\Models\ShippingCost;
use App\Http\Requests\ShippingCostFormRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ShippingCostController extends Controller
{

    use AuthorizesRequests;

    public function __construct() 
    { 
        $this->authorizeResource(ShippingCost::class, 'shippingcost');
    }

    public function index()
    {
        $costs = ShippingCost::all();
        return view('shippingcosts.index', compact('costs'));
    }

    public function create()
    {
        return view('shippingcosts.create');
    }

   public function store(ShippingCostFormRequest $request)
    {
        $data = $request->validated();

        if (empty($data['max_value_threshold'])) {
            $data['max_value_threshold'] = 9999999.99;
        }

        $lastId = ShippingCost::max('id') ?? 0;

        $data['name'] = 'Shipping Cost ' . ($lastId + 1);

        ShippingCost::create($data);

        return redirect()->route('shippingcosts.index')->with('success', 'Shipping cost created successfully.');
    }

     public function show(ShippingCost $shippingcost)
    {
        return view('shippingcosts.show', ['cost' => $shippingcost]);
    }

    public function edit(ShippingCost $shippingcost)
    {
        return view('shippingcosts.edit', ['cost' => $shippingcost]);
    }

    public function update(ShippingCostFormRequest $request, ShippingCost $shippingcost)
    {
        $shippingcost->update($request->validated());

        return redirect()->route('shippingcosts.index')->with('success', 'Shipping cost updated successfully.');
    }


    public function destroy(ShippingCost $shippingcost)
    {
        try {
            $shippingcost->delete();

            return redirect()->route('shippingcosts.index')->with('success', 'Shipping cost deleted successfully.');

        } catch (\Exception $error) {
            $message = $error->getMessage();

            if (str_contains($message, 'Integrity constraint violation')) {
                $alertMsg = "Cannot delete this shipping cost because it is associated with other records.";
            } else {
                $alertMsg = "It was not possible to delete the shipping cost due to an unexpected error.";
            }

            return redirect()->route('shippingcosts.index')->with('error', $alertMsg);
        }
    }

}

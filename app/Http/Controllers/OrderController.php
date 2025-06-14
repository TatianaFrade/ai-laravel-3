<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Card;
use App\Models\User;
use App\Http\Requests\OrderFormRequest;
use App\Models\ShippingCost;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCompletedMail;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class OrderController extends Controller
{
    use AuthorizesRequests;
    
    public string $email = '';
    
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    public function index(Request $request)
    {
        // $this->authorize('viewAny', Order::class); // Using authorizeResource now
        
        $user = Auth::user();
        $onlyOwnOrders = $request->boolean('mine');

       if ($user->type === 'employee') {
            $orders = Order::query()
                ->with('user')  // Eager load user relationship
                ->when($onlyOwnOrders, fn($query) => $query->where('member_id', $user->id))
                ->when(!$onlyOwnOrders, fn($query) => $query->where('status', 'pending'))
                ->orderByDesc('created_at')
                ->paginate(20);
        

        } elseif ($user->type === 'board') {
            if ($onlyOwnOrders) {
                $orders = Order::with('user')  // Eager load user relationship
                            ->where('member_id', $user->id)
                            ->orderByDesc('created_at')
                            ->paginate(20);
            } else {
                $orders = Order::with('user')  // Eager load user relationship
                            ->orderByDesc('created_at')
                            ->paginate(20);
            }
        } elseif ($user->type === 'member') {
            $orders = Order::with('user')  // Eager load user relationship
                        ->where('member_id', $user->id)
                        ->orderByDesc('created_at')
                        ->paginate(20);
        } else {
            abort(403, 'Unauthorized access');
        }

        // Determine if the user is a member (for view conditionals)
        $isMember = $user->type === 'member';
        
        return view('orders.index', [
            'allOrders' => $orders,
            'isMember' => $isMember,
        ]);
    }


    public function create()
    {
        $user = Auth::user();
        
        // Prepare field configurations
        $mode = 'create';
        $readonly = false;
        $isCreate = true;
        $isEdit = false;
        $isEmployee = $user->type === 'employee';
        $needsHiddenFields = false;

        $dateValue = old('date', now()->format('Y-m-d'));
        $cancelReason = old('cancel_reason', '');
        $cancelReasonOther = old('cancel_reason_other', '');
        
        return view('orders.create', [
            'user' => $user,
            'mode' => $mode,
            'readonly' => $readonly,
            'isCreate' => $isCreate,
            'isEdit' => $isEdit,
            'isEmployee' => $isEmployee,
            'needsHiddenFields' => $needsHiddenFields,
            'dateValue' => $dateValue,
            'cancelReason' => $cancelReason,
            'cancelReasonOther' => $cancelReasonOther,
        ]);
    }


    public function store(OrderFormRequest $request)
    {
        // Get validated data
        $validated = $request->validated();

        // Ensure date is filled with current date if not provided
        $validated['date'] = $validated['date'] ?? now();

        // Set the authenticated user's ID
        $validated['member_id'] = $validated['member_id'] ?? auth()->id();

        // Get the authenticated user
        $user = User::find($validated['member_id']);

        if (!$user) {
            return back()->withErrors(['member_id' => 'User not found.']);
        }

        // Get total_items from form or default to 0
        $totalItems = isset($validated['total_items']) ? (float)$validated['total_items'] : 0;
        
        // If shipping_cost was provided in the form, use it directly
        if (isset($validated['shipping_cost'])) {
            $shippingCost = (float)$validated['shipping_cost'];
        } else {
            // Otherwise calculate based on shipping cost rules
            $shippingCostRecord = ShippingCost::where('min_value_threshold', '<=', $totalItems)
                ->where('max_value_threshold', '>=', $totalItems)
                ->first();
            $shippingCost = $shippingCostRecord ? (float)$shippingCostRecord->shipping_cost : 0;
        }

        // Calculate the total
        $validated['shipping_cost'] = $shippingCost;
        $validated['total_items'] = $totalItems;
        $validated['total'] = $totalItems + $shippingCost;

        \Log::info("Creating new order with totals: items={$totalItems}, shipping={$shippingCost}, total={$validated['total']}");

        // Set the status as 'pending'
        $validated['status'] = 'pending';

        // Find the user's card
        $card = Card::find($user->id);

        if (!$card) {
            return back()->withErrors(['card' => 'Card not found for the user.']);
        }
    
        if ($card->balance < $validated['total']) {
            return back()->withErrors(['card' => 'Insufficient balance on the card.']);
        }

        // Start transaction to ensure atomicity
        \DB::beginTransaction();
        try {
            // Create order model
            $order = new Order($validated);
            
            // Ensure total is calculated using our model method
            $calculatedTotal = $order->calculateTotal();
            \Log::info("New order: Calculated total = {$calculatedTotal}");
            
            // Save the order
            $order->save();
            
            // Deduct the total from the card balance
            $card->balance -= $order->total;
            $card->save();
            
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error creating order: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error creating order: ' . $e->getMessage()]);
        }

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }


    public function destroy(Order $order)
    {
        return false;
    }


    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Eager load the user relationship
        $order->load('user', 'items.product');
        
        // Prepare field configurations for show mode
        $mode = 'show';
        $readonly = true;
        $isCreate = false;
        $isEdit = false;
        $isEmployee = $user->type === 'employee';
        $needsHiddenFields = false;

        $dateValue = $order->date;
        $cancelReason = $order->cancel_reason ?? '';
        $cancelReasonOther = $order->cancel_reason_other ?? '';
        
        return view('orders.show', [
            'order' => $order,
            'user' => $user,
            'mode' => $mode,
            'readonly' => $readonly,
            'isCreate' => $isCreate,
            'isEdit' => $isEdit,
            'isEmployee' => $isEmployee,
            'needsHiddenFields' => $needsHiddenFields,
            'dateValue' => $dateValue,
            'cancelReason' => $cancelReason,
            'cancelReasonOther' => $cancelReasonOther,
        ]);
    }

    public function edit(Order $order)
    {
        $user = Auth::user(); // get authenticated user
        
        // Eager load the user relationship and items with their products
        $order->load('user', 'items.product');
        
        // Prepare field configurations
        $mode = 'edit';
        $readonly = false;
        $isCreate = false;
        $isEdit = true;
        $isEmployee = $user->type === 'employee';
        $readonly = $isEdit && $isEmployee;
        $needsHiddenFields = $isEdit && $isEmployee;

        $dateValue = old('date', $order->date ?? now()->format('Y-m-d'));
        $cancelReason = old('cancel_reason', $order->cancel_reason ?? '');
        $cancelReasonOther = old('cancel_reason_other', $order->cancel_reason_other ?? '');
        
        return view('orders.edit', [
            'order' => $order,
            'user' => $user,
            'mode' => $mode,
            'readonly' => $readonly,
            'isCreate' => $isCreate,
            'isEdit' => $isEdit,
            'isEmployee' => $isEmployee,
            'needsHiddenFields' => $needsHiddenFields,
            'dateValue' => $dateValue,
            'cancelReason' => $cancelReason,
            'cancelReasonOther' => $cancelReasonOther,
        ]);
    }

    public function update(OrderFormRequest $request, Order $order)
    {
        //dd('Entrou no update');
        $order->load(['items.product', 'user']);

        $data = $request->validated();

        $user = auth()->user();

        $currentStatus = $order->status;
        $newStatus = $data['status'] ?? $currentStatus;

        //dd(compact('user', 'data', 'order'));
        // Validação para cancelar encomenda
        if ($newStatus === 'canceled') {
            if ($user->type !== 'board') {
                return back()->withErrors(['status' => 'Only board members can cancel orders.']);
            }


            if ($currentStatus !== 'pending') {
                return back()->withErrors(['status' => 'Only orders with "pending" status can be canceled.']);
            }

            if (empty($data['cancel_reason'])) {
                return back()->withErrors(['cancel_reason' => 'You must provide a reason for cancellation.']);
            }
            
            try {
                // Use database transaction to ensure atomic operations
                \DB::beginTransaction();
                
                $order->cancel_reason = $data['cancel_reason'];

                $card = $order->user->card;
                //dd($order->user, $order->user->card);

                if ($card) {
                    $card->balance += $order->total;
                    $card->save();
                } else {
                    \Log::warning('Card not found for user_id: ' . $order->user->id);
                }
                
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Erro ao cancelar encomenda: ' . $e->getMessage());
                return back()->withErrors(['status' => 'Erro ao processar o cancelamento: ' . $e->getMessage()]);
            }
        } else if ($newStatus === 'pending' && $currentStatus === 'canceled') {
            // Handle transition from canceled back to pending
            if ($user->type !== 'board') {
                return back()->withErrors(['status' => 'Only board members can reactivate canceled orders.']);
            }
            
            // Remove the cancel reason
            $order->cancel_reason = null;
            
            // Deduct the money from card again
            $card = $order->user->card;
            if ($card) {
                if ($card->balance < $order->total) {
                    return back()->withErrors(['status' => 'Insufficient balance in card to reactivate the order.']);
                }
                $card->balance -= $order->total;
                $card->save();
            } else {
                \Log::warning('Card not found for user_id: ' . $order->user->id);
                return back()->withErrors(['status' => 'User card not found.']);
            }
        } else {
            $order->cancel_reason = null;
        }

        $statusChangedToCompleted = $newStatus === 'completed' && $currentStatus !== 'completed';

        if ($statusChangedToCompleted) {
            // Check if the order can be completed
            if (!$order->canBeCompleted()) {
                // Check if there are products exceeding upper limit
                $exceededProducts = $order->getStockUpperLimitExceededProducts();
                
                if (!empty($exceededProducts)) {
                    $productList = [];
                    foreach ($exceededProducts as $item) {
                        $productList[] = "{$item['product']->name} (current: {$item['current_stock']}, upper limit: {$item['upper_limit']})";
                    }
                    
                    $errorMessage = 'Cannot mark as completed: the following products would exceed the upper stock limit: ' . 
                                   implode(', ', $productList);
                    
                    return back()->withErrors(['status' => $errorMessage]);
                } else {
                    return back()->withErrors(['status' => 'Cannot mark as completed: insufficient stock in some products.']);
                }
            }
        }

        // Handle the recalculation of totals if order is editable (pending status)
        if ($currentStatus === 'pending') {
            // Get the total_items from the form if provided, otherwise use existing
            $totalItems = isset($data['total_items']) ? (float)$data['total_items'] : (float)($order->total_items ?? 0);
            
            // If shipping_cost was provided in the form, use it directly
            if (isset($data['shipping_cost'])) {
                $shippingCost = (float)$data['shipping_cost'];
            } else {
                // Otherwise calculate from the shipping cost rules
                $shippingCostRecord = ShippingCost::where('min_value_threshold', '<=', $totalItems)
                    ->where('max_value_threshold', '>=', $totalItems)
                    ->first();
                $shippingCost = $shippingCostRecord ? (float)$shippingCostRecord->shipping_cost : 0;
            }

            // Update the data array with the calculated values
            $data['shipping_cost'] = $shippingCost;
            $data['total_items'] = $totalItems;
            
            // Calculate total (total_items + shipping_cost)
            // We'll handle actual total in a database transaction to make sure it's properly updated
            $data['total'] = $totalItems + $shippingCost;

            \Log::info("Updating order #{$order->id} with new totals: items={$totalItems}, shipping={$shippingCost}, total={$data['total']}");
        } else {
            // For non-editable orders (not in pending status), use existing values
            $data['total_items'] = $order->total_items;
            $data['shipping_cost'] = $order->shipping_cost;
            $data['total'] = $order->total;
        }

        // Use DB transaction to ensure atomicity
        \DB::beginTransaction();
        try {
            // Set the values directly on the model for proper calculation
            $order->fill($data);
            
            // Force calculation of totals
            if ($currentStatus === 'pending') {
                $calculatedTotal = $order->calculateTotal();
                \Log::info("Order #{$order->id}: Calculated total = {$calculatedTotal}");
            }
            
            $order->save();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error updating order #{$order->id}: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error updating order: ' . $e->getMessage()]);
        }

        if ($statusChangedToCompleted) {
            $order->load(['user', 'items.product']);

            if (!$order->user) {
                \Log::error('User not found for order id: ' . $order->id . ', member_id: ' . $order->member_id);
                return back()->withErrors(['user' => 'Associated user not found for this order.']);
            }

            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->stock = max(0, $product->stock - $item->quantity);
                    $product->save();
                } else {
                    \Log::warning('Product not found for item id: ' . $item->id);
                }
            }

            $pdf = Pdf::loadView('pdfs.order_receipt', ['order' => $order]);

            $fileName = 'receipt_' . $order->id . '_' . uniqid() . '.pdf';
            $pdfPath = storage_path('app/public/orders/' . $fileName);

            if (!file_exists(dirname($pdfPath))) {
                mkdir(dirname($pdfPath), 0755, true);
            }

            $pdf->save($pdfPath);

            Mail::to($order->user->email)->send(new OrderCompletedMail($order, $pdfPath));

            $order->pdf_receipt = $fileName;
            $order->save();
        }

        return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
    }

    //continuar a partir do método orderCancel









    
    public function orderCancel(Order $order)
    {
        $user = auth()->user();

        if ($user->type !== 'board') {
            return back()->withErrors(['status' => 'Only board members can cancel orders.']);
        }

        if ($order->status !== 'pending') {
            return back()->withErrors(['status' => 'Only orders with "pending" status can be canceled.']);
        }

        $cancelReason = request('cancel_reason');
        if (empty($cancelReason)) {
            return back()->withErrors(['cancel_reason' => 'You must provide a reason for cancellation.']);
        }
        
        try {
            // Use database transaction to ensure atomic operations
            \DB::beginTransaction();
            
            $order->cancel_reason = $cancelReason;
            $order->status = 'canceled';
            $order->save();

            $card = $order->user->card ?? null;
            if ($card) {
                $card->balance += $order->total;
                $card->save();
                
                // Log the refund operation
                \Log::info("Order #{$order->id} canceled. Refunded {$order->total} to user #{$order->user->id}'s card.");
            } else {
                \Log::warning("Order #{$order->id} canceled but card not found for user_id: {$order->user->id}");
            }
            
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error canceling order: ' . $e->getMessage());
            return back()->withErrors(['status' => 'Error processing cancellation: ' . $e->getMessage()]);
        }

        return redirect()->route('orders.index')->with('success', 'Order canceled successfully.');
    }
}

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
                ->when($onlyOwnOrders, fn($query) => $query->where('member_id', $user->id))
                ->when(!$onlyOwnOrders, fn($query) => $query->where('status', 'pending'))
                ->orderByDesc('created_at')
                ->paginate(20);
        

        } elseif ($user->type === 'board') {
            if ($onlyOwnOrders) {
                $orders = Order::where('member_id', $user->id)
                            ->orderByDesc('created_at')
                            ->paginate(20);
            } else {
                $orders = Order::orderByDesc('created_at')->paginate(20);
            }
        } elseif ($user->type === 'member') {
            $orders = Order::where('member_id', $user->id)
                        ->orderByDesc('created_at')
                        ->paginate(20);
        } else {
            abort(403, 'Acesso não autorizado');
        }

        return view('orders.index', ['allOrders' => $orders]);
    }


    public function create()
    {
    //     // // Exemplo: total estimado no create pode ser 0 ou default, no edit usar o valor real do order
    //     // $total = 0;

    //     // // Busca o custo de envio conforme o total
    //     // $shippingCost = ShippingCost::where('min_value_threshold', '<=', $total)
    //     //                             ->where('max_value_threshold', '>', $total)
    //     //                             ->first();

    //     // $calculatedShippingCost = $shippingCost ? $shippingCost->shipping_cost : 0;

    //     // return view('orders.create', [
    //     //     'shipping_cost' => $calculatedShippingCost,
    //     //     'mode' => 'create',
           
    //     // ]);
    // }


    // public function store(OrderFormRequest $request)
    // {
    //     // Pega os dados validados
    //     $validated = $request->validated();

    //     // Garante que a data seja preenchida com a atual se não vier do formulário
    //     $validated['date'] = $validated['date'] ?? now();

    //     // Define o ID do utilizador autenticado
    //     $validated['user_id'] = auth()->id();

    //     // 🧾 Obter utilizador autenticado
    //     $user = User::find($validated['user_id']);

    //     if (!$user) {
    //         return back()->withErrors(['user_id' => 'Utilizador não encontrado.']);
    //     }

    //     // Busca o shipping_cost conforme o total_items nas definições
    //     $totalItems = $validated['total_items'] ?? 0;

    //     $shippingCost = ShippingCost::query()
    //         ->where('min_value_threshold', '<=', $totalItems)
    //         ->where('max_value_threshold', '>', $totalItems)
    //         ->value('shipping_cost') ?? 0;

    //     // Calcula o total
    //     $validated['shipping_cost'] = $shippingCost;
    //     $validated['total'] = $totalItems + $shippingCost;

    //     // Define o status como 'pending'
    //     $validated['status'] = 'pending';

      
    //     $card = Card::find($user->id);

    //     if (!$card) {
    //         return back()->withErrors(['card' => 'Cartão não encontrado para o utilizador.']);
    //     }
    
    //     if ($card->balance < $validated['total']) {
    //         return back()->withErrors(['card' => 'Saldo insuficiente no cartão.']);
    //     }

    //     $card->balance -= $validated['total'];
    //     $card->save();

    //     // Cria a encomenda com os dados completos
    //     Order::create($validated);

    //     return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }


    public function destroy(Order $order)
    {
        return false;
    }


    public function show(Order $order)
    {
         return view('orders.show', ['order' => $order]);
    }

    public function edit(Order $order)
    {
        $user = Auth::user(); // pega o utilizador autenticado
        return view('orders.edit', [
            'order' => $order,
            'user' => $user,
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
                return back()->withErrors(['status' => 'Apenas membros do tipo board podem cancelar encomendas.']);
            }


            if ($currentStatus !== 'pending') {
                return back()->withErrors(['status' => 'Só é possível cancelar encomendas com status "pending".']);
            }

            if (empty($data['cancel_reason'])) {
                return back()->withErrors(['cancel_reason' => 'É necessário indicar o motivo do cancelamento.']);
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
                    \Log::warning('Cartão não encontrado para user_id: ' . $order->user->id);
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
                return back()->withErrors(['status' => 'Apenas membros do tipo board podem reativar encomendas canceladas.']);
            }
            
            // Remove the cancel reason
            $order->cancel_reason = null;
            
            // Deduct the money from card again
            $card = $order->user->card;
            if ($card) {
                if ($card->balance < $order->total) {
                    return back()->withErrors(['status' => 'Saldo insuficiente no cartão para reativar a encomenda.']);
                }
                $card->balance -= $order->total;
                $card->save();
            } else {
                \Log::warning('Cartão não encontrado para user_id: ' . $order->user->id);
                return back()->withErrors(['status' => 'Cartão do utilizador não encontrado.']);
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
                    
                    $errorMessage = 'Não pode marcar como completed: os seguintes produtos excederiam o limite superior de stock: ' . 
                                   implode(', ', $productList);
                    
                    return back()->withErrors(['status' => $errorMessage]);
                } else {
                    return back()->withErrors(['status' => 'Não pode marcar como completed: stock insuficiente em algum produto.']);
                }
            }
        }

        $totalItems = $data['total_items'] ?? $order->total_items ?? 0;

        $shippingCostRecord = ShippingCost::where('min_value_threshold', '<=', $totalItems)
            ->where('max_value_threshold', '>=', $totalItems)
            ->first();

        $shippingCost = $shippingCostRecord ? $shippingCostRecord->shipping_cost : 0;

        $data['shipping_cost'] = $shippingCost;
        $data['total'] = $totalItems + $shippingCost;

        // Atualizar a encomenda
        $order->update($data);

        if ($statusChangedToCompleted) {
            $order->load(['user', 'items.product']);

            if (!$order->user) {
                \Log::error('Utilizador não encontrado para order id: ' . $order->id . ', member_id: ' . $order->member_id);
                return back()->withErrors(['user' => 'Utilizador associado não encontrado para esta encomenda.']);
            }

            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->stock = max(0, $product->stock - $item->quantity);
                    $product->save();
                } else {
                    \Log::warning('Produto não encontrado para item id: ' . $item->id);
                }
            }

            $pdf = Pdf::loadView('pdfs.order_receipt', ['order' => $order]);

            $fileName = 'recibo_' . $order->id . '_' . uniqid() . '.pdf';
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
            return back()->withErrors(['status' => 'Apenas membros do tipo board podem cancelar encomendas.']);
        }

        if ($order->status !== 'pending') {
            return back()->withErrors(['status' => 'Só é possível cancelar encomendas com status "pending".']);
        }

        $cancelReason = request('cancel_reason');
        if (empty($cancelReason)) {
            return back()->withErrors(['cancel_reason' => 'É necessário indicar o motivo do cancelamento.']);
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
            \Log::error('Erro ao cancelar encomenda: ' . $e->getMessage());
            return back()->withErrors(['status' => 'Erro ao processar o cancelamento: ' . $e->getMessage()]);
        }

        return redirect()->route('orders.index')->with('success', 'Encomenda cancelada com sucesso.');
    }
}

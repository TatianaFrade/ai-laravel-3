@php
    use Illuminate\Support\Facades\Auth;
 
    $isMember = Auth::user()->type === 'member';
@endphp
 
<x-layouts.main-content :title="__('Orders')" :heading="$isMember ? 'My Orders' : (request()->boolean('mine') ? 'My Orders' : 'List of Orders')" :subheading="$isMember ? 'Here are your orders' : (request()->boolean('mine') ? 'Here are your orders' : 'Manage the orders of all users')">
 
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex justify-start">
            <div class="my-4 p-6 w-full">
                <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
                    @if ($allOrders->count() > 0)
                        <table class="table w-full border-collapse">
                            <thead>
                                <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                                    {{-- <th class="px-3 py-2 text-left">Id</th> --}}
                                    @if (!$isMember)
                                        <th class="px-3 py-2 text-left">Member</th>
                                    @endif
                                    <th class="px-3 py-2 text-left">Status</th>
                                    <th class="px-3 py-2 text-left">Date</th>
                                    <th class="px-3 py-2 text-left">Total Items</th>
                                    <th class="px-3 py-2 text-left">Shipping Cost</th>
                                    <th class="px-3 py-2 text-left">Total</th>
                                    <th class="px-3 py-2 text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allOrders as $order)
                                    <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                                        {{-- <td class="px-3 py-2">{{ $loop->iteration }}</td> --}}
                                        
                                        @if (!$isMember)
                                            <td class="px-3 py-2">
                                                {{ optional($order->user)->email ?? 'Email não disponível' }}
                                            </td>
                                        @endif
 
                                        <td class="px-3 py-2">{{ $order->status }}</td>
                                        <td class="px-3 py-2">{{ $order->date }}</td>
                                        <td class="px-3 py-2">{{ $order->total_items }}</td>
                                        <td class="px-3 py-2">{{ $order->shipping_cost }}</td>
                                        <td class="px-3 py-2">{{ $order->total }}</td>
 
                                        <td class="px-3 py-2 text-center">
                                            <div class="flex justify-center items-center gap-2">
                                                <a href="{{ route('orders.show', ['order' => $order]) }}" title="View">
                                                    <flux:icon.eye class="size-5 hover:text-gray-600" />
                                                </a>
 
                                                @can('update', $order)
                                                    @if ($order->status === 'pending')
                                                        <a href="{{ route('orders.edit', ['order' => $order]) }}" title="Edit">
                                                            <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                                                        </a>
                                                    @else
                                                        <span title="Completed orders cannot be edited" class="cursor-not-allowed opacity-60">
                                                            <flux:icon.pencil-square class="size-5" />
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="size-5 inline-block"></span>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-12 text-gray-500 text-lg">
                            {{ $isMember || request()->boolean('mine') ? "You don't have any orders yet." : "No orders found." }}
                        </div>
                    @endif
            </div>
 
            <div class="mt-4">
                {{ $allOrders->links() }}
            </div>
        </div>
    </div>
    </div>
</x-layouts.main-content>
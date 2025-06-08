<x-layouts.main-content :title="__('Orders')"
                        heading="List of Orders"
                        subheading="Manage the orders">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">
     

        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table w-full border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-3 py-2 text-left">Id</th>
                <th class="px-3 py-2 text-left">Member</th>
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
               
                <td class="px-3 py-2">{{ $order->id }}</td>
               <td class="px-3 py-2">
                  {{ optional($order->user)->email ?? 'Email não disponível' }}
              </td>

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
                    <a href="{{ route('orders.edit', ['order' => $order]) }}" title="Edit">
                      <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                    </a>
                    <a href="{{ route('orders.cancel', ['order' => $order]) }}" title="Cancel Order"></a>
                      <flux:icon.x-circle class="size-5 hover:text-blue-600" />
                    </a>

                    {{-- @if (!$order->trashed())
                      <form method="POST" action="{{ route('orders.destroy', ['order' => $order]) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                title="{{ $order->products_count > 0 ? 'Soft delete (has products)' : 'Permanent delete (no products)' }}">
                          @if ($order->products_count > 0)
                            <flux:icon.cube class="size-5 hover:text-orange-500" />
                          @else
                            <flux:icon.trash class="size-5 hover:text-red-600" />
                          @endif
                        </button>
                      </form>
                    @endif --}}
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-4">
          {{ $allOrders->links() }}
        </div>
      </div>
    </div>
  </div>
</x-layouts.main-content>

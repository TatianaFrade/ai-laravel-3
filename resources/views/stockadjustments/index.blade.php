<x-layouts.main-content :title="__('Inventory records')"
                        heading="Inventory changes on product's stock"
                        subheading="All changes registered">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">
        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table w-full border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">Product</th>
                <th class="px-3 py-2 text-left">Changed By</th>
                <th class="px-3 py-2 text-left">Date</th>
                <th class="px-3 py-2 text-left">Quantity Changed</th>
              </tr>
            </thead>
            <tbody>              @foreach ($stockadjustments as $stockadjustment)
                <tr>
                    <td class="px-3 py-2">{{ ($stockadjustments->currentPage() - 1) * $stockadjustments->perPage() + $loop->iteration }}</td>
                    <td class="px-3 py-2">{{ $stockadjustment->product->name ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $stockadjustment->user->name ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $stockadjustment->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-3 py-2 {{ $stockadjustment->quantity_changed > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $stockadjustment->quantity_changed > 0 ? '+' : '' }}{{ $stockadjustment->quantity_changed }}
                    </td>
                </tr>
              @endforeach            </tbody>
          </table>

          <div class="mt-4">
              {{ $stockadjustments->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</x-layouts.main-content>

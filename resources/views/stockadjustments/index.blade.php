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
                <th class="px-3 py-2 text-left">Product number</th>
                <th class="px-3 py-2 text-left">Member number</th>
                <th class="px-3 py-2 text-left">Quantity changed</th>
                <th class="px-3 py-2 text-center"></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($stockadjustments as $index => $stockadjustment)
                <tr>
                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                    <td class="px-3 py-2">{{ $stockadjustment->product->name ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $stockadjustment->user->name ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $stockadjustment->quantity_changed ?? '0' }}</td>
                </tr>
              @endforeach

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</x-layouts.main-content>

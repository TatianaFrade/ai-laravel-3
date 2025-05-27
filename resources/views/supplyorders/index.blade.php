<x-layouts.main-content :title="__('Orders')"
                        heading="List of Orders"
                        subheading="Manage the orders">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-4 mb-4">
        @if($userType === 'board' || $userType === 'employee')
            <flux:button variant="primary" href="{{ route('supplyorders.create') }}">
            Create a new supply order
            </flux:button>
        @endif
    </div>
    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">

        {{-- MENSAGENS DE SUCESSO E ERRO --}}
        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800 dark:bg-red-200 dark:text-red-900">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800 dark:bg-green-200 dark:text-green-900">
                {{ session('success') }}
            </div>
        @endif

        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table w-full border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-3 py-2 text-left">Id</th>
                <th class="px-3 py-2 text-left">Product number</th>
                <th class="px-3 py-2 text-left">Member number</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-left">Quantity</th>
                <th class="px-3 py-2 text-center"></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($allSupplyorders as $supplyorder)
              <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                <td class="px-3 py-2">{{ $supplyorder->id }}</td>
                <td class="px-3 py-2">
                  {{ optional($supplyorder->product)->id ?? 'Product not available' }}
                </td>
                <td class="px-3 py-2">{{ $supplyorder->registered_by_user_id }}</td>
                <td class="px-3 py-2">{{ $supplyorder->status }}</td>
                <td class="px-3 py-2">{{ $supplyorder->quantity }}</td>
                <td class="px-3 py-2 text-center">
                  <div class="flex justify-center items-center gap-2">
                    <a href="{{ route('supplyorders.edit', ['supplyorder' => $supplyorder]) }}" title="Edit">
                      <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                    </a>

                    <form method="POST" action="{{ route('supplyorders.destroy', $supplyorder) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this supply order?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Delete supply order">
                            <flux:icon.trash class="size-5 hover:text-red-600" />
                        </button>
                    </form>

                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-4">
          {{ $allSupplyorders->links() }}
        </div>
      </div>
    </div>
  </div>
</x-layouts.main-content>

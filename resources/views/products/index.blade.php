<x-layouts.main-content :title="__('Products')" heading="List of Products">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl text-sm"> {{-- aplica redução global de texto --}}

    @if (request('view') !== 'public')
    <div class="flex justify-between items-center mb-4">
      @can('create', App\Models\Product::class)
        <flux:button variant="primary" href="{{ route('products.create') }}">
          Create a new product
        </flux:button>
      @else
        <div></div>
      @endcan

      @can('viewAny', App\Models\StockAdjustment::class)
        <flux:button variant="primary" href="{{ route('stockadjustments.index') }}">
          Inventory records
        </flux:button>
      @endcan
    </div>
  @endif

    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">

        @can('viewFilter', App\Models\Product::class)
          <x-products.filter-card 
          class="mb-6"
            :filterAction="route('categories.index')" 
            :resetUrl="route('categories.index')" 
          />
        @endcan

        @can('viewTable', App\Models\Product::class)
         <x-products.table 
            :products="$allProducts" 
            :showView="true"
            :showEdit="in_array($userType, ['board', 'employee'])"
            :showDelete="$userType === 'board'"
            :showAddToCart="false"
            :showRemoveFromCart="false"
            :isCart="false"
          />

        @else
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
           @foreach ($allProducts as $product)
            @include('components.products.card', ['product' => $product])
          @endforeach

          </div>
        @endcan

        <div class="mt-4 flex justify-center">
          {{ $allProducts->withQueryString()->links() }}
        </div>

      </div>
    </div>
  </div>
</x-layouts.main-content>

<x-layouts.main-content :title="__('Products')" heading="List of Products">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl text-sm"> {{-- aplica redução global de texto --}}

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

    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">

        @can('viewFilter', App\Models\Product::class)
          <x-products.filter-card 
            :filterAction="route('categories.index')" 
            :resetUrl="route('categories.index')" 
          />
        @endcan

        @can('viewTable', App\Models\Product::class)
          <x-products.table 
            :products="$allProducts" 
            :showView="true"
            :showEdit="$userType === 'board'"
            :showDelete="$userType === 'board'"
            :showAddToCart="$userType !== 'board'"
            :showRemoveFromCart="false"
            :isCart="false"
          />
        @else
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($allProducts as $product)
              <div class="rounded-xl shadow p-4 border bg-white dark:bg-gray-900 text-sm">

                @php
                  $imagePath = 'storage/products/' . $product->photo;
                  $fullImagePath = public_path($imagePath);
                @endphp

                <div class="w-full h-48 mb-3">
                  @if ($product->photo && file_exists($fullImagePath))
                    <img src="{{ asset($imagePath) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded" />
                  @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div>
                  @endif
                </div>

                <div class="text-sm font-semibold mb-1">{{ $product->name }}</div>
                <div class="text-xs text-gray-600 mb-2">{{ $product->category->name ?? '—' }}</div>

                <div class="text-sm font-bold mb-1 {{ $product->discount ? 'text-green-600' : 'text-white' }}">
                  {{ number_format($product->price - ($product->discount ?? 0), 2) }} €
                </div>

                @if ($product->discount)
                  <div class="text-xs text-gray-500 line-through">
                    {{ number_format($product->price, 2) }} €
                  </div>
                @endif

                <div class="text-xs text-gray-700 mt-2">
                  {{ Str::limit($product->description_translated, 100) }}
                </div>

                <form method="POST" action="{{ route('cart.add', ['product' => $product]) }}" class="mt-4">
                  @csrf
                  <button type="submit" class="bg-green-600 text-white py-1.5 px-3 text-sm rounded hover:bg-green-700 w-full">
                    Add to cart
                  </button>
                </form>
              </div>
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

<x-layouts.main-content :title="__('Products')" heading="List of Products" subheading="All products available">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl text-sm">


    {{-- existem produtos sem stock logo apresenta essa mensagem --}}

    {{-- @if ($allProducts->contains(fn ($product) => $product->stock <= 0))
        <div class="p-4 bg-red-100 text-red-800 rounded mb-4">
            ⚠️ Existem produtos sem stock!
        </div>
    @endif --}}


    <div class="flex justify-between items-center mb-4">
      @if (request('view') !== 'public')
        @can('create', App\Models\Product::class)
          <flux:button variant="primary" href="{{ route('products.create') }}">
            Create a new product
          </flux:button>
        @endcan


        @can('viewAny', App\Models\StockAdjustment::class)
          <flux:button variant="primary" href="{{ route('stockadjustments.index') }}">
            Inventory records
          </flux:button>
        @endcan




        {{-- Pagina para mostrar os produtos que tem stock inferior ao stock lower limit --}}
        {{-- @can('viewAny', App\Models\StockAdjustment::class)
          <flux:button variant="primary" href="{{ route('products.stock-level') }}">
            Stock abaixo do limite
          </flux:button>
        @endcan --}}



      @else
        {{-- Se for vista pública, podemos mostrar apenas o espaço em branco ou ocultar os botões --}}
        <div></div>
      @endif
    </div>


    {{-- mostrar os ultimos produtos criados --}}
      {{-- <div class="my-6">
        <h2 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-200">Latest Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach ($latestProducts as $product)
              <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</p>
              </div>
          @endforeach
        </div> 
      </div> --}}


       {{-- mostrar os produtos mais comprados --}}
      {{-- <div class="my-6">
        <h2 class="text-lg font-semibold mb-3 text-gray-800 dark:text-gray-200">Most Sold Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach ($mostSoldProducts as $product)
              <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</p>
              </div>
          @endforeach
        </div> 
      </div>  --}}




    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">

        <x-products.filter-card 
          class="mb-6"
          :filterAction="request('view') === 'public' ? route('products.index', ['view' => 'public']) : route('products.index')" 
          :resetUrl="request('view') === 'public' ? route('products.index', ['view' => 'public']) : route('products.index')"
          :filter-by-name="$filterByName"
          :order-price="$orderPrice"
          :order-stock="$orderStock"
          :order-name="$orderName"
          :order-discount="$orderDiscount"
          :filter-by-categoria="$filterByCategoria"
          :categories="$categories"
        
          
          
        />
        {{-- orderDiscount --}}
        {{-- orderName --}}

        @if (request('view') === 'public')
 
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($allProducts as $product)
              @include('components.products.card', ['product' => $product, 'cart' => $cart])
            @endforeach
          </div>
        @elseif (auth()->check() && auth()->user()->can('viewTable', App\Models\Product::class))

          <x-products.table 
            :products="$allProducts" 
            :showView="true"
            :showEdit="in_array($userType, ['board', 'employee'])"
            :showDelete="$userType === 'board'"
            :showAddToCart="false"
            :showRemoveFromCart="false"
            :isCart="false"
            :userType="$userType"
          />
          
          @else

          {{-- tive de adicionar a variavel cart ao productController, pois nao estava a ser passada --}}
        
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($allProducts as $product)
              @include('components.products.card', ['product' => $product, 'cart' => $cart])
            @endforeach

          </div>
        @endif

        <div class="mt-4 flex justify-center">
          {{ $allProducts->withQueryString()->links() }}
        </div>

      </div>
    </div>
  </div>
</x-layouts.main-content>

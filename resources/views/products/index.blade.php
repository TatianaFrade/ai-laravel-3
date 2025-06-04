<x-layouts.main-content :title="__('Products')" heading="List of Products">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    
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

        {{-- Filtros mantidos para todos --}}
        <x-products.filter-card 
          :filterAction="route('categories.index')" 
          :resetUrl="route('categories.index')" 
        />

        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table-auto border-collapse w-full">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left hidden sm:table-cell">Photo</th>
                <th class="px-2 py-2 text-left">Name</th>
                <th class="px-2 py-2 text-left">Category</th>
                
                @if($userType === 'board' || $userType === 'employee')
                  <th class="px-2 py-2 text-left">Price</th>
                  <th class="px-2 py-2 text-left hidden sm:table-cell">Stock</th>
                  <th class="px-2 py-2 text-left hidden sm:table-cell">Description</th>
                  <th class="px-2 py-2 text-left hidden sm:table-cell">Discount</th>
                @else
                  <th class="px-2 py-2 text-left">Price</th>
                  <th class="px-2 py-2 text-left">Discount</th>
                  <th class="px-2 py-2 text-left hidden sm:table-cell">Description</th>
                @endif

                 <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
                    <x-products.table 
                      :products="$allProducts" 
                      :showView="true"
                      :showEdit="$userType === 'board'"
                      :showDelete="$userType === 'board'"
                      :showAddToCart="$userType !== 'board'"
                      :showRemoveFromCart="false"
                      :isCart="false"
                    />

                <th class="px-2 py-2 text-left"></th>

                @if($userType === 'board' || $userType === 'employee')
                  <th class="px-2 py-2 text-left"></th>
                  <th class="px-2 py-2 text-left"></th>
                @endif
              </tr>
            </thead>

            <tbody>
              {{-- @foreach ($allProducts as $product)
                @php
                  $hasDiscount = $product->discount && $product->discount > 0;
                  $priceAfterDiscount = $hasDiscount
                      ? $product->price - $product->discount
                      : $product->price;
                @endphp

                <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
        
                  <td class="px-2 py-2 hidden sm:table-cell">
                    @php
                      $imagePath = 'storage/products/' . $product->photo;
                      $fullImagePath = public_path($imagePath);
                    @endphp
                    @if ($product->photo && file_exists($fullImagePath))
                      <img src="{{ asset($imagePath) }}" alt="Photo of {{ $product->name }}" class="h-20 w-20 object-cover" />
                    @else
                      <span class="text-gray-400">No photo</span>
                    @endif
                  </td>

        
                  <td class="px-2 py-2 text-left {{ (!$userType === 'board' && $hasDiscount) ? 'text-green-700 font-semibold' : '' }}">
                    @if ($userType !== 'board' && $hasDiscount)
                      <span class="text-green-700 font-semibold">{{ $product->name }}</span>
                    @else
                      {{ $product->name }}
                    @endif
                  </td>

                  <td class="px-2 py-2 text-left">{{ $product->category->name ?? '—' }}</td>

           
                  @if ($userType === 'board' )
                    <td class="px-2 py-2 text-left">{{ number_format($product->price, 2) }}€</td>
                  @else
                    <td class="px-2 py-2 text-left {{ $hasDiscount ? 'text-green-700 font-semibold' : '' }}">
                      {{ number_format($priceAfterDiscount, 2) }} €
                    </td>
                  @endif

                 
                  @if($userType === 'board' || $userType === 'employee')
                    <td class="px-2 py-2 text-left hidden sm:table-cell">
                      <div class="flex items-center gap-1">
                        {{ $product->stock }}
                        @if ($product->stock <= $product->stock_lower_limit)
                          <span title="Low stock">
                            <flux:icon.battery-50 class="size-5 text-yellow-500" />
                          </span>
                        @elseif ($product->stock >= $product->stock_upper_limit)
                          <span title="Max stock">
                            <flux:icon.battery-100 class="size-5 text-green-500" />
                          </span>
                        @endif
                      </div>
                    </td>

              
                    <td class="px-2 py-2 text-left hidden sm:table-cell">
                      {{ $product->description_translated }}
                    </td>

                    <td class="px-2 py-2 text-left hidden sm:table-cell">
                      {{ $product->discount ? $product->discount . '€' : '—' }}
                    </td>
                  @else
                 
                    <td class="px-2 py-2 text-left {{ $hasDiscount ? 'text-green-700 font-semibold' : '' }}">
                      {{ $hasDiscount ? $product->discount . '€' : '—' }}
                    </td>

              
                    <td class="px-2 py-2 text-left hidden sm:table-cell">
                      {{ $product->description_translated }}
                    </td>
                  @endif

           
                  <td class="px-2 py-2 text-left"></td>

            
                  @if($userType === 'board' || $userType === 'employee')
                    <td class="px-2 py-2 text-center">
                      <a href="{{ route('products.edit', ['product' => $product]) }}" title="Edit">
                        <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                      </a>
                    </td>
                  @endif
                  @if($userType === 'board')
                    <td class="px-2 py-2 text-center">
                      <form method="POST" action="{{ route('products.destroy', ['product' => $product]) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Delete">
                          <flux:icon.trash class="size-5 hover:text-red-600" />
                        </button>
                      </form>
                    </td>
                  @endif
                    

                
                </tr>
              @endforeach --}}

            </tbody>
          </table>
        </div>

        
       

      </div>
       <!-- Paginação -->
          <div class="mt-4 flex justify-center">
              {{ $allProducts->withQueryString()->links() }}
          </div>
    </div>
  </div>
</x-layouts.main-content>
<x-layouts.main-content :title="__('Products')" heading="List of Products">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-4 mb-4">
      <flux:button variant="primary" href="{{ route('products.create') }}">Create a new product</flux:button>
    </div>
    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">
        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table-auto border-collapse w-full">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left hidden sm:table-cell">Photo</th>
                <th class="px-2 py-2 text-left">Name</th>
                <th class="px-2 py-2 text-left">Category</th>
                <th class="px-2 py-2 text-left">Price</th>
                <th class="px-2 py-2 text-left hidden sm:table-cell">Stock</th>
                <th class="px-2 py-2 text-left hidden sm:table-cell">Description</th>
                <th class="px-2 py-2 text-left"></th>
                <th class="px-2 py-2 text-left"></th>
                <th class="px-2 py-2 text-left"></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($allProducts as $product)
              <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                <td class="px-2 py-2 hidden sm:table-cell">
                  @php
                    $imagePath = 'storage/products/' . $product->photo;
                    $fullImagePath = public_path($imagePath);
                  @endphp

                  @if ($product->photo && file_exists($fullImagePath))
                    <img src="{{ asset($imagePath) }}" 
                         alt="Photo of {{ $product->name }}" 
                         class="h-20 w-20 object-cover" />
                  @else
                    <span class="text-gray-400">No photo</span>
                  @endif
                </td>
                
                <td class="px-2 py-2 text-left">
                  <span @if($product->trashed()) class="text-red-600 font-bold" @endif>
                    {{ $product->name }}
                  </span>
                </td>
                
                <td class="px-2 py-2 text-left">
                  {{ $product->category->name ?? '—' }}
                </td>
                
                <td class="px-2 py-2 text-left">{{ $product->price }}</td>
                
                <td class="px-2 py-2 text-left hidden sm:table-cell">{{ $product->stock }}</td>
                
                <td class="px-2 py-2 text-left hidden sm:table-cell">{{ $product->description_translated }}</td>
                
                <td class="px-2 py-2 text-center">
                  <a href="{{ route('products.show', ['product' => $product]) }}" title="View">
                    <flux:icon.eye class="size-5 hover:text-gray-600" />
                  </a>
                </td>
                
                <td class="px-2 py-2 text-center">
                  <a href="{{ route('products.edit', ['product' => $product]) }}" title="Edit">
                    <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                  </a>
                </td>
                
                <td class="px-2 py-2 text-center">
                  <form method="POST" action="{{ route('products.destroy', ['product' => $product]) }}" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Delete">
                      <flux:icon.trash class="size-5 hover:text-red-600" />
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-4">
          {{ $allProducts->links() }}
        </div>
      </div>
    </div>
  </div>
</x-layouts.main-content>

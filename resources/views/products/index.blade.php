
<x-layouts.main-content :title="__('Products')"
                        heading="List of Products">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl ">
    <div class="flex justify-start ">
      <div class="my-4 p-6 ">
        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table-auto border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left hidden sm:table-cell">Photo</th>
                <th class="px-2 py-2 text-left">Name</th>
                <th class="px-2 py-2 text-left">Category</th>
                <th class="px-2 py-2 text-left">Price</th>
                <th class="px-2 py-2 text-right hidden sm:table-cell">Description</th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($allProducts as $product)
              <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                <td class="px-2 py-2 text-right hidden sm:table-cell">
                 @if ($product->photo)
                  <img src="{{ asset('storage/products/' . $product->photo) }}" alt="Photo of {{ $product->name }}" class="h-10 w-10 rounded-full object-cover">
                @else
                  <span class="text-gray-400">No photo</span>
                @endif
                </td>
                <td class="px-2 py-2 text-left">{{ $product->name }}</td>
                <td class="px-2 py-2 text-left">{{ $product->category}}</td>
                <td class="px-2 py-2 text-left hidden sm:table-cell">{{  $product->price }}</td>
                <td class="px-2 py-2 text-right hidden sm:table-cell">{{ $product->description }}</td>
                
                
                <td class="ps-2 px-0.5">
                  <a href="{{ route('products.show', ['product' => $product]) }}">
                    <flux:icon.eye class="size-5 hover:text-gray-600" />
                  </a>
                </td>
                <td class="px-0.5">
                  <a href="{{ route('products.edit', ['product' => $product]) }}">
                    <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                  </a>
                </td>
                <td class="px-0.5">
                  <form method="POST" action="{{ route('products.destroy', ['product' => $product]) }}" class="flex items-center">
                    @csrf
                    @method('DELETE')
                    <button type="submit">
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


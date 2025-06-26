<x-layouts.main-content :title="__('products')"
                        heading="List of products"
                        subheading="Manage all background products">

  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">

        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table w-full border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-3 py-2 text-left">ID</th>                
                <th class="px-3 py-2 text-left">Name</th>
                <th class="px-3 py-2 text-left">Stock</th>                
                <th class="px-3 py-2 text-left">Stock lower limit</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($products as $product)              
                <tr class="border-b border-b-gray-300 dark:border-b-gray-600">                
                  <td class="px-3 py-2">{{ $product->id }}</td>
                  <td class="px-3 py-2">{{ $product->name }}</td>
                  <td class="px-3 py-2">{{ $product->stock }}</td>
                  <td class="px-3 py-2">{{ $product->stock_lower_limit }}</td>
                </tr>
              @endforeach
               
            </tbody>
          </table>
        </div>

        <div class="mt-4">
          {{ $products->links() }}
        </div>
        
      </div>
    </div>
  </div>
</x-layouts.main-content>

<x-layouts.main-content :title="__('Categories')"
                        heading="List of Categories"
                        subheading="Manage the categories offered by the institution">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">    <div class="flex items-center justify-between gap-4 mb-4">
      <flux:button variant="primary" href="{{ route('categories.create') }}">Create a new category</flux:button>
    </div>
    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">        <x-categories.filter-card 
              :filterAction="route('categories.index')" 
              :resetUrl="route('categories.index')"
              :filterByName="$filterByName"
              :order="$orderName"
              :orderProducts="$orderProducts"
        />

        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table w-full border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-3 py-2 text-left">Photo</th>
                <th class="px-3 py-2 text-left">Name</th>
                <th class="px-3 py-2 text-left">Products</th>
                <th class="px-3 py-2 text-center"></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($allCategories as $category)
              <tr class="border-b border-b-gray-400 dark:border-b-gray-500">

              <td class="px-3 py-2">
                  @if (!empty($category->image) && file_exists(public_path('storage/categories/' . $category->image)))
                      <img src="{{ asset('storage/categories/' . $category->image) }}" 
                          alt="{{ $category->name }}" 
                          class="w-20 h-20 object-cover rounded" />
                  @else
                      <span class="text-gray-400 text-xs">No photo</span>
                  @endif
              </td>



                <td class="px-3 py-2 {{ $category->trashed() ? 'text-red-600 font-semibold' : '' }}">
                  {{ $category->name }}
                </td>
                {{-- <td class="px-3 py-2">{{ $category->type ?? '—' }}</td> --}}
                <td class="px-3 py-2">{{ $category->products_count ?? '0' }}</td>                <td class="px-3 py-2">
                  <div class="flex justify-end items-center gap-1 min-w-[120px]">
                    @if (!$category->trashed())
                      <a href="{{ route('categories.show', ['category' => $category]) }}" title="View" class="p-2 inline-flex items-center justify-center">
                        <flux:icon.eye class="size-5 hover:text-gray-600" />
                      </a>
                      <a href="{{ route('categories.edit', ['category' => $category]) }}" title="Edit" class="p-2 inline-flex items-center justify-center">
                        <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                      </a>
                      <form method="POST" action="{{ route('categories.destroy', ['category' => $category]) }}" class="inline-flex items-center justify-center">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2"
                                title="{{ $category->products_count > 0 ? 'Soft delete (has products)' : 'Permanent delete (no products)' }}">
                          @if ($category->products_count > 0)
                            <flux:icon.cube class="size-5 hover:text-orange-500" />
                          @else
                            <flux:icon.trash class="size-5 hover:text-red-600" />
                          @endif
                        </button>
                      </form>
                    @else
                      <div class="p-2 inline-flex items-center justify-center">
                        <flux:icon.eye class="size-5 text-gray-400" />
                      </div>
                      <div class="p-2 inline-flex items-center justify-center">
                        <flux:icon.pencil-square class="size-5 text-gray-400" />
                      </div>                      <form method="POST" action="{{ route('categories.restore', ['id' => $category->id]) }}" class="inline-flex items-center justify-center">
                        @csrf
                        <button type="submit" class="p-2" title="Restore category">
                          <flux:icon.arrow-path-rounded-square class="size-5 hover:text-green-600" />
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-4">
          {{ $allCategories->links() }}
        </div>
      </div>
    </div>
  </div>
</x-layouts.main-content>

<x-layouts.main-content :title="__('Categories')"
                        heading="List of Categories"
                        subheading="Manage the categories offered by the institution">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center gap-4 mb-4">
      <flux:button variant="primary" href="{{ route('categories.create') }}">Create a new category</flux:button>
    </div>
    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">
        <x-categories.filter-card 
              :filterAction="route('categories.index')" 
              :resetUrl="route('categories.index')" 
        />

        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table w-full border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-3 py-2 text-left">Photo</th>
                <th class="px-3 py-2 text-left">Name</th>
                {{-- <th class="px-3 py-2 text-left">Type</th> --}}
                <th class="px-3 py-2 text-left">Products</th>
                <th class="px-3 py-2 text-center"></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($allCategories as $category)
              <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                <td class="px-3 py-2">
                  @php
                    $imagePath = 'storage/categories/' . $category->image;
                    $fullImagePath = public_path($imagePath);
                  @endphp

                  @if ($category->image && file_exists($fullImagePath))
                    <img src="{{ asset($imagePath) }}" 
                         alt="Photo of {{ $category->name }}" 
                         class="h-20 w-20 rounded-full object-cover" />
                  @else
                    <span class="text-gray-400">No photo</span>
                  @endif
                </td>
                <td class="px-3 py-2 {{ $category->trashed() ? 'text-red-600 font-semibold' : '' }}">
                  {{ $category->name }}
                </td>
                {{-- <td class="px-3 py-2">{{ $category->type ?? '—' }}</td> --}}
                <td class="px-3 py-2">{{ $category->products_count ?? '0' }}</td>
                <td class="px-3 py-2 text-center">
                  <div class="flex justify-center items-center gap-2">
                    <a href="{{ route('categories.show', ['category' => $category]) }}" title="View">
                      <flux:icon.eye class="size-5 hover:text-gray-600" />
                    </a>
                    <a href="{{ route('categories.edit', ['category' => $category]) }}" title="Edit">
                      <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                    </a>

                    @if (!$category->trashed())
                      <form method="POST" action="{{ route('categories.destroy', ['category' => $category]) }}" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                title="{{ $category->products_count > 0 ? 'Soft delete (has products)' : 'Permanent delete (no products)' }}">
                          @if ($category->products_count > 0)
                            <flux:icon.cube class="size-5 hover:text-orange-500" />
                          @else
                            <flux:icon.trash class="size-5 hover:text-red-600" />
                          @endif
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

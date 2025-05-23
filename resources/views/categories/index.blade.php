
<x-layouts.main-content :title="__('Categories')"
                        heading="List of categories"
                        >
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl ">
    <div class="flex items-center gap-4 mb-4">
          <flux:button variant="primary" href="{{ route('categories.create') }}">Create a new category</flux:button>
        </div>
    <div class="flex justify-start ">
      <div class="my-4 p-6 ">
        <x-categories.filter-card 
              :filterAction="route('categories.index')" 
              :resetUrl="route('categories.index')" 
        />
    
        
        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table-auto border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left">Photo</th>
                <th class="px-2 py-2 text-left">Name</th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($allCategories as $category)
              <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                <td class="px-2 py-2 text-right">
                 @if ($category->image)
                 <img src="{{ asset('storage/categories/' . $category->image) }}" alt="Photo of {{ $category->name }}" class="h-10 w-10 rounded-full object-cover">

                @else
                  <span class="text-gray-400">No photo</span>
                @endif
                </td>
                <td class="{{ $category->trashed() ? 'text-red-600 font-semibold' : '' }}">
                    {{ $category->name }}
                </td>
             


                <td class="ps-2 px-0.5">
                  <a href="{{ route('categories.show', ['category' => $category]) }}" class="hover:text-gray-600">
                    <flux:icon.eye class="size-5" />
                  </a>
                </td>
                <td class="px-0.5">
                  
                  <a href="{{ route('categories.edit', ['category' => $category]) }}">
                    <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                  </a>
                  
                </td>
              <td class="px-0.5">
                  @if (!$category->trashed())
                      <form method="POST" action="{{ route('categories.destroy', parameters: ['category' => $category]) }}" class="flex items-center">
                          @csrf
                          @method('DELETE')
                          <button type="submit" title="Cancelar membership">
                              <flux:icon.cube class="size-5 hover:text-red-600" />
                          </button>
                      </form>
                  @endif
              </td>

                  <td class="px-0.5">
                    {{-- @if ($category->type === 'employee') --}}
                      <form method="POST" action="{{ route('categories.forceDestroy', parameters: ['category' => $category]) }}" class="flex items-center">
                          @csrf
                          @method('DELETE')
                          <button type="submit" title="Delete category">
                              <flux:icon.trash class="size-5 hover:text-red-600" />
                          </button>
                      </form>
                    {{-- @endif --}}
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


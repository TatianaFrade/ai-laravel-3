<x-layouts.main-content :title="__('Products')" heading="List of Products">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center gap-4 mb-4">
            @if($userType === 'board')
                <flux:button variant="primary" href="{{ route('products.create') }}">
                    Create a new product
                </flux:button>
            @endif
        </div>

        <div class="flex justify-start">
            <div class="my-4 p-6 w-full">

                {{-- Filtros mantidos para todos --}}
                <x-products.filter-card :filterAction="route('categories.index')"
                    :resetUrl="route('categories.index')" />

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

                </div>

                <div class="mt-4">
                    {{ $allProducts->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.main-content>
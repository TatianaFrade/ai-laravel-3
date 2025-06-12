<x-layouts.main-content title="Favorite Products" heading="Your Favorite Products" subheading="Quick access to your preferred items">
    <div class="p-6 space-y-8">

        {{-- Navigation --}}
        <div class="flex gap-4 mb-6">
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">All Products</a>
            <a href="{{ route('favorites.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Favorites</a>
        </div>

        {{-- Favorite Products List --}}
        <div class="mt-10">
            <h2 class="text-xl font-bold mb-4 dark:text-white">⭐ Favorite Products</h2>

            @if ($favorites->isEmpty())
                <p class="text-gray-600 dark:text-gray-400">No products added to favorites yet.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($favorites as $favorite)
                        @include('components.products.card', ['product' => $favorite->product])
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-layouts.main-content>

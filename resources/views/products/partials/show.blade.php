<div class="max-w-2xl md:max-w-3xl mx-auto p-8 bg-gray-900 text-gray-100 shadow-xl rounded-md">
    <div class="flex flex-col items-center gap-6 md:gap-8">
        <!-- Product Image -->
        <div class="w-96 h-96 bg-gray-800 flex justify-center items-center rounded-md">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-88 h-88 object-cover rounded-md">
        </div>

        <!-- Product Details -->
        <div class="w-full p-6 bg-gray-800 rounded-md text-center">
            <h1 class="text-4xl font-bold text-white">{{ $product->name }}</h1>
            <p class="text-gray-400 mt-4 text-base">{{ $product->description }}</p>

            <p class="mt-4 text-lg font-semibold text-blue-400">
                Category: {{ $product->category->name }}
            </p>

            <!-- Price and Discount -->
            <div class="mt-6 bg-gray-700 p-4 rounded-md">
                @if ($product->discount > 0 && $product->stock > $product->discount_min_qty)
                    <p class="text-lg font-semibold text-green-400">
                        Save €{{ number_format($product->discount, 2) }} 
                        ({{ number_format(($product->discount / $product->price) * 100, 0) }}% off)
                    </p>
                    <p class="text-xl font-bold text-gray-300">
                        Discounted Price: €{{ number_format($product->price - $product->discount, 2) }}
                    </p>
                @else
                    <p class="text-xl font-bold text-gray-300">
                        Price: €{{ number_format($product->price, 2) }}
                    </p>
                @endif
            </div>

            <!-- Add to Cart Button -->
            <div class="mt-6 text-center">
                <form method="POST" action="{{ route('cart.add', ['product' => $product]) }}">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white py-3 px-6 rounded-md hover:bg-green-700 transition duration-200">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
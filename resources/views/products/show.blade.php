<x-layouts.main-content :title="$product->name"
    heading="Product"
    subheading="Information about this product">
<div class="flex flex-col space-y-6">
<div class="max-full">
<section>
    <div class="mt-6 space-y-4">
        @include('products.partials.fields', ['mode' => 'show'])
    </div>
    
    <!-- Add to Cart Button -->
    <div class="mt-6">
        <form method="POST" action="{{ route('cart.add', ['product' => $product]) }}">
            @csrf
            <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">
                Add to Cart
            </button>
        </form>
    </div>
</section>
</div>
</div>
</x-layouts.main-content>



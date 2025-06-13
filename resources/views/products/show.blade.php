<x-layouts.main-content :title="$product->name" heading="Product" subheading="Information about this product">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <div class="mt-6 space-y-4">
                    @if ($mode === 'show')
                        @include('products.partials.show', ['product' => $product])
                    @else
                        @include('products.partials.fields', ['mode' => 'show'])
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-layouts.main-content>
<x-layouts.main-content :title="$product->name"
    heading="Product"
    subheading="Information about this product">
<div class="flex flex-col space-y-6">
<div class="max-full">
<section>
    

    <div class="mt-6 space-y-4">
        @include('products.partials.fields', ['mode' => 'show'])
    </div>
</form>
</section>
</div>
</div>
</x-layouts.main-content>



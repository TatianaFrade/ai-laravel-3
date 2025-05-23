<x-layouts.main-content :title="$product->name"
                        heading="Edit product"
                        :subheading="$product->name">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <div class="static sm:absolute -top-2 right-0 flex flex-wrap justify-start sm:justify-end items-center gap-4">
                    @can('create', App\Models\Product::class)
                        <flux:button variant="primary" href="{{ route('products.create') }}">New</flux:button>
                    @endcan
                    @can('view', $product)    
                        <flux:button href="{{ route('products.show', ['product' => $product]) }}">View</flux:button>
                    @endcan
                    @can('delete', $product)    
                        <form method="POST" action="{{ route('products.destroy', ['product' => $product]) }}">
                            @csrf
                            @method('DELETE')
                            <flux:button variant="danger" type="submit">Delete</flux:button>
                        </form>
                    @endcan
                </div>
                <form method="POST" action="{{ route('products.update', ['product' => $product]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mt-6 space-y-4">
                        @include('products.partials.fields', ['mode' => 'edit'])
                    </div>
                    <div class="flex mt-6">
                        <flux:button variant="primary" type="submit"  class="uppercase">Save</flux:button>
                        <flux:button class="uppercase ms-4" href="{{ url()->full() }}">Cancel</flux:button>
                    </div>
                </form>
            </section>
        </div>
    </div>
    <form class="hidden" id="form_to_delete_product_image"
        method="POST" 
        action="{{ route('products.image.destroy', ['product' => $product]) }}">
        @csrf
        @method('DELETE')
    </form>    
</x-layouts.main-content>

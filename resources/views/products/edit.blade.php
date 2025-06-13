<x-layouts.main-content :title="$product->name"
                        heading="{{ auth()->user()->type === 'employee' ? 'Update Stock' : 'Edit product' }}"
                        :subheading="$product->name">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                @if(auth()->user()->type === 'employee')
                    <form method="POST" action="{{ route('products.update', ['product' => $product]) }}" id="stock-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="mt-6 space-y-4">
                            <!-- For employees, only show the stock field and make others readonly -->
                            <div class="border p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <h3 class="text-lg font-medium mb-2">Stock Update</h3>
                                
                                <div class="w-full sm:w-96 mb-4">
                                    <flux:input name="name" label="Product Name" value="{{ $product->name }}" readonly />
                                </div>
                                
                                <div class="w-full sm:w-96 mb-4">
                                    <flux:input name="stock" label="Stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" required placeholder="Enter new stock quantity" />
                                </div>
                                
                                <p class="mt-2 text-sm text-gray-600">
                                    All stock changes are recorded in the stock adjustments history.
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex mt-6">
                            <flux:button variant="primary" type="submit" class="uppercase">Update Stock</flux:button>
                            <flux:button class="uppercase ms-4" href="{{ route('products.index') }}">Cancel</flux:button>
                        </div>
                    </form>
                @else
                    <form method="POST" action="{{ route('products.update', ['product' => $product]) }}" enctype="multipart/form-data" id="product-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="mt-6 space-y-4">
                            @include('products.partials.fields', ['mode' => 'edit'])
                        </div>

                        <div class="flex mt-6">
                            <flux:button variant="primary" type="submit" class="uppercase">Save</flux:button>
                            <flux:button class="uppercase ms-4" href="{{ route('products.index') }}">Cancel</flux:button>
                        </div>
                    </form>
                @endif
            </section>
        </div>
    </div>
</x-layouts.main-content>

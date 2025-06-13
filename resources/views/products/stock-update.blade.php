<x-layouts.main-content :title="'Update Stock - ' . $product->name"
                        heading="Update Product Stock"
                        :subheading="$product->name">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <form method="POST" action="{{ route('products.updateStock', ['product' => $product]) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mt-6 space-y-4">
                        <div class="w-full sm:w-96">
                            <flux:input name="name" label="Product Name" value="{{ $product->name }}" readonly />
                        </div>
                        
                        <div class="w-full sm:w-96">
                            <flux:input name="current_stock" label="Current Stock" value="{{ $product->stock }}" readonly />
                        </div>
                        
                        <div class="w-full sm:w-96">
                            <flux:input name="stock" label="New Stock Quantity" value="{{ old('stock', $product->stock) }}" 
                                      type="number" step="1" required placeholder="Enter new stock quantity" />
                        </div>
                        
                        <div class="w-full sm:w-96">
                            <flux:input name="reason" label="Reason for Change" value="{{ old('reason') }}" 
                                      placeholder="Why are you changing the stock?" />
                        </div>
                    </div>

                    <div class="flex mt-6">
                        <flux:button variant="primary" type="submit" class="uppercase">Update Stock</flux:button>
                        <flux:button class="uppercase ms-4" href="{{ route('products.show', ['product' => $product]) }}">Cancel</flux:button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.main-content>

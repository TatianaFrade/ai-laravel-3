<x-layouts.main-content :title="$product->name"
                        heading="Edit product"
                        :subheading="$product->name">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <form method="POST" action="{{ route('products.update', ['product' => $product]) }}" enctype="multipart/form-data" id="product-form">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-900 text-red-300 rounded border border-red-700 shadow-md">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-6 space-y-4">
                        @include('products.partials.fields', ['mode' => 'edit'])
                    </div>

                    <div class="flex mt-6">
                        <flux:button variant="primary" type="submit" class="uppercase">Save</flux:button>
                        <flux:button class="uppercase ms-4" href="{{ url()->full() }}">Cancel</flux:button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    {{-- validar que desconto só pode se stock >= quantidade mínima --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('product-form');
            const stockInput = document.getElementById('stock');
            const minQtyInput = document.getElementById('discount_min_qty');
            const discountInput = document.getElementById('discount');

            form.addEventListener('submit', function(e) {
                const stock = parseInt(stockInput.value) || 0;
                const minQty = parseInt(minQtyInput.value) || 0;
                const discount = discountInput.value;

                if (discount && stock < minQty) {
                    alert('Desconto só pode ser aplicado se o stock for maior ou igual à quantidade mínima para desconto.');
                    e.preventDefault();
                    discountInput.focus();
                }
            });
        });
    </script> -->

</x-layouts.main-content>

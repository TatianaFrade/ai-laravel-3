<x-layouts.main-content title="Cart"
                        heading="Shopping Cart"
                        subheading="Product to register for a User">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl ">
        <div class="flex justify-start ">
            @empty($cart)
                {{-- <div class="flex items-center justify-center w-full h-full"> --}}
                <div class="my-4 p-6 ">    
                    <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-300">Your cart is empty</h2>
                </div>
            @else
                <div class="my-4 p-6 ">
                    <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
                        <x-products.table
                            :products="$cart"
                            :showView="false"
                            :showEdit="false"
                            :showDelete="false"
                            :showAddToCart="false"
                            :showRemoveFromCart="true"
                            />
                    </div>
                    <div class="mt-12">
                        <div>
                            <h3 class="mb-4 text-xl">Shopping Cart Confirmation </h3>
                        </div>
                        <div class="flex justify-between items-start space-x-4">
                            <form action="{{ route('cart.confirm') }}" method="post" class="flex space-x-4">
                                @csrf
                                <flux:input name="user_number" label="User Number" value="{{ old('user_number') }}"/>
                                <flux:button variant="primary" type="submit" class="mt-[1.7rem]">Confirm</flux:button>
                            </form>
                            <form action="{{ route('cart.destroy') }}" method="post">
                                @csrf
                                @method('DELETE')
                                <flux:button variant="danger" type="submit" class="mt-[1.7rem]">Clear Cart</flux:button>
                            </form>
                        </div>
                    </div>
                </div>
            @endempty
        </div>
    </div>
</x-layouts.main-content>

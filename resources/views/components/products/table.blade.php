<div {{ $attributes }}>
    <table class="table-auto border-collapse">
        @php
            use Illuminate\Support\Facades\Auth;

            $userType = Auth::user()->type ?? 'guest';
        @endphp

        <thead>
            <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left">Photo</th>
                <th class="px-2 py-2 text-left">Name</th>
                @if (!$isCart)
                    <th class="px-2 py-2 text-left hidden md:table-cell">Category</th>
                @endif
                <th class="px-2 py-2 text-left whitespace-nowrap hidden sm:table-cell">Unit Price</th>
                <th class="px-2 py-2 text-left hidden sm:table-cell">Discount</th>
                @if ($isCart)
                    <th class="px-2 py-2 text-left hidden sm:table-cell">Total Price</th>
                @endif
                <td class="px-2 py-2 text-left hidden sm:table-cell">{{ $isCart ? 'Quantity' : 'Stock' }}</td>
                @if (!$isCart)
                    <th class="px-2 py-2 text-left hidden sm:table-cell">Description</th>
                @endif
                @if($showView) <th></th> @endif
                @if($showEdit) <th></th> @endif
                @if($showDelete) <th></th> @endif
                @if ($isCart)
                    <th></th><th></th>
                @endif
                @if($showAddToCart) <th></th> @endif
                @if($showRemoveFromCart) <th></th> @endif
                @if ($userType === 'employee') <th></th> @endif
            </tr>
        </thead>

        <tbody>
            @foreach ($products as $product)
                @php
                    $hasDiscount = $product->discount && $product->discount_min_qty < $product->stock;
                    $priceAfterDiscount = $hasDiscount ? $product->price - $product->discount : $product->price;
                    $totalPrice = $priceAfterDiscount * $product->quantity;
                @endphp
                <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                    {{-- Photo --}}
                    <td class="px-2 py-2 hidden sm:table-cell">
                            
                     <div class="h-20 w-20 rounded-full object-cover">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded" />
                    </div>

                    {{-- Name --}}
                   <td class="px-2 py-2 text-left">
                        <span class="{{ $product->trashed() ? 'text-red-600 font-semibold' : ($userType !== 'board' && $hasDiscount ? 'text-green-700 font-semibold' : '') }}">
                            {{ $product->name }}
                        </span>
                    </td>


                    @if (!$isCart)
                        <td class="px-2 py-2 text-left">{{ $product->category->name ?? '—' }}</td>
                    @endif

                    {{-- Unit Price --}}
                    <td class="px-2 py-2 text-left whitespace-nowrap hidden sm:table-cell {{ $hasDiscount && $userType !== 'board' ? 'text-green-700 font-semibold' : '' }}">
                        {{ number_format($userType === 'board' ? $product->price : $priceAfterDiscount, 2) }} €
                    </td>

                    {{-- Discount --}}
                    {{-- Discount --}}
                        <td class="px-2 py-2 text-left hidden sm:table-cell">
                            {{ $product->discount ? number_format($product->discount, 2) . ' €' : '—' }}
                        </td>


                    {{-- Total in Cart or Stock --}}
                    <td class="px-2 py-2 text-left whitespace-nowrap hidden sm:table-cell">
                        @if ($isCart)
                            {{ $product->quantity }}
                            @if ($product->quantity > $product->stock)
                                <span class="text-red-600 ms-2">(stock insuficiente)</span>
                            @endif
                        @else
                            @if ($userType === 'board')
                                {{ $product->stock }}
                            @else
                                {{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}
                            @endif
                        @endif
                    </td>

                    {{-- Description --}}
                    @if (!$isCart)
                        <td class="px-2 py-2 text-left hidden sm:table-cell">{{ $product->description_translated }}</td>
                    @endif

                    {{-- Actions --}}
                    @if($showView)
                        <td class="ps-2 px-0.5">
                            <a href="{{ route('products.show', $product) }}">
                                <flux:icon.eye class="size-5 hover:text-green-600" />
                            </a>
                        </td>
                    @endif

                   @if ($showEdit)
                    <td class="px-0.5">
                        <a href="{{ route('products.edit', ['product' => $product]) }}">
                        <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                        </a>
                    </td>
                    @endif

                    @if($showDelete)
                        <td class="px-0.5">
                            <form method="POST" action="{{ route('products.destroy', $product) }}">
                                @csrf @method('DELETE')
                                <button type="submit">
                                    <flux:icon.trash class="size-5 hover:text-red-600" />
                                </button>
                            </form>
                        </td>
                    @endif

                    @if ($isCart)
                        <td class="pl-4">
                            <form method="POST" action="{{ route('cart.decrease', $product) }}">
                                @csrf
                                <button type="submit">
                                    <flux:icon.minus-circle class="size-5 hover:text-green-600" />
                                </button>
                            </form>
                        </td>
                        <td class="pl-4">
                            <form method="POST" action="{{ route('cart.increase', $product) }}">
                                @csrf
                                <button type="submit">
                                    <flux:icon.plus-circle class="size-5 hover:text-green-600" />
                                </button>
                            </form>
                        </td>
                    @endif

                    @if($showAddToCart)
                        <td class="pl-4">
                            <form method="POST" action="{{ route('cart.add', $product) }}">
                                @csrf
                                <button type="submit">
                                    <flux:icon.shopping-cart class="size-5 hover:text-green-600" />
                                </button>
                            </form>
                        </td>
                    @endif

                    @if($showRemoveFromCart)
                        <td class="pl-4">
                            <form method="POST" action="{{ route('cart.remove', $product) }}">
                                @csrf @method('DELETE')
                                <button type="submit">
                                    <flux:icon.trash class="size-5 hover:text-red-600" />
                                </button>
                            </form>
                        </td>
                    @endif


                </tr>
            @endforeach
        </tbody>
    </table>
</div>

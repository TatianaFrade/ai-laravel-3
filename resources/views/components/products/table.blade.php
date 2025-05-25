<div {{ $attributes }}>
    <table class="table-auto border-collapse">
        @php
            $userType = session('userType', 'pending_member'); 
        @endphp
        <thead>
            <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left">Photo</th>
                <th class="px-2 py-2 text-left">Name</th>
                <th class="px-2 py-2 text-left  hidden md:table-cell">Category</th>
                <th class="px-2 py-2 text-left hidden sm:table-cell">Price</th>
                <th class="px-2 py-2 text-left hidden sm:table-cell">Discount</th>
                <th class="px-2 py-2 text-left hidden sm:table-cell">Stock</th>
                <th class="px-2 py-2 text-left hidden sm:table-cell">Description</th>
                @if($showView)
                    <th></th>
                @endif
                @if($showEdit)
                    <th></th>
                @endif
                @if($showDelete)
                    <th></th>
                @endif
                @if($showAddToCart)
                    <th></th>
                @endif
                @if($showRemoveFromCart)
                    <th></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                @php
                    $hasDiscount = $product->discount && $product->discount > 0;
                    $priceAfterDiscount = $hasDiscount
                        ? $product->price - $product->discount
                        : $product->price;
                @endphp
                <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                    {{-- Photo --}}
                    <td class="px-2 py-2 hidden sm:table-cell">
                        @php
                            $imagePath = 'storage/products/' . $product->photo;
                            $fullImagePath = public_path($imagePath);
                        @endphp
                        @if ($product->photo && file_exists($fullImagePath))
                            <img src="{{ asset($imagePath) }}" alt="Photo of {{ $product->name }}"
                                class="h-20 w-20 object-cover" />
                        @else
                            <span class="text-gray-400">No photo</span>
                        @endif
                    </td>

                    {{-- Name --}}
                    <td
                        class="px-2 py-2 text-left {{ (!$userType === 'board' && $hasDiscount) ? 'text-green-700 font-semibold' : '' }}">
                        @if ($userType !== 'board' && $hasDiscount)
                            <span class="text-green-700 font-semibold">{{ $product->name }}</span>
                        @else
                            {{ $product->name }}
                        @endif
                    </td>

                    {{-- Category --}}
                    <td class="px-2 py-2 text-left">{{ $product->category->name ?? '—' }}</td>

                    {{-- Price --}}
                    @if ($userType === 'board')
                        <td class="px-2 py-2 text-left">{{ number_format($product->price, 2) }}€</td>
                    @else
                        <td class="px-2 py-2 text-left {{ $hasDiscount ? 'text-green-700 font-semibold' : '' }}">
                            {{ number_format($priceAfterDiscount, 2) }} €
                        </td>
                    @endif

                    {{-- Discount --}}
                    @if ($userType === 'board')
                        <td class="px-2 py-2 text-left hidden sm:table-cell">
                            {{ $product->discount ? number_format($product->discount, 2) . ' €' : '—' }}
                        </td>
                    @else
                        <td class="px-2 py-2 text-left hidden sm:table-cell">
                            {{ $hasDiscount ? number_format($product->discount, 2) . ' €' : '—' }}
                        </td>
                    @endif

                    {{-- Stock --}}
                    @if ($userType === 'board')
                        <td class="px-2 py-2 text-left hidden sm:table-cell">{{ $product->stock }}</td>
                    @else
                        <td class="px-2 py-2 text-left hidden sm:table-cell">
                            {{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}
                        </td>
                    @endif


                    <td class="px-2 py-2 text-left hidden sm:table-cell">
                        {{ $product->description_translated }}
                    </td>

                    @if($showView)
                        <td class="ps-2 px-0.5">
                            <a href="{{ route('products.show', ['product' => $product]) }}">
                                <flux:icon.eye class="size-5 hover:text-green-600" />
                            </a>
                        </td>
                    @endif

                    @if($showEdit)
                        <td class="px-0.5">
                            <a href="{{ route('products.edit', ['product' => $product]) }}">
                                <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                            </a>
                        </td>
                    @endif
                    @if($showDelete)
                        <td class="px-0.5">
                            <form method="POST" action="{{ route('products.destroy', ['product' => $product]) }}"
                                class="flex items-center">
                                @csrf
                                @method('DELETE')
                                <button type="submit">
                                    <flux:icon.trash class="size-5 hover:text-red-600" />
                                </button>
                            </form>
                        </td>
                    @endif


                    @if($showAddToCart)
                        <td class="pl-4">
                            <form method="POST" action="{{ route('cart.add', ['product' => $product]) }}"
                                class="flex items-center">
                                @csrf
                                <button type="submit">
                                    <flux:icon.shopping-cart class="size-5 hover:text-green-600" />
                                </button>
                            </form>
                        </td>
                    @endif
                    @if($showRemoveFromCart)
                        <td class="pl-4">
                            <form method="POST" action="{{ route('cart.remove', ['product' => $product]) }}"
                                class="flex items-center"></form>
                            @csrf
                            @method('DELETE')
                            <button type="submit">
                                <flux:icon.minus-circle class="size-5 hover:text-red-600" />
                            </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div {{ $attributes }}>
    <table class="table-auto border-collapse">
        {{-- User type should be passed as a prop --}}
 
        <thead>
            <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left">Photo</th>
                <th class="px-2 py-2 text-left">Name</th>
                @if (!$isCart)
                    <th class="px-2 py-2 text-left hidden md:table-cell">Category</th>
                @endif
                <th class="px-2 py-2 text-left whitespace-nowrap hidden sm:table-cell">Unit Price</th>
                <th class="px-2 py-2 text-left hidden sm:table-cell">Discount</th>
                @if (in_array($userType, ['board', 'employee']) && !$isCart)
                    <th class="px-2 py-2 text-left hidden sm:table-cell">Stock</th>
                @endif
                @if ($isCart)
                    <th class="px-2 py-2 text-left hidden sm:table-cell">Total Price</th>
                    <td class="px-2 py-2 text-left hidden sm:table-cell">Quantity</td>
                @endif
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
                <tr class="border-b border-b-gray-400 dark:border-b-gray-500 {{ (request('view') !== 'public' && $product->has_active_discount) ? ($product->stock <= $product->stock_lower_limit ? 'bg-amber-50 dark:bg-amber-900/20' : 'bg-green-50 dark:bg-green-900/20') : '' }}">
                    {{-- Photo --}}
                    <td class="px-2 py-2 hidden sm:table-cell">
                        <div class="h-20 w-20 rounded-full object-cover relative">
                            @if($product->has_active_discount)
                                <div class="absolute -top-2 -right-2 {{ (request('view') !== 'public' && $product->stock <= $product->stock_lower_limit) ? 'bg-amber-600' : 'bg-green-600' }} text-white text-xs font-bold px-2 py-1 rounded-full shadow-md z-10">
                                    -{{ number_format($product->discount_percentage, 0) }}%
                                </div>
                            @endif
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded {{ $product->has_active_discount && request('view') !== 'public' ? ($product->stock <= $product->stock_lower_limit ? 'border-2 border-amber-500' : 'border-2 border-green-500') : '' }}" />
                        </div>
                    </td>
 
                    {{-- Name --}}
                    <td class="px-2 py-2 text-left">
                        <span class="{{ $product->trashed() ? 'text-red-600 font-semibold' : (request('view') !== 'public' && $userType !== 'board' && $product->has_active_discount ? ($product->stock <= $product->stock_lower_limit ? 'text-amber-700 font-semibold' : 'text-green-700 font-semibold') : '') }}">
                            {{ $product->name }}
                        </span>
                        @if($product->has_active_discount)
                            <div class="text-xs {{ request('view') !== 'public' && $product->stock <= $product->stock_lower_limit ? 'text-amber-600' : 'text-green-600' }} font-medium mt-1">
                                {{ request('view') !== 'public' && $product->stock <= $product->stock_lower_limit ? 'Low stock discount!' : 'Discounted!' }}
                            </div>
                        @endif
                    </td>
 
 
                    @if (!$isCart)
                        <td class="px-2 py-2 text-left">{{ $product->category->name ?? '—' }}</td>
                    @endif
 
                    {{-- Unit Price --}}
                    <td class="px-2 py-2 text-left whitespace-nowrap hidden sm:table-cell {{ $product->has_active_discount && $userType !== 'board' ? 'text-green-700 font-semibold' : '' }}">
                        @if($product->has_active_discount && $userType !== 'board')
                            <div class="font-semibold text-green-700">
                                {{ number_format($product->price_after_discount, 2) }} €
                            </div>
                            <div class="text-xs text-gray-500 line-through">
                                {{ number_format($product->price, 2) }} €
                            </div>
                        @else
                            {{ number_format($userType === 'board' ? $product->price : $product->price_after_discount, 2) }} €
                        @endif
                    </td>
 
                    {{-- Discount --}}
                    <td class="px-2 py-2 text-left hidden sm:table-cell">
                        @if($product->discount && $product->discount > 0)
                            @if($product->has_active_discount)
                                <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 px-2 py-1 rounded text-center">
                                    <div>{{ number_format($product->discount, 2) }} €</div>
                                    <div class="text-xs font-medium">{{ number_format($product->discount_percentage, 0) }}% off</div>
                                    @if($product->stock <= $product->stock_lower_limit)
                                        <div class="text-xs font-bold text-amber-600 mt-1">(Low stock discount)</div>
                                    @endif
                                </div>
                            @else
                                <div>{{ number_format($product->discount, 2) }} € <span class="text-xs text-amber-600">(not active)</span></div>
                            @endif
                        @else
                            —
                        @endif
                    </td>

                    {{-- Stock (only for board and employee) --}}
                    @if (in_array($userType, ['board', 'employee']) && !$isCart)
                        <td class="px-2 py-2 text-left whitespace-nowrap hidden sm:table-cell">
                            <div class="{{ $product->stock <= 0 ? 'text-red-600 font-medium' : ($product->stock <= $product->stock_lower_limit ? 'text-amber-600 font-medium' : 'text-green-50') }}">
                                {{ $product->stock }} units
                                @if($product->stock <= 0)
                                    <div class="text-xs text-red-600 font-medium">(Out of stock)</div>
                                @elseif($product->stock <= $product->stock_lower_limit)
                                    <div class="text-xs text-amber-600 font-medium">(Low stock)</div>
                                @endif
                            </div>
                        </td>
                    @endif
 
                    {{-- Quantity in Cart (only if isCart) --}}
                    @if ($isCart)
                    <td class="px-2 py-2 text-left whitespace-nowrap hidden sm:table-cell">
                        {{ $product->quantity }}
                        @if ($product->quantity > $product->stock)
                            <span class="text-red-600 ms-2">(stock insuficiente)</span>
                        @endif
                    </td>
                    @endif
 
                    {{-- Description --}}
                    @if (!$isCart)
                        <td class="px-2 py-2 text-left hidden sm:table-cell">{{ $product->description_translated }}</td>
                    @endif
 
                    {{-- Actions --}}
                    <td class="pr-2 py-2">
                        <div class="flex items-center space-x-2">
                            @if($showView)
                                @if(!$product->trashed())
                                    <a href="{{ route('products.show', $product) }}" class="inline-flex">
                                        <flux:icon.eye class="size-5 hover:text-green-600" />
                                    </a>
                                @else
                                    <div class="inline-flex" title="View (Read-only)">
                                        <flux:icon.eye class="size-5 text-gray-400" />
                                    </div>
                                @endif
                            @endif

                            @if($showEdit)
                                @if(!$product->trashed())
                                    <a href="{{ route('products.edit', ['product' => $product]) }}" class="inline-flex">
                                        <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                                    </a>
                                @else
                                    <div class="inline-flex" title="Edit (Read-only)">
                                        <flux:icon.pencil-square class="size-5 text-gray-400" />
                                    </div>
                                @endif
                            @endif

                            @if($showDelete)
                                @if($product->trashed())
                                    <form method="POST" action="{{ route('products.restore', ['id' => $product->id]) }}" class="inline-flex">
                                        @csrf
                                        <button type="submit" title="Restore product" class="inline-flex">
                                            <flux:icon.arrow-path-rounded-square class="size-5 hover:text-green-600" />
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline-flex" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Delete product" class="inline-flex">
                                            <flux:icon.trash class="size-5 hover:text-red-600" />
                                        </button>
                                    </form>
                                @endif
                            @endif

                            @if($isCart)
                                <form method="POST" action="{{ route('cart.decrease', $product) }}" class="inline-flex">
                                    @csrf
                                    <button type="submit" class="inline-flex">
                                        <flux:icon.minus-circle class="size-5 hover:text-green-600" />
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('cart.increase', $product) }}" class="inline-flex">
                                    @csrf
                                    <button type="submit" class="inline-flex">
                                        <flux:icon.plus-circle class="size-5 hover:text-green-600" />
                                    </button>
                                </form>
                            @endif

                            @if($showAddToCart)
                                <form method="POST" action="{{ route('cart.add', $product) }}" class="inline-flex">
                                    @csrf
                                    <button type="submit" class="inline-flex">
                                        <flux:icon.shopping-cart class="size-5 hover:text-green-600" />
                                    </button>
                                </form>
                            @endif

                            @if($showRemoveFromCart)
                                <form method="POST" action="{{ route('cart.remove', $product) }}" class="inline-flex">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex">
                                        <flux:icon.trash class="size-5 hover:text-red-600" />
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

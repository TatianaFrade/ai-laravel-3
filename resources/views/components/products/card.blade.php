<div class="rounded-xl shadow p-4 border text-sm flex flex-col h-full>
{{-- <div class=" relative rounded-xl shadow p-4 border text-sm flex flex-col h-full> para por a etiqueta da
    promocao--}}

    {{ request('view') === 'public'
    ?
    // Vista pública: aqui poderás alterar o fundo se o produto tiver desconto ativo
    // Exemplo alternativo:
    // ($product->discount && $product->discount > 0 && $product->discount_min_qty < $product->stock
    //     ? 'bg-green-50 dark:bg-green-900'
    //     : 'bg-white dark:bg-gray-900')
    
    
    'bg-white dark:bg-gray-900'

    : (


        ($product->discount && $product->discount > 0 && $product->discount_min_qty < $product->stock)
        ?
        // Vista privada: produto com desconto ativo
        // Para alterar o fundo nesta condição, substitua a classe abaixo
        // Exemplo: 
        //'relative border-pink-600 border-2 bg-green-50 dark:bg-amber-700'
        'relative border-pink-600 border-2 bg-white dark:bg-gray-900'



        : ($product->stock <= 0 ?
            // Vista privada: produto sem stock 
            'relative border-red-500 border-2 bg-white dark:bg-gray-900'
            : ($product->stock_lower_limit && $product->stock <= $product->stock_lower_limit
                ?
                // Vista privada: stock abaixo do limite inferior
                'relative border-amber-500 border-2 bg-white dark:bg-gray-900'
                :
                // Vista privada: produto sem condições especiais
                'bg-white dark:bg-gray-900'))
    )
    }}">


    {{-- Etiqueta em cima dos produtos com desconto --}}
    {{-- para adicionar a etiqueta basta ir à linha 1 e adicionar 'relative' ao inicio da linha --}}

    @if($product->has_active_discount)
        <div
            class="absolute -top-3 -right-3 bg-green-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow-md transform rotate-12">
            DESCONTO
        </div>
    @endif




    {{-- Imagem do produto --}}
    <div class="w-full h-48 mb-3">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded" />
    </div>

    {{-- Nome e categoria --}}

    {{-- produtos com desconto com o nome a verde --}}

    {{-- <div title="{{ e($product->description) }}" class="text-sm font-semibold mb-1 {{ $product->discount && $product->discount > 0 && $product->discount_min_qty < $product->stock ? 'text-green-600' : 'text-white' }}">
        {{ $product->name }}</div> --}}



    <div title="{{ e($product->description) }}" class="text-sm font-semibold mb-1 ">{{ $product->name }}</div>
    
    <div class="text-xs text-gray-600 mb-2">{{ $product->category->name ?? '—' }}</div>

    {{-- Preço e desconto --}}
    <div class="h-[4.5rem]"> {{-- Altura fixa para manter consistência, aumentada para acomodar texto de desconto --}}
        <div
            class="text-sm font-bold {{ $product->discount && $product->discount > 0 && $product->discount_min_qty < $product->stock ? 'text-green-600' : 'text-white' }}">
            {{ number_format($product->price - ($product->discount && $product->discount_min_qty < $product->stock ? $product->discount : 0), 2) }}
            €
        </div>
        @if ($product->discount && $product->discount > 0 && $product->discount_min_qty < $product->stock)
            <div class="text-xs text-gray-500 line-through mb-1">
                {{ number_format($product->price, 2) }} €
            </div>
            <div
                class="text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 px-1 py-0.5 rounded inline-block">
                Save {{ number_format($product->discount, 2) }}€
                ({{ number_format(($product->discount / $product->price) * 100, 0) }}% off)
            </div>
        @endif
    </div>



    {{-- Descrição --}}
    <div class="text-xs text-gray-700 mt-2 flex-grow">
        {{ Str::limit($product->description_translated, 100) }}
    </div>

    {{-- Botão adicionar ao carrinho --}}
    
    {{-- apenas para produtos que têm stock --}}
    {{-- @if($product->stock) --}} 
        <form method="POST" action="{{ route('cart.add', ['product' => $product]) }}" class="mt-4">
            @csrf
            <button type="submit" class="bg-green-600 text-white py-1.5 px-3 text-sm rounded hover:bg-green-700 w-full">
                Add to cart
            </button>
        </form>
    {{-- @endif --}}

    
    {{-- Botão remover do carrinho (só se estiver no carrinho) --}}
    {{-- @if($cart->contains('id', $product->id))
        <form method="POST" action="{{ route('cart.remove', ['product' => $product]) }}">
            @csrf
            @method('DELETE')
            <button type="submit">
                <flux:icon.minus-circle class="size-5 hover:text-red-600" />
            </button>
        </form>
    @endif  --}}

</div>
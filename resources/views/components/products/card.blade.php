<div class="rounded-xl shadow p-4 border bg-white dark:bg-gray-900 text-sm flex flex-col h-full">

    {{-- Imagem do produto --}}
    <div class="w-full h-48 mb-3">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded" />
    </div>

    {{-- Nome e categoria --}}
    <div class="text-sm font-semibold mb-1">{{ $product->name }}</div>
    <div class="text-xs text-gray-600 mb-2">{{ $product->category->name ?? '—' }}</div>

    {{-- Preço e desconto --}}
    <div class="h-[3.5rem]"> {{-- Altura fixa para manter consistência --}}
      <div class="text-sm font-bold {{ $product->discount ? 'text-green-600' : 'text-white' }}">
          {{ number_format($product->price - ($product->discount ?? 0), 2) }} €
      </div>
      @if ($product->discount)
          <div class="text-xs text-gray-500 line-through">
              {{ number_format($product->price, 2) }} €
          </div>
      @endif
    </div>

    {{-- Descrição --}}
    <div class="text-xs text-gray-700 mt-2 flex-grow">
        {{ Str::limit($product->description_translated, 100) }}
    </div>

    {{-- Botão adicionar ao carrinho --}}
    <form method="POST" action="{{ route('cart.add', ['product' => $product]) }}" class="mt-4">
        @csrf
        <button type="submit" class="bg-green-600 text-white py-1.5 px-3 text-sm rounded hover:bg-green-700 w-full">
            Add to cart
        </button>
    </form>

</div>

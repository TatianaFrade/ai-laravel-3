@php
    $mode = $mode ?? 'edit';
    $readonly = $mode === 'show';

    $disableName = $readonly;
    $disableImage = $readonly;
@endphp

{{-- Nome do Produto --}}
<flux:input 
    name="name" 
    label="{{ __('Name') }}" 
    value="{{ old('name', $product->name ?? '') }}" 
    :disabled="$readonly" 
    :placeholder="__('Required')" 
/>

{{-- Categoria (select com categorias existentes) --}}
<flux:select 
    name="category_id" 
    label="{{ __('Category') }}" 
    :disabled="$readonly" 
    required
>
    <option value="">-- {{ __('Select Category') }} --</option>
    @foreach ($categories as $category)
        <option 
            value="{{ $category->id }}" 
            {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}
        >
            {{ $category->name }}
        </option>
    @endforeach
</flux:select>

{{-- Preço --}}
<flux:input 
    name="price" 
    label="{{ __('Price') }}" 
    value="{{ old('price', $product->price ?? '') }}" 
    :disabled="$readonly" 
    :placeholder="__('Required')" 
    type="number"
    step="0.01"
/>

{{-- Descrição --}}
<flux:textarea 
    name="description" 
    label="{{ __('Description') }}" 
    :disabled="$readonly" 
>
    {{ old('description', $product->description_translated ?? '') }}
</flux:textarea>




<flux:input 
    name="stock" 
    label="{{ __('Stock Quantity') }}" 
    value="{{ old('stock', $product->stock ?? '') }}" 
    :disabled="$readonly" 
    :placeholder="__('Required')" 
    type="number"
    step="0.01"
/>

@if(isset($product) && isset($product->photo) && $product->photo)
    <img src="{{ asset('storage/products/' . $product->photo) }}" 
         alt="Product image" 
         class="h-24 w-24 object-cover rounded-full mb-4" />
@endif

<flux:input
    name="photo"
    label="{{ __('Product Photo') }}"
    type="file"
    accept="image/*"
    :disabled="$disableImage"
/>
@if ($disableImage)
    <input type="hidden" name="photo" value="{{ old('photo', $product->photo ?? '') }}">
@endif

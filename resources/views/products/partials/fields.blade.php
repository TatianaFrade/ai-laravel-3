@php
    $mode = $mode ?? 'edit';
    $readonly = $mode === 'show';
    $isCreate = $mode === 'create';
    $isEdit = $mode === 'edit';

    $userType = $userType ?? auth()->user()->type ?? 'guest';

    $canEditAll = !$readonly && $userType === 'board';
    $canEditStockOnly = !$readonly && $userType === 'employee';
    $forceReadonly = $readonly || ($userType !== 'board' && $userType !== 'employee');

@endphp

{{-- Nome do Produto --}}
<div class="w-full sm:w-96">
    @if ($forceReadonly || !$canEditAll)
        <flux:input name="name" label="{{ __('Name') }}" value="{{ old('name', $product->name ?? '') }}" readonly
            :placeholder="__('Required')" />
        <input type="hidden" name="name" value="{{ old('name', $product->name ?? '') }}">
    @else
        <flux:input name="name" label="{{ __('Name') }}" value="{{ old('name', $product->name ?? '') }}"
            :placeholder="__('Required')" />
    @endif
</div>

{{-- Categoria --}}
<div class="w-full sm:w-96">
    @if ($forceReadonly || !$canEditAll)
        <flux:select name="category_id" label="{{ __('Category') }}" :disabled="true">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </flux:select>
        <input type="hidden" name="category_id" value="{{ old('category_id', $product->category_id ?? '') }}">
    @else
        <flux:select name="category_id" label="{{ __('Category') }}" required>
            <option value="">-- {{ __('Select Category') }} --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </flux:select>
    @endif
</div>

{{-- Preço --}}
<div class="w-full sm:w-96">
    @if ($forceReadonly || !$canEditAll)
        <flux:input name="price" label="{{ __('Price') }}" value="{{ old('price', $product->price ?? '') }}" readonly
            type="number" step="0.01" :placeholder="__('Required')" />
        <input type="hidden" name="price" value="{{ old('price', $product->price ?? '') }}">
    @else
        <flux:input name="price" label="{{ __('Price') }}" value="{{ old('price', $product->price ?? '') }}" type="number"
            step="0.01" :placeholder="__('Required')" />
    @endif
</div>

{{-- Descrição --}}
<div class="w-full sm:w-96">
    @if ($forceReadonly || !$canEditAll)
        <flux:textarea name="description" label="{{ __('Description') }}" readonly>
            {{ old('description', $product->description_translated ?? '') }}
        </flux:textarea>
        <input type="hidden" name="description" value="{{ old('description', $product->description_translated ?? '') }}">
    @else
        <flux:textarea name="description" label="{{ __('Description') }}">
            {{ old('description', $product->description_translated ?? '') }}
        </flux:textarea>
    @endif
</div>

{{-- Stock --}}
<div class="w-full sm:w-96">
    @if ($readonly || !($canEditAll || $canEditStockOnly))
        <flux:input name="stock" label="{{ __('Stock Quantity') }}" value="{{ old('stock', $product->stock ?? '') }}"
            readonly type="number" step="1" :placeholder="__('Required')" />
        <input type="hidden" name="stock" value="{{ old('stock', $product->stock ?? '') }}">
    @else
        <flux:input name="stock" label="{{ __('Stock Quantity') }}" value="{{ old('stock', $product->stock ?? '') }}"
            type="number" step="1" :placeholder="__('Required')" />
    @endif
</div>

{{-- Stock Lower limit--}}
<div class="w-full sm:w-96">
    @if ($readonly || !($canEditAll || $canEditStockOnly))
        <flux:input name="stock_lower_limit" label="{{ __('Stock Lower Limit') }}" value="{{ old('stock_lower_limit', $product->stock_lower_limit ?? '') }}"
            readonly type="number" step="1" :placeholder="__('Required')" />
        <input type="hidden" name="stock" value="{{ old('stock', $product->stock_lower_limit ?? '') }}">
    @else
        <flux:input name="stock_lower_limit" label="{{ __('Stock Lower Limit') }}" value="{{ old('stock_lower_limit', $product->stock_lower_limit ?? '') }}"
            type="number" step="1" :placeholder="__('Required')" />
    @endif
</div>

{{-- Stock upper limit--}}
<div class="w-full sm:w-96">
    @if ($readonly || !($canEditAll || $canEditStockOnly))
        <flux:input name="stock_upper_limit" label="{{ __('Stock Upper Limit') }}" value="{{ old('stock_upper_limit', $product->stock_upper_limit ?? '') }}"
            readonly type="number" step="1" :placeholder="__('Required')" />
        <input type="hidden" name="stock" value="{{ old('stock', $product->stock ?? '') }}">
    @else
        <flux:input name="stock_upper_limit" label="{{ __('Stock Upper Limit') }}" value="{{ old('stock_upper_limit', $product->stock_upper_limit ?? '') }}"
            type="number" step="1" :placeholder="__('Required')" />
    @endif
</div>

{{-- Quantidade mínima para desconto --}}
<div class="w-full sm:w-96">
    @if ($forceReadonly || !$canEditAll)
        <flux:input name="discount_min_qty" label="{{ __('Minimum quantity for discount') }}"
            value="{{ old('discount_min_qty', $product->discount_min_qty ?? '') }}" type="number" step="1" min="0"
            readonly />
        <input type="hidden" name="discount_min_qty"
            value="{{ old('discount_min_qty', $product->discount_min_qty ?? '') }}">
    @else
        <flux:input name="discount_min_qty" label="{{ __('Minimum quantity for discount') }}"
            value="{{ old('discount_min_qty', $product->discount_min_qty ?? '') }}" type="number" step="1" min="0" />
    @endif
</div>

{{-- Desconto --}}
<div class="w-full sm:w-96">
    @if ($forceReadonly || !$canEditAll)
        <flux:input name="discount" label="{{ __('Discount') }}" value="{{ old('discount', $product->discount ?? '') }}"
            readonly type="number" step="0.01" />
        <input type="hidden" name="discount" value="{{ old('discount', $product->discount ?? '') }}">
    @else
        <flux:input name="discount" label="{{ __('Discount') }}" value="{{ old('discount', $product->discount ?? '') }}"
            type="number" step="0.01" />
    @endif
</div>




<div class="w-full sm:w-96">
    @if ($forceReadonly || !$canEditAll)
        <input type="hidden" name="photo" value="{{ old('photo', $product->photo ?? '') }}">
    @else
        <flux:input name="photo" label="{{ __('Product Photo') }}" type="file" accept="image/*" />
    @endif
</div> 
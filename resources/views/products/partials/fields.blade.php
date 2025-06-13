@php
    $mode = $mode ?? 'edit';
    $readonly = $mode === 'show';
    $isCreate = $mode === 'create';
    $isEdit = $mode === 'edit';

    $userType = auth()->user()->type ?? 'guest';

    $canEditAll = !$readonly && $userType === 'board';
    $canEditStockOnly = !$readonly && $userType === 'employee';
    $forceReadonly = $readonly || (!$canEditAll && !$canEditStockOnly);
    
    // For employees, make everything except stock readonly
    $isEmployeeEditing = $userType === 'employee' && $mode === 'edit';
@endphp



{{-- Name --}}
<div class="w-full sm:w-96">
    @if ($readonly || (!$canEditAll && !$canEditStockOnly))
        <flux:input name="name" label="Name" value="{{ old('name', $product->name ?? '') }}" readonly required placeholder="Required" />
        <input type="hidden" name="name" value="{{ old('name', $product->name ?? '') }}">
    @else
        <flux:input name="name" label="Name" value="{{ old('name', $product->name ?? '') }}" required placeholder="Required" />
    @endif
</div>

@if($canEditAll || $readonly)
    {{-- Category --}}
    <div class="w-full sm:w-96">
        @if ($forceReadonly || !$canEditAll)
            <flux:select name="category_id" label="Category" disabled>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </flux:select>
            <input type="hidden" name="category_id" value="{{ old('category_id', $product->category_id ?? '') }}">
        @else
            <flux:select name="category_id" label="Category" required placeholder="Required">
                <option value="">-- Select Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </flux:select>
        @endif
    </div>

    {{-- Price --}}
    <div class="w-full sm:w-96">
        @if ($forceReadonly || !$canEditAll)
            <flux:input name="price" label="Price" value="{{ old('price', $product->price ?? '') }}" readonly type="number" step="0.01" placeholder="Required" />
            <input type="hidden" name="price" value="{{ old('price', $product->price ?? '') }}">
        @else
            <flux:input name="price" label="Price" value="{{ old('price', $product->price ?? '') }}" type="number" step="0.01" required placeholder="Required" />
        @endif
    </div>

    {{-- Description --}}
    <div class="w-full sm:w-96 flex-grow">
        @if ($forceReadonly || !$canEditAll)
            <flux:textarea name="description" label="Description" readonly class="h-full" rows="10">{{ old('description', $product->description_translated ?? '') }}</flux:textarea>
            <input type="hidden" name="description" value="{{ old('description', $product->description_translated ?? '') }}">
        @else
            <flux:textarea name="description" label="Description" required placeholder="Required" class="h-full" rows="10">{{ old('description', $product->description_translated ?? '') }}</flux:textarea>
        @endif
    </div>
@endif

{{-- Stock --}}
<div class="w-full sm:w-96">
    @if ($readonly || (!$canEditAll && !$canEditStockOnly))
        <flux:input name="stock" label="Stock Quantity" value="{{ old('stock', $product->stock ?? '') }}" readonly type="number" step="1" placeholder="Required" />
        <input type="hidden" name="stock" value="{{ old('stock', $product->stock ?? '') }}">
    @else
        <flux:input name="stock" label="Stock Quantity" value="{{ old('stock', $product->stock ?? '') }}" type="number" step="1" required placeholder="Required" />
    @endif
</div>

@if($canEditAll || $readonly)
    {{-- Stock Lower Limit --}}
    <div class="w-full sm:w-96">
        @if ($forceReadonly || !$canEditAll)
            <flux:input name="stock_lower_limit" label="Stock Lower Limit" value="{{ old('stock_lower_limit', $product->stock_lower_limit ?? '') }}" readonly type="number" step="1" placeholder="Required" />
            <input type="hidden" name="stock_lower_limit" value="{{ old('stock_lower_limit', $product->stock_lower_limit ?? '') }}">
        @else
            <flux:input name="stock_lower_limit" label="Stock Lower Limit" value="{{ old('stock_lower_limit', $product->stock_lower_limit ?? '') }}" type="number" step="1" required placeholder="Required" />
        @endif
    </div>

    {{-- Stock Upper Limit --}}
    <div class="w-full sm:w-96">
        @if ($forceReadonly || !$canEditAll)
            <flux:input name="stock_upper_limit" label="Stock Upper Limit" value="{{ old('stock_upper_limit', $product->stock_upper_limit ?? '') }}" readonly type="number" step="1" placeholder="Required" />
            <input type="hidden" name="stock_upper_limit" value="{{ old('stock_upper_limit', $product->stock_upper_limit ?? '') }}">
        @else
            <flux:input name="stock_upper_limit" label="Stock Upper Limit" value="{{ old('stock_upper_limit', $product->stock_upper_limit ?? '') }}" type="number" step="1" required placeholder="Required" />
        @endif
    </div>

    {{-- Optional fields --}}
    <div class="w-full sm:w-96">
        @if ($forceReadonly || !$canEditAll)
            <flux:input name="discount_min_qty" label="Minimum quantity for discount" value="{{ old('discount_min_qty', $product->discount_min_qty ?? '') }}" readonly type="number" step="1" min="0" placeholder="Optional" />
            <input type="hidden" name="discount_min_qty" value="{{ old('discount_min_qty', $product->discount_min_qty ?? '') }}">
        @else
            <flux:input name="discount_min_qty" label="Minimum quantity for discount" value="{{ old('discount_min_qty', $product->discount_min_qty ?? '') }}" type="number" step="1" min="0" placeholder="Optional" />
        @endif
    </div>

    <div class="w-full sm:w-96">
        @if ($forceReadonly || !$canEditAll)
            <flux:input name="discount" label="Discount" value="{{ old('discount', $product->discount ?? '') }}" readonly type="number" step="0.01" placeholder="Optional" />
            <input type="hidden" name="discount" value="{{ old('discount', $product->discount ?? '') }}">
        @else
            <flux:input name="discount" label="Discount" value="{{ old('discount', $product->discount ?? '') }}" type="number" step="0.01" placeholder="Optional" />
        @endif
    </div>

    <div class="w-full sm:w-96">
        @if ($forceReadonly || !$canEditAll)
            <input type="hidden" name="photo" value="{{ old('photo', $product->photo ?? '') }}">
        @else
            <flux:input name="photo" label="Product Photo" type="file" accept="image/*" placeholder="Optional" />
        @endif
    </div>
@endif

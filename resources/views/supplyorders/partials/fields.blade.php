@php
    $mode = $mode ?? 'edit'; 
    $readonly = $mode === 'show';
    $isCreate = $mode === 'create';
    $isEdit = $mode === 'edit';

    $userType = $user->type ?? null;
    $canEdit = in_array($userType, ['employee', 'board']); 

    $forceReadonly = $readonly || ($isEdit && !$canEdit);

    $registeredUserIdReadonly = $isCreate || $isEdit || $forceReadonly;

    $dateValue = old('date', $supplyorder->date ?? now()->format('Y-m-d'));
    $registeredUserId = old('registered_by_user_id', auth()->id());
@endphp


<div class="w-full sm:w-96">
    <flux:input 
        name="product_id" 
        label="Product number" 
        value="{{ old('product_id', $supplyorder->product_id ?? '') }}" 
        :readonly="$forceReadonly" 
        :placeholder="__('Required')" 
    />
    @if ($forceReadonly)
        <input type="hidden" name="product_id" value="{{ old('product_id', $supplyorder->product_id ?? '') }}">
    @endif
</div>


<div class="w-full sm:w-96">
    <flux:input 
        name="registered_by_user_id" 
        label="Member number" 
        value="{{ $registeredUserId }}" 
        :readonly="$registeredUserIdReadonly" 
    />
    <input type="hidden" name="registered_by_user_id" value="{{ $registeredUserId }}">
</div>


@if ($isCreate || $readonly)
    <flux:input 
        name="status" 
        label="Status" 
        value="{{ old('status', $supplyorder->status ?? 'requested') }}" 
        readonly 
        :placeholder="__('Required')" 
    />
@elseif ($isEdit && isset($user) && ($user->type === 'employee' || $user->type === 'board'))
    <flux:select name="status" :label="__('Status')">
        <option value="requested" @selected(old('status', $supplyorder->status) === 'requested')>Requested</option>
        <option value="completed" @selected(old('status', $supplyorder->status) === 'completed')>Completed</option>
    </flux:select>
@endif


<div class="w-full sm:w-96">
    <flux:input 
        name="quantity" 
        label="Quantity" 
        value="{{ old('quantity', $supplyorder->quantity ?? '') }}" 
        :readonly="$forceReadonly" 
        :placeholder="__('Required')" 
    />
    @if ($forceReadonly)
        <input type="hidden" name="quantity" value="{{ old('quantity', $supplyorder->quantity ?? '') }}">
    @endif
</div>

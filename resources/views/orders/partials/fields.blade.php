@php
    $mode = $mode ?? 'edit'; // modos: create, edit, show
    $readonly = $mode === 'show';
    $isCreate = $mode === 'create';
    $isEdit = $mode === 'edit';

    $dateValue = old('date', $order->date ?? now()->format('Y-m-d'));
    $cancelReason = old('cancel_reason', $order->cancel_reason ?? '');
    $cancelReasonOther = old('cancel_reason_other', $order->cancel_reason_other ?? '');
@endphp

<div class="w-full sm:w-96">
    <flux:input 
        name="member_id" 
        label="Member number" 
        value="{{ old('member_id', $order->member_id ?? '') }}" 
        :disabled="$readonly" 
        :placeholder="__('Required')" 
    />
    @if ($readonly)
        <input type="hidden" name="member_id" value="{{ old('member_id', $order->member_id ?? '') }}">
    @endif
</div>

@if ($isCreate || $readonly)
    <flux:input 
        name="status" 
        label="Status" 
        value="{{ old('status', $order->status ?? 'pending') }}" 
        readonly 
        :placeholder="__('Required')" 
    />
@else
    @if ($user->type === 'employee')
        <flux:select name="status" :label="__('Status')">
            <option value="completed" @selected(old('status', $order->status) === 'completed')>Completed</option>
            <option value="pending" @selected(old('status', $order->status) === 'pending')>Pending</option>
        </flux:select>
    @elseif ($user->type === 'board')
        <flux:select name="status" :label="__('Status')">
            <option value="canceled" @selected($cancelReason !== '' && old('status', $order->status) === 'canceled')>Canceled</option>
            <option value="{{ $order->status }}" selected hidden>{{ ucfirst($order->status) }}</option>
        </flux:select>

        {{-- Mostrar dropdown e campo "other" com Alpine.js --}}
        @if ($isEdit)
            <div x-data="{ selected: '{{ $cancelReason }}' }" class="mt-4">
                <flux:select
                    name="cancel_reason"
                    label="Cancel Reason"
                    x-model="selected"
                >
                    <option value="" disabled selected>{{ __('Select a reason') }}</option>
                    <option value="excessive processing time" @selected($cancelReason === 'excessive processing time')>Excessive processing time</option>
                    <option value="cancellation request from the club member" @selected($cancelReason === 'cancellation request from the club member')>Cancellation request from the club member</option>
                    <option value="other" @selected($cancelReason === 'other')>Other</option>
                </flux:select>

                <div x-show="selected === 'other'" class="mt-2" style="display: none;">
                    <flux:input
                        name="cancel_reason_other"
                        label="Please specify"
                        value="{{ $cancelReasonOther }}"
                        :disabled="false"
                        placeholder="Enter custom reason"
                    />
                </div>
            </div>
        @endif
    @endif
@endif

<flux:input 
    name="date" 
    label="Date" 
    value="{{ $dateValue }}" 
    readonly 
    :placeholder="__('Required')" 
/>

<flux:input 
    name="total_items" 
    label="Total Items" 
    value="{{ old('total_items', $order->total_items ?? '') }}" 
    :disabled="$readonly" 
    :placeholder="__('Required')" 
/>

<flux:input 
    name="nif" 
    label="NIF" 
    value="{{ old('nif', $order->nif ?? '') }}" 
    :disabled="$readonly" 
    :placeholder="__('Required')" 
/>

<flux:input 
    name="delivery_address" 
    label="Address" 
    value="{{ old('delivery_address', $order->delivery_address ?? '') }}" 
    :disabled="$readonly" 
    :placeholder="__('Required')" 
/>

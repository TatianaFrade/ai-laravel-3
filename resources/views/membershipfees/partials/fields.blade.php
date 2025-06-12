@php
    $mode = $mode ?? 'edit'; 
    $isEdit = $mode === 'edit';
    $forceReadonly = $isEdit || $readonly;
@endphp





<div class="w-full sm:w-96">
    <flux:input 
        name="membership_fee" 
        label="Value to pay" 
        value="{{ $membershipfee->membership_fee }}" 
       
    />
</div>


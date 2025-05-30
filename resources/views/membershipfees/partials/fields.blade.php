@php
    $mode = $mode ?? 'edit'; 
    $isEdit = $mode === 'edit';
    $forceReadonly = $isEdit || $readonly;
@endphp


<div class="w-full sm:w-96">
    <flux:input 
        name="id" 
        label="ID" 
        value="{{ old('id', $membershipfee->id ?? '') }}"
        :readonly="$forceReadonly"  
    />
</div>


<div class="w-full sm:w-96">
    <flux:input 
        name="membership_fee" 
        label="Member number" 
        value="{{ $membershipfee->membership_fee }}" 
       
    />
</div>


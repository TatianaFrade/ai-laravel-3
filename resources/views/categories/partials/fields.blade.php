@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';

@endphp

<div class="w-full sm:w-96">
    <flux:input 
        name="name" 
        label="Name" 
        value="{{ old('name', $user->name) }}" 
        :disabled="$disableName" 
        :placeholder="__('Required')" 
    />

</div>



<flux:input
    name="image"
    label="{{ __('category Photo') }}"
    type="file"
    accept="image/*"
    :disabled="$disablePhoto"
/>
    
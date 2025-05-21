@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
@endphp

<div class="w-full sm:w-96">
    <flux:input name="name" label="name" value="{{ old('name', $user->name) }}"
        :disabled="$readonly" :readonly="$mode == 'edit'"/>
</div>


<flux:input name="name" label="Name" value="{{ old('name', $user->name) }}" :disabled="$readonly" />


<div class="flex flex-row sm:flex-row sm space-x-4">
    <div class="w-full">
        <flux:input name="email" label="Email" value="{{ old('email', $user->email) }}" :disabled="$readonly" />
    </div>
    <div class="w-full">
        <flux:input name="type" label="type" value="{{ old('type', $user->type) }}" :disabled="$readonly" />
    </div>
    <div class="w-full">
        <flux:input name="gender" label="gender" value="{{ old('gender', $user->gender) }}" :disabled="$readonly" />
    </div>
</div>

<flux:input name="delivery_address" label="delivery_address" value="{{ old('delivery_address', $user->delivery_address) }}" :disabled="$readonly" />

<flux:textarea name="nif" label="nif" :disabled="$readonly" :resize="$readonly ? 'none' : 'vertical'" rows="auto" >
    {{ old('objectives', $user->objectives) }}
</flux:textarea>
<flux:error name="objectives" />


@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
@endphp

<div class="w-full sm:w-96">
    <flux:input name="name" label="Name" value="{{ old('name', $user->name) }}" :disabled="$readonly" :placeholder="__('Required')" />
</div>

<div class="flex flex-row sm:flex-row sm space-x-4">
    <div class="w-full">
        <flux:input name="email" label="Email" value="{{ old('email', $user->email) }}" :disabled="$readonly" :placeholder="__('Required')" />
    </div>
    <div class="w-full">
        @if ($mode === 'create')
            <input name="type" value="employee" readonly>
        @else
            <flux:select wire:model="type" :label="__('Type of user')" required>
                <option value="Member" {{ old('type', $user->type) == 'member' ? 'selected' : '' }}>Member</option>
                <option value="Board" {{ old('type', $user->type) == 'board' ? 'selected' : '' }}>Board</option>
                <option value="Employee" {{ old('type', $user->type) == 'employee' ? 'selected' : '' }}>Employee</option>
            </flux:select>
        @endif
    </div>

    <div class="w-full">
       <flux:select name="gender" label="{{ __('Gender') }}" :disabled="$readonly" required>
            <option value="">-- {{ __('Select Gender') }} --</option>
            <option value="F" {{ old('gender', $user->gender) == 'F' ? 'selected' : '' }}>Feminino</option>
            <option value="M" {{ old('gender', $user->gender) == 'M' ? 'selected' : '' }}>Masculine</option>
            <option value="O" {{ old('gender', $user->gender) == 'O' ? 'selected' : '' }}>Other</option>
        </flux:select>
    </div>
</div>

<flux:input
    wire:model="password"
    :label="__('Password')"
    type="password"
    :required="$mode === 'create'"
    autocomplete="new-password"
    :placeholder="$mode === 'create' ? __('Required') : __('Leave empty to keep current password')"
    viewable
/>

<flux:input
    wire:model="password_confirmation"
    :label="__('Confirm password')"
    type="password"
    :required="$mode === 'create'"
    autocomplete="new-password"
    :placeholder="$mode === 'create' ? __('Required') : __('Leave empty to keep current password')"
    viewable
/>



<flux:input name="delivery_address" label="Personal address" value="{{ old('delivery_address', $user->delivery_address) }}" :disabled="$readonly" />

<flux:input name="nif" label="Nif" value="{{ old('nif', $user->nif) }}" :disabled="$readonly" />

<flux:input name="payment_details" label="Payment details" value="{{ old('payment_details', $user->payment_details) }}" :disabled="$readonly" />

<img src="{{ asset('storage/photos/' . $user->photo) }}" alt="User photo">





<flux:error name="objectives" />


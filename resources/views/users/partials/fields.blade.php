@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';

    $userType = $user->type ?? 'employee'; // caso user não tenha type

    $onlyTypeReadOnly = $userType === 'employee';

    // Controles individuais de disable para os campos
    $disableType = $onlyTypeReadOnly || $readonly;

    $disableName = $readonly;
    $disableEmail = $readonly;
    $disableGender = $readonly;
    $disableAddress = $readonly;
    $disableNif = $readonly;
    $disablePayment = $readonly;
    $disablePhoto = $readonly;
@endphp

<div class="w-full sm:w-96">
    <flux:input 
        name="name" 
        label="Name" 
        value="{{ old('name', $user->name) }}" 
        :disabled="$disableName" 
        :placeholder="__('Required')" 
    />
    @if ($disableName)
        <input type="hidden" name="name" value="{{ old('name', $user->name) }}">
    @endif
</div>

<div class="flex flex-row sm:flex-row sm space-x-4">
    <div class="w-full">
        <flux:input 
            name="email" 
            label="Email" 
            value="{{ old('email', $user->email) }}" 
            :disabled="$disableEmail" 
            :placeholder="__('Required')" 
        />
        @if ($disableEmail)
            <input type="hidden" name="email" value="{{ old('email', $user->email) }}">
        @endif
    </div>

    <div class="w-full">
        @if ($mode === 'create')
            <flux:input 
                label="Type of user" 
                value="Employee" 
                readonly 
                name="type" 
            />
            <input type="hidden" name="type" value="{{ old('type', 'employee') }}" />
        @elseif ($mode === 'edit')
            <flux:select 
                name="type" 
                :label="__('Type of user')" 
                :disabled="$disableType"
            >
                @if ($userType === 'board')
                    <option value="board" @selected(old('type', $user->type) === 'board')>Board</option>
                    <option value="member" @selected(old('type', $user->type) === 'member')>Member</option>
                    <option value="employee" @selected(old('type', $user->type) === 'employee')>Employee</option>
                @elseif ($userType === 'member')
                    <option value="member" @selected(old('type', $user->type) === 'member')>Member</option>
                    <option value="board" @selected(old('type', $user->type) === 'board')>Board</option>
                @else
                    <option value="employee" @selected(true)>Employee</option>
                @endif
            </flux:select>
            @if ($disableType)
                <input type="hidden" name="type" value="{{ old('type', $user->type) }}">
            @endif
        @endif
    </div>

    <div class="w-full">
        <flux:select 
            name="gender" 
            label="{{ __('Gender') }}" 
            :disabled="$disableGender" 
            required
        >
            <option value="">-- {{ __('Select Gender') }} --</option>
            <option value="F" {{ old('gender', $user->gender) == 'F' ? 'selected' : '' }}>Feminino</option>
            <option value="M" {{ old('gender', $user->gender) == 'M' ? 'selected' : '' }}>Masculine</option>
            <option value="O" {{ old('gender', $user->gender) == 'O' ? 'selected' : '' }}>Other</option>
        </flux:select>
        @if ($disableGender)
            <input type="hidden" name="gender" value="{{ old('gender', $user->gender) }}">
        @endif
    </div>
</div>

<flux:input
    wire:model="password"
    :label="__('Password')"
    type="password"
    :disabled="$readonly"
    :required="$mode === 'create'"
    autocomplete="new-password"
    :placeholder="$mode === 'create' ? __('Required') : __('Leave empty to keep current password')"
    viewable
/>

<flux:input
    wire:model="password_confirmation"
    :label="__('Confirm password')"
    type="password"
    :disabled="$readonly"
    :required="$mode === 'create'"
    autocomplete="new-password"
    :placeholder="$mode === 'create' ? __('Required') : __('Leave empty to keep current password')"
    viewable
/>

<flux:input
    name="delivery_address"
    label="Personal address"
    value="{{ old('delivery_address', $user->delivery_address) }}"
    :disabled="$disableAddress"
    placeholder="Optional"
/>
@if ($disableAddress)
    <input type="hidden" name="delivery_address" value="{{ old('delivery_address', $user->delivery_address) }}">
@endif

<flux:input
    name="nif"
    label="Nif"
    value="{{ old('nif', $user->nif) }}"
    :disabled="$disableNif"
    placeholder="Optional"
/>
@if ($disableNif)
    <input type="hidden" name="nif" value="{{ old('nif', $user->nif) }}">
@endif

<flux:input
    name="payment_details"
    label="Payment details"
    value="{{ old('payment_details', $user->payment_details) }}"
    :disabled="$disablePayment"
    placeholder="Optional"
/>
@if ($disablePayment)
    <input type="hidden" name="payment_details" value="{{ old('payment_details', $user->payment_details) }}">
@endif

<flux:input
    name="photo"
    label="{{ __('Profile Photo') }}"
    type="file"
    accept="image/*"
    :disabled="$disablePhoto"
/>
    
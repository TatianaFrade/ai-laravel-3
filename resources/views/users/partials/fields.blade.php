@php
    $mode = $mode ?? 'edit';
    $readonly = $mode === 'show';

    // Permissões baseadas nos campos editáveis
    function canEdit($field, $editableFields) {
        return in_array($field, $editableFields);
    }
@endphp

<div class="w-full sm:w-96">
    @if (canEdit('name', $editableFields))
        <flux:input name="name" label="Name" value="{{ old('name', $user->name) }}" :disabled="$readonly" placeholder="Required" />
    @else
        <flux:input name="name" label="Name" value="{{ old('name', $user->name) }}" disabled />
        <input type="hidden" name="name" value="{{ old('name', $user->name) }}">
    @endif
</div>

<div class="flex flex-row sm:space-x-4">
    <div class="w-full">
        @if (canEdit('email', $editableFields))
            <flux:input name="email" label="Email" value="{{ old('email', $user->email) }}" :disabled="$readonly" placeholder="Required" />
        @else
            <flux:input name="email" label="Email" value="{{ old('email', $user->email) }}" disabled />
            <input type="hidden" name="email" value="{{ old('email', $user->email) }}">
        @endif
    </div>

    <div class="w-full">
        @if ($mode === 'create')
            <flux:input label="Type of user" value="Employee" readonly name="type" />
            <input type="hidden" name="type" value="employee" />
        @elseif (canEdit('type', $editableFields))
            <flux:select name="type" :label="__('Type of user')" :disabled="$readonly">
                @if (Auth::user()->type === 'board')
                    @if ($user->type === 'board')
                        <option value="board" @selected(old('type', $user->type) === 'board')>Board</option>
                        <option value="employee" @selected(old('type', $user->type) === 'employee')>Employee</option>
                    @elseif ($user->type === 'member')
                        <option value="member" @selected(old('type', $user->type) === 'member')>Member</option>
                        <option value="board" @selected(old('type', $user->type) === 'board')>Board</option>
                    @else
                        <option value="employee" selected>Employee</option>
                    @endif
                @else
                    <option value="{{ $user->type }}" selected>{{ ucfirst($user->type) }}</option>
                @endif
            </flux:select>
        @else
            <flux:input label="Type of user" value="{{ ucfirst($user->type ?? 'employee') }}" readonly name="type" />
            <input type="hidden" name="type" value="{{ old('type', $user->type ?? 'employee') }}" />
        @endif
    </div>

    <div class="w-full">
        @if (canEdit('gender', $editableFields))
            <flux:select name="gender" label="{{ __('Gender') }}" :disabled="$readonly" required>
                <option value="">-- {{ __('Select Gender') }} --</option>
                <option value="F" @selected(old('gender', $user->gender) == 'F')>Feminino</option>
                <option value="M" @selected(old('gender', $user->gender) == 'M')>Masculino</option>
                <option value="O" @selected(old('gender', $user->gender) == 'O')>Outro</option>
            </flux:select>
        @else
            <flux:input label="{{ __('Gender') }}" value="{{ $user->gender }}" readonly name="gender" />
            <input type="hidden" name="gender" value="{{ old('gender', $user->gender) }}">
        @endif
    </div>
</div>

@if (canEdit('password', $editableFields))
    <flux:input name="password" label="{{ __('Password') }}" type="password" :disabled="$readonly"
        :required="$mode === 'create'" autocomplete="new-password"
        :placeholder="$mode === 'create' ? __('Required') : __('Leave empty to keep current password')" viewable />
    
    <flux:input name="password_confirmation" label="{{ __('Confirm password') }}" type="password" :disabled="$readonly"
        :required="$mode === 'create'" autocomplete="new-password"
        :placeholder="$mode === 'create' ? __('Required') : __('Leave empty to keep current password')" viewable />
@endif

@if (canEdit('default_delivery_address', $editableFields))
    <flux:input name="default_delivery_address" label="Personal address"
        value="{{ old('default_delivery_address', $user->default_delivery_address) }}" :disabled="$readonly" placeholder="Optional" />
@else
    <input type="hidden" name="default_delivery_address" value="{{ old('default_delivery_address', $user->default_delivery_address) }}">
@endif

@if (canEdit('nif', $editableFields))
    <flux:input name="nif" label="NIF" value="{{ old('nif', $user->nif) }}" :disabled="$readonly" placeholder="Optional" />
@else
    <input type="hidden" name="nif" value="{{ old('nif', $user->nif) }}">
@endif

@if (canEdit('payment_details', $editableFields))
    <flux:input name="payment_details" label="Payment details"
        value="{{ old('payment_details', $user->payment_details) }}" :disabled="$readonly" placeholder="Optional" />
@else
    <input type="hidden" name="payment_details" value="{{ old('payment_details', $user->payment_details) }}">
@endif

@if (canEdit('photo', $editableFields))
    <flux:input name="photo" label="{{ __('Profile Photo') }}" type="file" accept="image/*" :disabled="$readonly" />
@endif

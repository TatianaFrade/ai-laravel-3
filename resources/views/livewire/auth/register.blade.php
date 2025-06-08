<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Card;
use Illuminate\Validation\Rule;

new #[Layout('components.layouts.auth')] class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $gender = '';
  
    public ?string $default_delivery_address = null;
    public ?string $nif = null;
    public ?string $payment_details = null;
    public $photo = null; 

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')],  
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'gender' => ['required', 'string', 'max:255'],
            'default_delivery_address' => ['nullable', 'string', 'max:255'],
            'nif' => ['nullable', 'string', 'max:255'],
            'payment_details' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'], // até 2MB
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($this->photo) {
            $filename = $this->photo->getClientOriginalName();
            $storedPath = $this->photo->storeAs('users', uniqid() . '_' . $filename, 'public');
            $validated['photo'] = basename($storedPath); // guarda apenas o nome
        }


        event(new Registered(($user = User::create($validated))));


        Auth::login($user);

     
        $this->redirectIntended(route('verification.notice'), navigate: true);

    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />
        

        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />
        @error('email')
            <div class="text-red-500 text-sm">{{ $message }}</div>
        @enderror

        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable    
        />

        <flux:select wire:model="gender" :label="__('Gender')" required>
            <option value="">-- {{ __('Select Gender') }} --</option>
            <option value="F">{{ __('Female') }}</option>
            <option value="M">{{ __('Male') }}</option>
            <option value="O">{{ __('Other') }}</option>
        </flux:select>


        <flux:input
            wire:model="default_delivery_address"
            :label="__('Delivery Address')"
            type="text"
            autocomplete="street-address"
            :placeholder="__('Optional')"
        />

        <flux:input
            wire:model="nif"
            :label="__('NIF Number')"
            type="text"
            :placeholder="__('Optional')"
        />


        <flux:input
            wire:model="payment_details"
            :label="__('Payment Details')"
            type="text"
            :placeholder="__('Optional')"
        />

  
        <flux:input
            wire:model="photo"
            :label="__('Profile Photo')"
            type="file"
            accept="image/*"
        />

      

            {{-- tirar depois --}}
        @error('photo') 
            <div class="text-red-500 text-sm">{{ $message }}</div>
        @enderror


        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>

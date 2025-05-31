<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $gender = '';
    public string $default_delivery_address = '';
    public string $nif = '';
    public string $payment_details = '';
    public $profile_photo = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->gender = $user->gender ?? '';
        $this->default_delivery_address = $user->default_delivery_address ?? '';
        $this->nif = $user->nif ?? '';
        $this->payment_details = $user->payment_details ?? '';
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        if($user->isBoard() || $user->isRegular()){
            $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'gender' => ['nullable', 'in:F,M,O'],
            'default_delivery_address' => ['nullable', 'string', 'max:255'],
            'nif' => ['nullable', 'string', 'max:20'],
            'payment_details' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            if ($this->profile_photo) {
                $path = $this->profile_photo->store('profile-photos', 'public');
                $user->profile_photo = $path;
            }

            $user->save();

            $this->dispatch('profile-updated', name: $user->name);
        }

        if($user->isEmployee()){
             
        }
      
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" :readonly="auth()->user()->isEmployee()"/>

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" :readonly="auth()->user()->isEmployee()"/>

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

        <flux:select wire:model="gender" :label="__('Gender')" :disabled="auth()->user()->isEmployee()">
            <option value="">-- {{ __('Select Gender') }} --</option>
            <option value="F">{{ __('Female') }}</option>
            <option value="M">{{ __('Male') }}</option>
            <option value="O">{{ __('Other') }}</option>
        </flux:select>

            @unless(auth()->user()->isEmployee())
                <flux:input wire:model="default_delivery_address" :label="__('Delivery Address')" type="text" autocomplete="street-address" />
            @endunless  

            @unless(auth()->user()->isEmployee())
                <flux:input wire:model="nif" :label="__('NIF Number')" type="text" />
            @endunless

            @unless(auth()->user()->isEmployee())
                <flux:input wire:model="payment_details" :label="__('Payment Information')" type="text" />
            @endunless
            
            <flux:input wire:model="profile_photo" :label="__('Profile Photo')" type="file" accept="image/*" :disabled="auth()->user()->isEmployee()" />


            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    @unless(auth()->user()->isEmployee())
                        <flux:button variant="primary" type="submit" class="w-full">
                            {{ __('Save') }}
                        </flux:button>
                    @endunless  
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>

               

            </div>
        </form>

        @unless(auth()->user()->isEmployee())
            <livewire:settings.delete-user-form/>
        @endunless
    </x-settings.layout>
</section>

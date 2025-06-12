<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
 
    <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
        <x-app-logo />
    </a>
 
    @if(session('cart'))
        <flux:navlist variant="outline">
            <!-- <flux:navlist.group :heading="__('Services')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                <flux:navlist.item icon="academic-cap" :href="route('products.index')" :current="request()->routeIs('products.index')" wire:navigate>Products</flux:navlist.item>
                <flux:navlist.item icon="academic-cap" :href="route('orders.index')" :current="request()->routeIs('orders.index')" wire:navigate>Orders</flux:navlist.item>
                <flux:navlist.item icon="academic-cap" :href="route('card.show')" :current="request()->routeIs('card.show')" wire:navigate>Virtual Card</flux:navlist.item>
                <flux:navlist.item icon="academic-cap" :href="route('operations.index')" :current="request()->routeIs('operations.show')" wire:navigate>Operations</flux:navlist.item>
            </flux:navlist.group> -->
            <div class="relative inline-flex items-center mr-4">
                <div class="-top-0.5 absolute left-6 z-10">
                    <p class="flex p-3 h-3 w-3 items-center justify-center rounded-full bg-red-500 text-xs text-white">
                        {{ session('cart')->count() }}
                    </p>
                </div>
                <flux:navlist.item icon="shopping-cart" icon:variant="solid" :href="route('cart.show')" :current="request()->routeIs('cart.show')" wire:navigate>
                    <span class="pl-2">Shopping Cart</span>
                </flux:navlist.item>
            </div>
        </flux:navlist>
    @endif
 
    <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('Principal')" class="grid">
            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
        </flux:navlist.group>
    </flux:navlist>
 
    <flux:navlist variant="outline">
        <flux:navlist.group :heading="__('Services')" class="grid">
            <flux:navlist.item icon="academic-cap" :href="route('products.index')" :current="request()->routeIs('products.index')" wire:navigate>Products</flux:navlist.item>
            <flux:navlist.item icon="academic-cap" :href="route('orders.index')" :current="request()->routeIs('orders.index')" wire:navigate>Orders</flux:navlist.item>
            <flux:navlist.item icon="academic-cap" :href="route('card.show')" :current="request()->routeIs('card.show')" wire:navigate>Virtual Card</flux:navlist.item>
            <flux:navlist.item icon="academic-cap" :href="route('operations.index')" :current="request()->routeIs('operations.index')" wire:navigate>Operations</flux:navlist.item>
        </flux:navlist.group>
    </flux:navlist>
 
    <flux:navlist variant="outline">
        @can('viewAny', App\Models\MembershipFee::class)
            <flux:navlist.group :heading="__('Managing Board')" class="grid">
                <flux:navlist.item icon="academic-cap" :href="route('membershipfees.index')" :current="request()->routeIs('membershipfees.index')" wire:navigate>Membership fees</flux:navlist.item>
                <flux:navlist.item icon="academic-cap" :href="route('shippingcosts.index')" :current="request()->routeIs('shippingcosts.index')" wire:navigate>Shipping costs</flux:navlist.item>
                <flux:navlist.item icon="academic-cap" :href="route('supplyorders.index')" :current="request()->routeIs('supplyorders.index')" wire:navigate>Supply Orders</flux:navlist.item>
                <flux:navlist.item icon="academic-cap" :href="route('categories.index')" :current="request()->routeIs('categories.index')" wire:navigate>Categories</flux:navlist.item>
				<flux:navlist.item icon="academic-cap" :href="route('statistics.basic')" :current="request()->routeIs('statistics.*')" wire:navigate>Statistics</flux:navlist.item>
                <flux:navlist.item icon="academic-cap" :href="route('users.index')" :current="request()->routeIs('users.index')" wire:navigate>Users</flux:navlist.item>
            </flux:navlist.group>
        @endcan
    </flux:navlist>
 
    {{-- Desktop User Menu --}}
    @auth
        <flux:dropdown position="bottom" align="start">
            <flux:profile :name="auth()->user()?->firstLastName() ?? auth()->user()->name"
                :initials="auth()->user()?->firstLastInitial() ?? auth()->user()->initials()"
                :avatar="auth()->user()?->photoFullUrl" icon-trailing="chevrons-up-down" />
            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                @if(auth()->user()?->photoFullUrl)
                                    <img src="{{ auth()->user()->photoFullUrl }}" alt="User Photo" class="rounded-full w-8 h-8 object-cover" />
                                @else
                                    <span class="inline-flex items-center justify-center rounded-full bg-gray-300 text-gray-700 w-8 h-8 font-semibold">
                                        {{ auth()->user()?->firstLastInitial() ?? auth()->user()->initials() }}
                                    </span>
                                @endif
                            </span>
                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>
                <flux:menu.separator />
                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>
                <flux:menu.separator />
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
	@else
		<flux:navlist variant="outline">
			<flux:navlist.group :heading="'Authentication'" class="grid">
				<flux:navlist.item icon="key" :href="route('login')" :current="request()->routeIs('login')" wire:navigate>Login</flux:navlist.item>
			</flux:navlist.group>
		</flux:navlist>
    @endauth
 
</flux:sidebar>
 
<!-- Mobile User Menu -->
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
    <flux:spacer />
    @auth
        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()?->firstLastInitial() ?? auth()->user()->initials()" icon-trailing="chevron-down" />
            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                @if(auth()->user()?->photoFullUrl)
                                    <img src="{{ auth()->user()->photoFullUrl }}" alt="User Photo">
                                @else
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()?->firstLastInitial() ?? auth()->user()->initials() }}
                                    </span>
                                @endif
                            </span>
                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>
                <flux:menu.separator />
                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>
                <flux:menu.separator />
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
	@else
		<flux:navbar>
			<flux:navbar.item  icon="key" :href="route('login')" :current="request()->routeIs('login')" wire:navigate>Login</flux:navbar.item>
		</flux:navbar>
    @endauth
	
</flux:header>
 
{{ $slot }}
 
@fluxScripts
</body>
</html>
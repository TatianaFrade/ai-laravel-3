<x-layouts.app :title="__('Dashboard')">
    <div class="mb-4">
        @auth
            <p class="text-xl text-gray-900 dark:text-white">Bem-vindo, {{ auth()->user()->name }}!</p>
        @else
            <p class="text-xl text-gray-900 dark:text-white">Bem-vindo, convidado.</p>
        @endauth
    </div>

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            {{-- My Orders --}}
            <a href="{{ route('orders.index', ['mine' => true]) }}"
               class="relative aspect-video overflow-hidden rounded-xl border border-gray-300 dark:border-neutral-700 hover:scale-105 transition-transform bg-white dark:bg-transparent">
                <img src="{{ asset('storage/orders/caixasencomenda.jpg') }}" alt="My Orders"
                     class="absolute inset-0 size-full object-cover opacity-70" />
                <div class="absolute inset-0 flex items-center justify-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white drop-shadow-lg">My Orders</h2>
                </div>
            </a>

            {{-- Member Access --}}
            <a href="{{ route('membershipfees.index',['view' => 'public']) }}"
               class="relative aspect-video overflow-hidden rounded-xl border border-gray-300 dark:border-neutral-700 hover:scale-105 transition-transform bg-white dark:bg-transparent">
                <img src="{{ asset('storage/orders/descontos.png') }}" alt="Member Access"
                     class="absolute inset-0 size-full object-cover opacity-70" />
                <div class="absolute inset-0 flex items-center justify-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white drop-shadow-lg">Member Access</h2>
                </div>
            </a>

            {{-- My Card --}}
            <a href="{{ route('card.show') }}"
               class="relative aspect-video overflow-hidden rounded-xl border border-gray-300 dark:border-neutral-700 hover:scale-105 transition-transform bg-white dark:bg-transparent">
                <img src="{{ asset('storage/orders/cartao.jpg') }}" alt="My Card"
                     class="absolute inset-0 size-full object-cover opacity-70" />
                <div class="absolute inset-0 flex items-center justify-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white drop-shadow-lg">My Card</h2>
                </div>
            </a>
        </div>

        {{-- Product Catalog --}}
        <a href="{{ route('products.index', ['view' => 'public']) }}"
           class="relative h-full flex-1 overflow-hidden rounded-xl border border-gray-300 dark:border-neutral-700 hover:scale-[1.01] transition-transform bg-white dark:bg-transparent">
            <img src="{{ asset('storage/orders/produnormal.jpg') }}" alt="Product Catalog"
                 class="absolute inset-0 size-full object-cover opacity-60" />
            <div class="absolute inset-0 flex items-center justify-center">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white drop-shadow-lg">Product Catalog</h2>
            </div>
        </a>
    </div>
</x-layouts.app>

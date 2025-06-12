@props(['filterAction', 'resetUrl', 'filterByName' => '', 'orderPrice' => '', 'orderStock' => ''])

<div {{ $attributes }}>
    <form method="GET" action="{{ $filterAction }}">
        <div class="flex justify-between space-x-3">

            <!-- Input nome ou categoria -->
            <div class="grow flex flex-col space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-300">Search by name or category</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    value="{{ $filterByName }}" 
                    placeholder="All"
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <!-- Dropdown ordenação por preço -->
            <div class="w-48 flex flex-col space-y-2">
                <label for="order_price" class="block text-sm font-medium text-gray-300">Order by price</label>
                <select 
                    name="order_price" 
                    id="order_price" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                >                    
                    <option value="">All</option>
                    <option value="asc" {{ $orderPrice === 'asc' ? 'selected' : '' }}>Asc</option>
                    <option value="desc" {{ $orderPrice === 'desc' ? 'selected' : '' }}>Desc</option>
                </select>
            </div>

            @auth
                @if(Auth::user()->type === 'board')
                    <!-- Dropdown ordenação por stock (apenas para board) -->
                    <div class="w-48 flex flex-col space-y-2">
                        <label for="order_stock" class="block text-sm font-medium text-gray-300">Order by stock</label>
                        <select 
                            name="order_stock" 
                            id="order_stock" 
                            class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >                            
                            <option value="">All</option>
                            <option value="asc" {{ $orderStock === 'asc' ? 'selected' : '' }}>Asc</option>
                            <option value="desc" {{ $orderStock === 'desc' ? 'selected' : '' }}>Desc</option>
                        </select>
                    </div>
                @endif
            @endauth

            <!-- Botões -->
            <div class="grow-0 flex flex-col space-y-3 justify-start pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Filter
                </button>
            </div>
            <div class="grow-0 flex flex-col space-y-3 justify-start pt-6">
                <a href="{{ $resetUrl }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-center">
                    Cancel
                </a>
            </div>

        </div>
    </form>
</div>

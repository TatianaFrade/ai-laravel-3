@props(['filterAction', 'resetUrl', 'filterByName' => '', 'order' => '', 'orderProducts' => ''])

<div {{ $attributes }}>
    <form method="GET" action="{{ $filterAction }}">
        <div class="flex justify-between space-x-3">
            
            <div class="grow flex flex-col space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-300">Search by name</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    value="{{ $filterByName }}" 
                    placeholder="All"
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

           <!-- Dropdown ordenação alfabética -->
            <div class="w-48 flex flex-col space-y-2">
                <label for="order" class="block text-sm font-medium text-gray-300">Order by name</label>
                <select 
                    name="order" 
                    id="order" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                >                    
                    <option value="">All</option>
                    <option value="name_asc" {{ $order === 'name_asc' ? 'selected' : '' }}>A → Z</option>
                    <option value="name_desc" {{ $order === 'name_desc' ? 'selected' : '' }}>Z → A</option>
                </select>
            </div>

            <!-- Dropdown ordenação por número de produtos -->
            <div class="w-48 flex flex-col space-y-2">
                <label for="order_products" class="block text-sm font-medium text-gray-300">Order by quantity</label>
                <select 
                    name="order_products" 
                    id="order_products" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                >                      <option value="">All</option>
                    <option value="most" {{ $orderProducts === 'most' ? 'selected' : '' }}>Most products</option>
                    <option value="least" {{ $orderProducts === 'least' ? 'selected' : '' }}>Least products</option>
                </select>
            </div>            <!-- Buttons -->
            <div class="flex flex-col space-y-2">
                <label class="block text-sm font-medium text-gray-300 invisible">Actions</label>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded min-w-[90px]">
                        Filter
                    </button>
                    <a href="{{ $resetUrl }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-center min-w-[90px]">
                        Cancel
                    </a>
                </div>
            </div>
             
        </div>
    </form>
</div>

@props([
    'filterAction',
    'resetUrl',
    'filterByName' => '',
    'orderPrice' => '',
    'orderStock' => '',
    'orderName' => '',
    'orderDiscount' => '',
    'filterByCategoria' => '',
    'categories' => []  
])


{{-- 'orderDiscount' => '' --}}
{{-- 'orderName' => '' --}}

<div {{ $attributes }}>
    <form method="GET" action="{{ $filterAction }}">
        @if(request('view'))
            <input type="hidden" name="view" value="{{ request('view') }}">
        @endif
        <div class="flex justify-between space-x-3">


            {{-- filtrar por categoria  --> Tambem foi necessario passar as categorias no controlador--}}
           
            {{-- <div class="grow flex flex-col space-y-2">
                <label for="category_id" class="block text-sm font-medium text-gray-300">Category</label>
                <select 
                    name="category_id" 
                    id="category_id" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500">                    
                    <option value="">All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $filterByCategoria == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div> --}}

       
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

            {{-- Ordenacao asc e desc para o nome --}}

             <div class="grow flex flex-col space-y-2">
                <label for="order_name" class="block text-sm font-medium text-gray-300">Name</label>
                <select 
                    name="order_name" 
                    id="order_name" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                >                    
                    <option value="">All</option>
                    <option value="asc" {{ $orderName === 'asc' ? 'selected' : '' }}>Asc</option>
                    <option value="desc" {{ $orderName === 'desc' ? 'selected' : '' }}>Desc</option>
                </select>
            </div> 

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



              {{-- Filtro por desconto --}}
            {{-- <div class="w-48 flex flex-col space-y-2">
                <label for="order_discount" class="block text-sm font-medium text-gray-300">Filter by discount</label>
                <select 
                    name="order_discount" 
                    id="order_discount" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">All</option>
                    <option value="apenas_com_desconto" {{ $orderDiscount === 'apenas_com_desconto' ? 'selected' : '' }}>
                        With discount
                    </option>
                    <option value="sem_desconto" {{ $orderDiscount === 'sem_desconto' ? 'selected' : '' }}>
                        Without discount
                    </option>
                </select>
            </div>  --}}

            @auth
                @if(in_array(Auth::user()->type, ['board', 'employee']))
                 
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

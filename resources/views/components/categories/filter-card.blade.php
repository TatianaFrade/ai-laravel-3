<div {{ $attributes }}>
    <form method="GET" action="{{ route('categories.index') }}">
        <div class="flex justify-between space-x-3">

            
            <div class="grow flex flex-col space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-300">Nome</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    value="{{ old('name', $filterByName ?? '') }}" 
                    placeholder="Pesquisar por nome"
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

           <!-- Dropdown ordenação alfabética -->
            <div class="w-48 flex flex-col space-y-2">
                <label for="order" class="block text-sm font-medium text-gray-300">Ordenar por Nome</label>
                <select 
                    name="order" 
                    id="order" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="" @selected(empty($order))>Padrão</option>
                    <option value="name_asc" @selected(($order ?? '') === 'name_asc')>A → Z</option>
                    <option value="name_desc" @selected(($order ?? '') === 'name_desc')>Z → A</option>
                </select>
            </div>

            <!-- Dropdown ordenação por número de produtos -->
            <div class="w-48 flex flex-col space-y-2">
                <label for="order_products" class="block text-sm font-medium text-gray-300">Ordenar por Nº de Produtos</label>
                <select 
                    name="order_products" 
                    id="order_products" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="" @selected(empty($orderProducts))>Padrão</option>
                    <option value="most" @selected(($orderProducts ?? '') === 'most')>Mais produtos</option>
                    <option value="least" @selected(($orderProducts ?? '') === 'least')>Menos produtos</option>
                </select>
            </div>


            <!-- Botões -->
            <div class="grow-0 flex flex-col space-y-3 justify-start pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Filtrar
                </button>
            </div>
             <div class="grow-0 flex flex-col space-y-3 justify-start pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Cancelar
                </button>
            </div>
             
        </div>
    </form>
</div>

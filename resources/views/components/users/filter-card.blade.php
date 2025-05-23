<div {{ $attributes }}>
    <form method="GET" action="{{ route('users.index') }}">
        <div class="flex justify-between space-x-3">

            <!-- Input nome ou email -->
            <div class="grow flex flex-col space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-300">Nome ou Email</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    value="{{ old('name', $filterByName ?? '') }}" 
                    placeholder="Pesquisar por nome ou email"
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <!-- Dropdown gênero -->
            <div class="w-48 flex flex-col space-y-2">
                <label for="gender" class="block text-sm font-medium text-gray-300">Género</label>
                <select 
                    name="gender" 
                    id="gender" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="" @selected(empty($filterByGender)) class="bg-gray-800 text-gray-100">Todos</option>
                    <option value="F" @selected(($filterByGender ?? '') === 'F') class="bg-gray-800 text-gray-100">Feminino</option>
                    <option value="M" @selected(($filterByGender ?? '') === 'M') class="bg-gray-800 text-gray-100">Masculino</option>
                    <option value="O" @selected(($filterByGender ?? '') === 'O') class="bg-gray-800 text-gray-100">Outro</option>
                </select>
            </div>

            <!-- Dropdown tipo -->
            <div class="w-48 flex flex-col space-y-2">
                <label for="type" class="block text-sm font-medium text-gray-300">Tipo de Utilizador</label>
                <select 
                    name="type" 
                    id="type" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="" @selected(empty($filterByType)) class="bg-gray-800 text-gray-100">Todos</option>
                    <option value="board" @selected(($filterByType ?? '') === 'board') class="bg-gray-800 text-gray-100">Board</option>
                    <option value="member" @selected(($filterByType ?? '') === 'member') class="bg-gray-800 text-gray-100">Member</option>
                    <option value="employee" @selected(($filterByType ?? '') === 'employee') class="bg-gray-800 text-gray-100">Employee</option>
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

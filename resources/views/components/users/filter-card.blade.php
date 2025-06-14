@props(['filterAction', 'resetUrl', 'filterByName' => '', 'filterByGender' => '', 'filterByType' => ''])

<div {{ $attributes }}>
    <form method="GET" action="{{ $filterAction }}">
        <div class="flex justify-between space-x-3">

          
            <div class="grow flex flex-col space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-300">Search by name or email</label>                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    value="{{ $filterByName ?? '' }}" 
                    placeholder="All"
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>


            <div class="w-48 flex flex-col space-y-2">
                <label for="gender" class="block text-sm font-medium text-gray-300">Order by gender</label>
                <select 
                    name="gender" 
                    id="gender" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                >                    <option value="">All</option>
                    <option value="F" {{ $filterByGender === 'F' ? 'selected' : '' }} class="bg-gray-800 text-gray-100">Feminino</option>
                    <option value="M" {{ $filterByGender === 'M' ? 'selected' : '' }} class="bg-gray-800 text-gray-100">Masculino</option>
                    <option value="O" {{ $filterByGender === 'O' ? 'selected' : '' }} class="bg-gray-800 text-gray-100">Outro</option>
                </select>
            </div>

     
            <div class="w-48 flex flex-col space-y-2">
                <label for="type" class="block text-sm font-medium text-gray-300">Order by type of user</label>
                <select 
                    name="type" 
                    id="type" 
                    class="border border-gray-600 bg-gray-800 text-gray-100 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                >                    <option value="">All</option>
                    <option value="board" {{ $filterByType === 'board' ? 'selected' : '' }} class="bg-gray-800 text-gray-100">Board</option>
                    <option value="member" {{ $filterByType === 'member' ? 'selected' : '' }} class="bg-gray-800 text-gray-100">Member</option>
                    <option value="employee" {{ $filterByType === 'employee' ? 'selected' : '' }} class="bg-gray-800 text-gray-100">Employee</option>
                </select>
            </div>

            <div class="grow-0 flex flex-col space-y-3 justify-start pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Filter
                </button>
            </div>             <div class="grow-0 flex flex-col space-y-3 justify-start pt-6">
                <a href="{{ route('users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-center">
                    Cancel
                </a>
            </div>
            
        </div>
    </form>
</div>

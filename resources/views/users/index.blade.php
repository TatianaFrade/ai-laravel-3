<x-layouts.main-content :title="__('Users')"
                        heading="List of Users"
                        subheading="Manage the users offered by the institution">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl ">
    @can('create', App\Models\User::class)
      <div class="flex items-center gap-4 mb-4">
        <flux:button variant="primary" href="{{ route('users.create') }}">Create a new employee</flux:button>
      </div>
    @endcan
 
    <div class="flex justify-start ">
      <div class="my-4 p-6 w-full">        <x-users.filter-card 
              :filterAction="route('users.index')" 
              :resetUrl="route('users.index')"
              :filter-by-name="$filterByName"
              :filter-by-gender="$filterByGender"
              :filter-by-type="$filterByType"
        />

        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table-auto w-full border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left">Photo</th>
                <th class="px-2 py-2 text-left">Name</th>
                <th class="px-2 py-2 text-left">Email</th>
                <th class="px-2 py-2 text-left">Type</th>
                <th class="px-2 py-2 text-left">Gender</th>
                  @can('viewBlockedStatus', auth()->user())
                    <th class="px-2 py-2 text-left">Blocked</th>
                  @endcan                <th class="px-2 py-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
               @foreach ($allUsers as $user)
              <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                 <td class="px-2 py-2 text-left">
                  

                   <div class="h-20 w-20 rounded-full object-cover">
                    <img src="{{ $user->image_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded" />
                  </div>

   
                  </td>
                  <td class="{{ $user->trashed() ? 'text-red-600 font-semibold' : '' }}">
                    {{ $user->name }}
                  </td>
                  <td class="px-2 py-2 text-left">{{ $user->email }}</td>
                  <td class="px-2 py-2 text-left">{{ $user->type }}</td>
                  <td class="px-2 py-2 text-left">{{ $user->gender }}</td>

               <td class="px-2 py-2 text-left">                    @can('update', $user)
                        @if ($user->id !== Auth::id() && $user->type === 'member')
                            @if(Auth::user()->type === 'board')
                                <form action="{{ route('users.toggleBlocked', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button 
                                        type="submit" 
                                        title="{{ $user->blocked ? 'Desbloquear utilizador' : 'Bloquear utilizador' }}"
                                        class="text-sm text-white px-3 py-1 rounded 
                                            {{ $user->blocked ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
                                        {{ $user->blocked ? 'Blocked' : 'Unblocked' }}
                                    </button>
                                </form>
                            @else
                                <div class="text-sm px-3 py-1 rounded bg-gray-400" title="{{ $user->blocked ? 'Blocked' : 'Unblocked' }} (Cannot modify)">
                                    {{ $user->blocked ? 'Blocked' : 'Unblocked' }}
                                </div>
                            @endif
                        @else
                            <div class="text-left text-gray-500">-</div>
                        @endif
                    @else
                        <div class="text-left text-gray-500">-</div>
                    @endcan
                </td>                  <td class="px-2 py-2 text-right">                    
                    <div class="flex items-center justify-end gap-2">
                      {{-- View button - always visible --}}
                      @if (!$user->trashed() && Auth::user()->type === 'board')
                        <a href="{{ route('users.show', ['user' => $user]) }}" class="hover:text-gray-600" title="View">
                          <flux:icon.eye class="size-5" />
                        </a>
                      @else
                        <div class="inline-flex" title="View (Read-only)">
                          <flux:icon.eye class="size-5 text-gray-400" />
                        </div>
                      @endif

                      {{-- Edit button --}}
                      @can('update', $user)
                        @if (!$user->trashed() && Auth::user()->type === 'board')
                          <a href="{{ route('users.edit', ['user' => $user]) }}" title="Edit">
                            <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                          </a>
                        @else
                          <div class="inline-flex" title="Edit ({{ $user->trashed() ? 'Read-only' : 'Unavailable' }})">
                            <flux:icon.pencil-square class="size-5 text-gray-400" />
                          </div>
                        @endif
                      @endcan

                      {{-- Cancel/Delete button --}}
                      @can('delete', $user)
                        @if (!$user->trashed() && Auth::user()->type === 'board')
                          <form method="POST" action="{{ route('users.destroy', ['user' => $user]) }}" class="flex items-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Cancel membership">
                              <flux:icon.cube class="size-5 hover:text-red-600" />
                            </button>
                          </form>
                        @else
                          <div class="inline-flex" title="Cancel membership ({{ $user->trashed() ? 'Read-only' : 'Unavailable' }})">
                            <flux:icon.cube class="size-5 text-gray-400" />
                          </div>
                        @endif
                      @endcan

                      {{-- Restore button (only for soft deleted users) --}}
                      @if ($user->trashed())
                        @can('restore', $user)
                          @if (Auth::user()->type === 'board')
                            <form method="POST" action="{{ route('users.restore', ['user' => $user]) }}" class="flex items-center">
                              @csrf
                              <button type="submit" title="Restore membership">
                                <flux:icon.arrow-path-rounded-square class="size-5 hover:text-green-600" />
                              </button>
                            </form>
                          @else
                            <div class="inline-flex" title="Restore membership (Unavailable)">
                              <flux:icon.arrow-path-rounded-square class="size-5 text-gray-400" />
                            </div>
                          @endif
                        @endcan
                      @endif
                      
                      {{-- Permanent Delete button (only for board users to delete employees, regardless of soft delete status) --}}
                      @can('forceDelete', $user)
                        @if (Auth::user()->type === 'board' && $user->type === 'employee')
                          <form method="POST" action="{{ route('users.forceDestroy', ['id' => $user->id]) }}" class="flex items-center" onsubmit="return confirm('Are you sure you want to permanently delete this employee? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Permanently delete employee">
                              <flux:icon.trash class="size-5 hover:text-red-700" />
                            </button>
                          </form>
                        @endif
                      @endcan
                    </div>
                  </td>


                </tr>
                @endforeach
            </tbody>
          </table>
        </div>

        <div class="mt-4">
          {{ $allUsers->links() }}
        </div>
      </div>
    </div>
  </div>
</x-layouts.main-content>

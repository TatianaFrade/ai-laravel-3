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
      <div class="my-4 p-6 w-full">
        <x-users.filter-card 
              :filterAction="route('users.index')" 
              :resetUrl="route('users.index')" 
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
                  @endcan

                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($allUsers as $user)
              <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                <td class="px-2 py-2 text-left">
                  @if ($user->photo)
                    <img src="{{ asset('storage/users/' . $user->photo) }}" alt="Photo of {{ $user->name }}" class="h-10 w-10 rounded-full object-cover">
                  @else
                    <span class="text-gray-400">No photo</span>
                  @endif
                </td>
                <td class="{{ $user->trashed() ? 'text-red-600 font-semibold' : '' }}">
                  {{ $user->name }}
                </td>
                <td class="px-2 py-2 text-left">{{ $user->email }}</td>
                <td class="px-2 py-2 text-left">{{ $user->type }}</td>
                <td class="px-2 py-2 text-left">{{ $user->gender }}</td>

                @can('update', $user)
                  <td class="px-2 py-2 text-left">
                    @if ($user->type === 'member')
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
                      {{-- Mostrar hífen alinhado no lugar do botão --}}
                      <div class="text-left text-gray-500">-</div>
                    @endif
                  </td>
                @endcan

                <td class="ps-2 px-0.5">
                  <a href="{{ route('users.show', ['user' => $user]) }}" class="hover:text-gray-600" title="View">
                    <flux:icon.eye class="size-5" />
                  </a>
                </td>
                
            @can('update', $user)
              <td class="px-0.5">
                <a href="{{ route('users.edit', ['user' => $user]) }}" title="Edit">
                  <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                </a>
              </td>
            @endcan

            @can('delete', $user)
              <td class="px-0.5">
                @if (!$user->trashed())
                  <form method="POST" action="{{ route('users.destroy', ['user' => $user]) }}" class="flex items-center">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Cancel membership">
                      <flux:icon.cube class="size-5 hover:text-red-600" />
                    </button>
                  </form>
                @endif
              </td>
            @endcan

            @can('forceDelete', $user)
              <td class="px-0.5">
                <form method="POST" action="{{ route('users.forceDestroy', ['user' => $user]) }}" class="flex items-center">
                  @csrf
                  @method('DELETE')
                  <button type="submit" title="Delete user">
                    <flux:icon.trash class="size-5 hover:text-red-600" />
                  </button>
                </form>
              </td>
            @endcan


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

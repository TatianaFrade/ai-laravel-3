
<x-layouts.main-content :title="__('Users')"
                        heading="List of Users"
                        subheading="Manage the users offered by the institution">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl ">
    <div class="flex justify-start ">
      <div class="my-4 p-6 ">
        <div class="flex items-center gap-4 mb-4">
          <flux:button variant="primary" href="{{ route('users.create') }}">Create a new user</flux:button>
        </div>
        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table-auto border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left">Name</th>
                <th class="px-2 py-2 text-left">Email</th>
                <th class="px-2 py-2 text-left hidden sm:table-cell">Type</th>
                <th class="px-2 py-2 text-right hidden sm:table-cell">gender</th>
                <th class="px-2 py-2 text-right hidden sm:table-cell">delivery_address</th>
                <th class="px-2 py-2 text-right hidden sm:table-cell">nif</th>
                <th class="px-2 py-2 text-right hidden sm:table-cell">payment_details</th>
                <th class="px-2 py-2 text-right hidden sm:table-cell">profile_photo</th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($allUsers as $user)
              <tr class="border-b border-b-gray-400 dark:border-b-gray-500">
                <td class="px-2 py-2 text-left">{{ $user->name }}</td>
                <td class="px-2 py-2 text-left">{{ $user->email}}</td>
                <td class="px-2 py-2 text-left hidden sm:table-cell">{{  $user->type }}</td>
                <td class="px-2 py-2 text-right hidden sm:table-cell">{{ $user->gender }}</td>
                <td class="px-2 py-2 text-right hidden sm:table-cell">{{ $user->delivery_address }}</td>
                <td class="px-2 py-2 text-right hidden sm:table-cell">{{ $user->nif }}</td>
                <td class="px-2 py-2 text-right hidden sm:table-cell">{{ $user->payment_details }}</td>
                <td class="ps-2 px-0.5">
                  <a href="{{ route('users.show', ['user' => $user]) }}">
                    <flux:icon.eye class="size-5 hover:text-gray-600" />
                  </a>
                </td>
                <td class="px-0.5">
                  <a href="{{ route('users.edit', ['user' => $user]) }}">
                    <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                  </a>
                </td>
                <td class="px-0.5">
                  <form method="POST" action="{{ route('users.destroy', ['user' => $user]) }}" class="flex items-center">
                    @csrf
                    @method('DELETE')
                    <button type="submit">
                      <flux:icon.trash class="size-5 hover:text-red-600" />
                    </button>
                  </form>
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


<x-layouts.main-content :title="__('Membership fee')"
                        heading="Membership fee applied to new members"
                        subheading="New members must pay membership fee to have full access to services">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">
        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          <table class="table w-full border-collapse">
            <thead>
              <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">Membership Fee</th>
                <th class="px-3 py-2 text-center"></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($fees as $fee)
                <tr>
                  <td class="px-3 py-2">{{ $fee->id ?? '—' }}</td>
                  <td class="px-3 py-2">{{ $fee->membership_fee ?? '0' }}</td>
                </tr>
              @endforeach

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</x-layouts.main-content>

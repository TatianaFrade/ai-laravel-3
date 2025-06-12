<x-layouts.main-content 
    :title="__('Membership fee')"
    heading="Membership fee applied to members anually"
    subheading="New members must pay membership fee to have full access to services">

  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

    {{-- ✅ Botão destacado acima da tabela, só aparece para membros bloqueados --}}      @can('pay', $membershipfee)
        @if(Auth::user()->showMembershipPayButton())
          <div class="flex justify-start px-6 pt-4">
            <form method="POST" action="{{ route('membershipfees.pay', $membershipfee) }}">
              @csrf
              <flux:button type="submit" variant="primary" class="text-lg px-6 py-3">
                Pay Membership Fee (€{{ number_format($membershipfee->membership_fee, 2) }})
              </flux:button>
            </form>
          </div>
        @endif
      @endcan




          {{-- 🔽 Tabela de visualização da taxa --}}
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
                <tr>
                  <td class="px-3 py-2">{{ $membershipfee->id ?? '—' }}</td>
                  <td class="px-3 py-2">{{ $membershipfee->membership_fee ?? '0' }}</td>
                  <td class="px-2 py-2 text-center">
                    <div class="flex gap-2 justify-center">
                      @can('update', $membershipfee)
                        <a href="{{ route('membershipfees.edit', $membershipfee) }}" title="Edit">
                          <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                        </a>
                      @endcan


                    {{-- Nota: botão "Pay" está acima --}}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</x-layouts.main-content>

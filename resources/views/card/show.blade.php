<x-layouts.main-content :title="__('User Card')"
                        heading="User Card Details"
                        subheading="Card information for the authenticated user">
  <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex justify-start">
      <div class="my-4 p-6 w-full">
        <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
          @if ($card)
            <table class="table w-full border-collapse">
              <thead>
                <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                  <th class="px-3 py-2 text-left">Card Number</th>
                  <th class="px-3 py-2 text-left">Balance</th>
                  <th class="px-3 py-2 text-left">Created At</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="px-3 py-2">{{ $card->card_number ?? '—' }}</td>
                  <td class="px-3 py-2">{{ number_format($card->balance, 2) ?? '0.00' }} €</td>
                  <td class="px-3 py-2">{{ $card->created_at ? $card->created_at->format('d/m/Y H:i') : '—' }}</td>
                </tr>
              </tbody>
            </table>
          @else
            <div class="text-center py-12 text-gray-500 text-lg">
              You don’t have a card yet.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-layouts.main-content>

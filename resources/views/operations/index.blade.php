<x-layouts.main-content :title="'Meu Cartão'" :heading="'Operações'" subheading='Histórico de operações.'>
    <div class="flex flex-col space-y-6">
        <div class="w-full">
            <section class="bg-gray-800 text-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-gray-200 mb-4">Histórico de Operações</h2>

                <div class="bg-gray-700 rounded-md shadow-sm border border-gray-600">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-900 text-gray-200">
                                <th class="p-3 border border-gray-600">Card ID</th>
                                <th class="p-3 border border-gray-600">Type</th>
                                <th class="p-3 border border-gray-600">Value</th>
                                <th class="p-3 border border-gray-600">Date</th>
                                <th class="p-3 border border-gray-600">Debit Type</th>
                                <th class="p-3 border border-gray-600">Credit Type</th>
                                <th class="p-3 border border-gray-600">Payment Type</th>
                                <th class="p-3 border border-gray-600">Payment Reference</th>
                                <th class="p-3 border border-gray-600">Order ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operations as $item)
                                <tr class="bg-gray-800 text-gray-300">
                                    <td class="p-3 border border-gray-600">{{ $item->card_id }}</td>
                                    <td class="p-3 border border-gray-600">{{ $item->type }}</td>
                                    <td class="p-3 border border-gray-600">{{ $item->value }}</td>
                                    <td class="p-3 border border-gray-600">{{ date('d/m/Y', strtotime($item->date)) }}</td>
                                    <td class="p-3 border border-gray-600">
                                        @if ($item->debit_type === 'order')
                                            Order
                                        @elseif ($item->debit_type === 'membership_fee')
                                            Membership Fee
                                        @else
                                            {{ $item->debit_type }}
                                        @endif
                                    </td>
                                    <td class="p-3 border border-gray-600">
                                        @if ($item->credit_type === 'order_cancellation')
                                            Order Cancellation
                                        @elseif ($item->credit_type === 'payment')
                                            Payment
                                        @else
                                            {{ $item->credit_type }}
                                        @endif
                                    </td>
                                    <td class="p-3 border border-gray-600">{{ $item->payment_type }}</td>
                                    <td class="p-3 border border-gray-600">{{ $item->payment_reference }}</td>
                                    <td class="p-3 border border-gray-600">{{ $item->order_id }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-layouts.main-content>
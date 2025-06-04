<x-layouts.main-content :title="'Meu Cartão'" :heading="'Detalhes do cartão'"
    subheading='Verifique as informações do seu cartão abaixo.'>

    <div class="flex flex-col space-y-6">
        <div class="w-1/2 ml-0">
            <section class="bg-gray-800 text-white p-6 rounded-lg shadow-md">
                <form method="POST" action="{{ route('balance.update') }}">
                    @csrf
                    <h2 class="text-2xl font-bold text-gray-200 mb-4">Meu Cartão</h2>

                    <div class="bg-gray-700 p-4 rounded-md shadow-sm border border-gray-600">
                        <p class="text-lg font-semibold text-gray-300">
                            Número do Cartão: <span class="text-blue-400" name="cardNum">{{ $card->card_number }}</span>
                        </p>
                        <p class="text-lg font-semibold text-gray-300">
                            Saldo: <span class="text-green-400">€{{ number_format($card->balance, 2) }}</span>
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-4 mt-4">
                        <div class="flex-1 min-w-[180px]">
                            <flux:input name="amount" label="Adicionar Valor (€)" value="{{ old('amount') }}"
                                class="w-full" />
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <flux:select name="type" label="Método de Pagamento:" class="w-full">
                                <option value="Visa">Visa</option>
                                <option value="PayPal">PayPal</option>
                                <option value="MB WAY">Mb WAY</option>
                            </flux:select>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-6">
                        <div class="flex space-x-2 w-full">
                            <flux:button variant="primary" class="w-1/3" type="submit">
                                Adicionar
                            </flux:button>
                            <flux:button variant="filled" class="w-1/3" href="{{ url()->full() }}">Cancel</flux:button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.main-content>
<x-layouts.main-content :title="'Criar Cartão Virtual'" :heading="'Criar Cartão Virtual'"
    subheading='Preencha os dados abaixo para criar seu cartão virtual.'>
    <div class="flex flex-col space-y-6">
        <div class="w-1/2 ml-0">
            <section class="bg-gray-800 text-white p-6 rounded-lg shadow-md">
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-900 text-red-300 rounded border border-red-700 shadow-md">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('card.store') }}">
                    @csrf
                    <h2 class="text-2xl font-bold text-gray-200 mb-4">Novo Cartão</h2>

                    <div class="bg-gray-700 p-4 rounded-md shadow-sm border border-gray-600">
                        <p class="text-lg font-semibold text-gray-300">
                            Nome do Usuário: <span class="text-yellow-400">{{ auth()->user()->name }}</span>
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-4 justify-center">
                        <div class="flex-1 min-w-[180px]">
                            <flux:input name="amount" label="Valor Inicial (€)" value="{{ old('amount') }}"
                                class="w-full" />
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <flux:select name="type" label="Método de Pagamento:" class="w-full" id="payment-type">
                                <option value="">Selecione</option>
                                <option value="Visa" {{ old('type') == 'Visa' ? 'selected' : '' }}>Visa</option>
                                <option value="PayPal" {{ old('type') == 'PayPal' ? 'selected' : '' }}>PayPal</option>
                                <option value="MB WAY" {{ old('type') == 'MB WAY' ? 'selected' : '' }}>Mb WAY</option>
                            </flux:select>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 mt-4">
                        <div class="flex-1 min-w-[180px]" id="visa-fields" style="display: none;">
                            <flux:input name="card_num" label="Número do Cartão" value="{{ old('num_card') }}" class="w-full mb-2" />
                            <flux:input name="cvc" label="CVC" value="{{ old('cvc') }}" class="w-full" />
                        </div>

                        <div class="flex-1 min-w-[180px]" id="paypal-fields" style="display: none;">
                            <flux:input name="email" label="Email PayPal" value="{{ old('email') }}" class="w-full" />
                        </div>

                        <div class="flex-1 min-w-[180px]" id="mbway-fields" style="display: none;">
                            <flux:input name="phone_number" label="Telemóvel MB WAY" value="{{ old('phone_number') }}" class="w-full" />
                        </div>

                        <script>
                            function showPaymentFields() {
                                const type = document.getElementById('payment-type').value;
                                document.getElementById('visa-fields').style.display = (type === 'Visa') ? '' : 'none';
                                document.getElementById('paypal-fields').style.display = (type === 'PayPal') ? '' : 'none';
                                document.getElementById('mbway-fields').style.display = (type === 'MB WAY') ? '' : 'none';
                            }
                            document.addEventListener('DOMContentLoaded', function() {
                                document.getElementById('payment-type').addEventListener('change', showPaymentFields);
                                showPaymentFields();
                            });
                        </script>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-6">
                        <div class="flex space-x-2 w-full justify-center">
                            <flux:button variant="primary" class="w-1/3" type="submit">
                                Criar Cartão
                            </flux:button>
                            <flux:button variant="filled" class="w-1/3" href="{{ url()->previous() }}">Cancelar</flux:button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.main-content>

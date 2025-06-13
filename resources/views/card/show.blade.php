<x-layouts.main-content :title="'My Card'" :heading="'Card Details'" subheading='Check your card information below.'>

    @if($card)
        <div class="flex flex-col space-y-6">
            <div class="w-1/2 ml-0">
                <section class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white p-6 rounded-lg shadow-md">
                    <form method="POST" action="{{ route('balance.update') }}">
                        @csrf
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">My Card</h2>

                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md shadow-sm border border-gray-300 dark:border-gray-600">
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                                User Name: <span class="text-yellow-600 dark:text-yellow-400">{{ auth()->user()->name }}</span>
                            </p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                                Card Number: <span class="text-blue-600 dark:text-blue-400" name="cardNum">{{ $card->card_number }}</span>
                            </p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                                Balance: <span class="text-green-600 dark:text-green-400">€{{ number_format($card->balance, 2) }}</span>
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-4 justify-center">
                            <div class="flex-1 min-w-[180px]">
                                <flux:input name="amount" label="Add Amount (€)" value="{{ old('amount') }}" class="w-full" />
                            </div>
                            <div class="flex-1 min-w-[180px]">
                                <flux:select name="type" label="Payment Method:" class="w-full" id="payment-type">
                                    <option value="">Select</option>
                                    <option value="Visa" {{ old('type') == 'Visa' ? 'selected' : '' }}>Visa</option>
                                    <option value="PayPal" {{ old('type') == 'PayPal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="MB WAY" {{ old('type') == 'MB WAY' ? 'selected' : '' }}>MB WAY</option>
                                </flux:select>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 mt-4">
                            <div class="flex-1 min-w-[180px]" id="visa-fields" style="display: none;">
                                <flux:input name="card_num" label="Card Number" value="{{ old('num_card') }}" class="w-full mb-2" />
                                <flux:input name="cvc" label="CVC" value="{{ old('cvc') }}" class="w-full" />
                            </div>

                            <div class="flex-1 min-w-[180px]" id="paypal-fields" style="display: none;">
                                <flux:input name="email" label="PayPal Email" value="{{ old('email') }}" class="w-full" />
                            </div>

                            <div class="flex-1 min-w-[180px]" id="mbway-fields" style="display: none;">
                                <flux:input name="phone_number" label="MB WAY Phone" value="{{ old('phone_number') }}" class="w-full" />
                            </div>

                            <script>
                                function showPaymentFields() {
                                    const type = document.getElementById('payment-type').value;
                                    document.getElementById('visa-fields').style.display = (type === 'Visa') ? '' : 'none';
                                    document.getElementById('paypal-fields').style.display = (type === 'PayPal') ? '' : 'none';
                                    document.getElementById('mbway-fields').style.display = (type === 'MB WAY') ? '' : 'none';
                                }
                                document.addEventListener('DOMContentLoaded', function () {
                                    document.getElementById('payment-type').addEventListener('change', showPaymentFields);
                                    showPaymentFields();

                                    const cardInput = document.querySelector("[name='card_num']");

                                    cardInput.addEventListener("input", function () {
                                        let value = cardInput.value.replace(/\D/g, "");
                                        value = value.replace(/(\d{4})/g, "$1  ").trim();
                                        cardInput.value = value;
                                    });

                                    cardInput.closest("form").addEventListener("submit", function () {
                                        cardInput.value = cardInput.value.replace(/\s/g, "");
                                    });
                                });
                            </script>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-6">
                            <div class="flex space-x-2 w-full justify-center">
                                <flux:button variant="primary" class="w-1/3" type="submit">Add</flux:button>
                                <flux:button variant="filled" class="w-1/3" href="{{ url()->full() }}">Cancel</flux:button>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    @else
        <div class="flex justify-center items-center h-64">
            <span class="text-lg text-gray-800 dark:text-gray-400">You don't have a card yet</span>
        </div>
    @endif

</x-layouts.main-content>

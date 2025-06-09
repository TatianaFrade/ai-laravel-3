<x-layouts.main-content title="Estatísticas Avançadas" heading="Visão Pessoal Avançada" subheading="Seus hábitos de compra">
    <div class="p-6 space-y-8">

        {{-- Navegação --}}
        <div class="flex gap-4 mb-6">
            <a href="{{ route('statistics.basic') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Básico</a>
            <a href="{{ route('statistics.advanced') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Avançado</a>
        </div>

        {{-- Gasto por Mês --}}
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2">💸 Gasto por Mês</h3>
            <canvas id="spendingChart"></canvas>
        </div>

        {{-- Produtos Frequentes --}}
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2">🛍️ Produtos Mais Comprados</h3>
            <table class="min-w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2">Produto</th>
                        <th class="px-3 py-2">Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['frequent_products'] as $prod)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $prod->name }}</td>
                            <td class="px-3 py-2">{{ $prod->total_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx2 = document.getElementById('spendingChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data['orders_by_month']->pluck('month')) !!},
                datasets: [{
                    label: 'Gasto (€)',
                    data: {!! json_encode($data['orders_by_month']->pluck('total')) !!},
                    backgroundColor: 'rgba(34,197,94,0.6)',
                    borderColor: 'rgba(22,163,74,1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</x-layouts.main-content>
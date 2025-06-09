<x-layouts.main-content title="Estatísticas Avançadas" heading="Análise Detalhada" subheading="Insights mensais e principais métricas">
    <div class="p-6 space-y-8">

        {{-- Navegação entre Basic e Advanced --}}
        <div class="flex gap-4 mb-6">
            <a href="{{ route('statistics.basic') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Básico</a>
            <a href="{{ route('statistics.advanced') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Avançado</a>
        </div>

        {{-- Vendas por Mês --}}
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2">💰 Vendas por Mês</h3>
            <canvas id="salesChart"></canvas>
        </div>

        {{-- Top Produtos --}}
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2">🏆 Top 5 Produtos Mais Vendidos</h3>
            <table class="min-w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2">Produto</th>
                        <th class="px-3 py-2">Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['top_products'] as $prod)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $prod->name }}</td>
                            <td class="px-3 py-2">{{ $prod->total_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Top Spenders --}}
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2">💳 Top 5 Membros que Mais Gastaram</h3>
            <table class="min-w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2">Membro</th>
                        <th class="px-3 py-2">Total Gasto (€)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['top_spenders'] as $sp)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $sp->name }}</td>
                            <td class="px-3 py-2">€{{ number_format($sp->total_spent, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($data['sales_by_month']->pluck('month')) !!},
                datasets: [{
                    label: 'Vendas (€)',
                    data: {!! json_encode($data['sales_by_month']->pluck('total')) !!},
                    borderColor: 'rgba(59,130,246,1)',
                    backgroundColor: 'rgba(59,130,246,0.2)',
                    tension: 0.3
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</x-layouts.main-content>
<x-layouts.main-content title="Advanced Statistics" heading="Detailed Analysis" subheading="Monthly insights and key metrics">
    <div class="p-6 space-y-8">

        {{-- Navigation between Basic and Advanced --}}
        <div class="flex gap-4 mb-6">
            <a href="{{ route('statistics.basic') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Basic</a>
            <a href="{{ route('statistics.advanced') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Advanced</a>
        </div>

        {{-- Top Products --}}
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2">🏆 Top 5 Best-Selling Products</h3>
            <table class="min-w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2">Product</th>
                        <th class="px-3 py-2">Quantity</th>
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
            <h3 class="text-lg font-semibold mb-2">💳 Top 5 Members by Spending</h3>
            <table class="min-w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2">Member</th>
                        <th class="px-3 py-2">Total Spent (€)</th>
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

        {{-- Sales by Month and Category --}}
		<a href="{{ route('statistics.export.category') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
			📥 Export Sales by Category
		</a>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2">📊 Sales by Month & Category</h3>

            <div id="categoryButtons" class="flex flex-wrap gap-3 mb-4">
                {{-- Os botões serão gerados via JS --}}
            </div>

            <canvas id="categoryChart" class="mb-4"></canvas>

            <div id="statsDisplay" class="text-sm text-gray-700 space-y-1">
                <p><strong>Category:</strong> <span id="statCategory">-</span></p>
                <p><strong>Min:</strong> €<span id="statMin">-</span></p>
                <p><strong>Avg:</strong> €<span id="statAvg">-</span></p>
                <p><strong>Max:</strong> €<span id="statMax">-</span></p>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const rawDataFlat = {!! json_encode($data['sales_by_category']) !!};

        // Agrupa dados por categoria
        const rawData = {};
        rawDataFlat.forEach(item => {
            if (!rawData[item.category]) {
                rawData[item.category] = [];
            }
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            rawData[item.category].push({
                month: monthNames[item.month - 1] || item.month,
                totalS: parseFloat(item.totalS)
            });
        });

        // Função que calcula o total por mês somando todas as categorias
        function getTotalByMonth() {
            const totalsByMonth = {};
            // percorre cada categoria e seus dados
            Object.values(rawData).forEach(categoryData => {
                categoryData.forEach(({month, totalS}) => {
                    totalsByMonth[month] = (totalsByMonth[month] || 0) + totalS;
                });
            });
            // transforma em array ordenada
            const months = Object.keys(totalsByMonth);
            const totals = months.map(m => totalsByMonth[m]);
            return {months, totals};
        }

        const ctx = document.getElementById('categoryChart').getContext('2d');
        let chart;

        function updateCategoryChart(category) {
            let months = [];
            let totals = [];

            if (category === 'Total') {
                const totalData = getTotalByMonth();
                months = totalData.months;
                totals = totalData.totals;
            } else {
                const dataPoints = rawData[category] || [];
                months = dataPoints.map(entry => entry.month);
                totals = dataPoints.map(entry => entry.totalS);
            }

            if (totals.length === 0) return;

            const min = Math.min(...totals);
            const max = Math.max(...totals);
            const avg = (totals.reduce((a, b) => a + b, 0) / totals.length).toFixed(2);

            document.getElementById('statCategory').textContent = category;
            document.getElementById('statMin').textContent = min.toFixed(2).replace('.', ',');
            document.getElementById('statAvg').textContent = avg.replace('.', ',');
            document.getElementById('statMax').textContent = max.toFixed(2).replace('.', ',');

            if (chart) chart.destroy();
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Sales (€)',
                        data: totals,
                        borderColor: 'rgba(59,130,246,1)',
                        backgroundColor: 'rgba(59,130,246,0.2)',
                        tension: 0.3
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Cria botões dinamicamente, adicionando o botão "Total" no início
        function createCategoryButtons() {
            const container = document.getElementById('categoryButtons');
            container.innerHTML = '';

            // Botão Total
            const totalBtn = document.createElement('button');
            totalBtn.textContent = "Total";
            totalBtn.className = "px-4 py-2 bg-gray-200 hover:bg-blue-500 hover:text-white rounded";
            totalBtn.addEventListener('click', () => updateCategoryChart('Total'));
            container.appendChild(totalBtn);

            // Botões por categoria
            Object.keys(rawData).forEach(category => {
                const btn = document.createElement('button');
                btn.textContent = category.charAt(0).toUpperCase() + category.slice(1);
                btn.className = "px-4 py-2 bg-gray-200 hover:bg-blue-500 hover:text-white rounded";
                btn.addEventListener('click', () => updateCategoryChart(category));
                container.appendChild(btn);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            createCategoryButtons();
            // Exibe o gráfico com "Total" inicialmente
            updateCategoryChart('Total');
        });
    </script>
</x-layouts.main-content>

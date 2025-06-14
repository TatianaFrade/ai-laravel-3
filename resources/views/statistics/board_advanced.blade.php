<x-layouts.main-content title="Advanced Statistics" heading="Detailed Analysis" subheading="Monthly insights and key metrics">
    <div class="p-6 space-y-8">

        {{-- Navigation between Basic and Advanced --}}
        <div class="flex gap-4 mb-6">
            <a href="{{ route('statistics.basic') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">Basic</a>
            <a href="{{ route('statistics.advanced') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Advanced</a>
        </div>

        {{-- Top Products --}}
        <div class="bg-white dark:bg-gray-900 p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2 dark:text-white">🏆 Top 5 Best-Selling Products</h3>
            <table class="min-w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-100 dark:bg-gray-800 dark:text-gray-200">
                    <tr>
                        <th class="px-3 py-2">Product</th>
                        <th class="px-3 py-2">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['top_products'] as $prod)
                        <tr class="border-t dark:border-gray-700">
                            <td class="px-3 py-2">{{ $prod->name }}</td>
                            <td class="px-3 py-2">{{ $prod->total_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Top Spenders --}}
        <div class="bg-white dark:bg-gray-900 p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2 dark:text-white">💳 Top 5 Members by Spending</h3>
            <table class="min-w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-100 dark:bg-gray-800 dark:text-gray-200">
                    <tr>
                        <th class="px-3 py-2">Member</th>
                        <th class="px-3 py-2">Total Spent (€)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['top_spenders'] as $sp)
                        <tr class="border-t dark:border-gray-700">
                            <td class="px-3 py-2">{{ $sp->name }}</td>
                            <td class="px-3 py-2">€{{ number_format($sp->total_spent, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Export Button --}}
        <a href="{{ route('statistics.export.category') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            📥 Export Sales by Category
        </a>

        {{-- Sales by Month and Category --}}
        <div class="bg-white dark:bg-gray-900 p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2 dark:text-white">📊 Sales by Month & Category</h3>

            <div id="categoryButtons" class="flex flex-wrap gap-3 mb-4">
                {{-- Botões via JS --}}
            </div>

            <canvas id="categoryChart" class="mb-4 bg-white dark:bg-gray-800 rounded p-2"></canvas>

            <div id="statsDisplay" class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
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
        const rawDataShipping = {!! json_encode($data['shipping']) !!};

        // Agrupa dados por categoria
        const rawData = {};
        rawData['Total'] = [];
        rawDataFlat.forEach(item => {
            if (!rawData[item.category]) rawData[item.category] = [];
            rawData[item.category].push({
                year: item.year,
                month: item.month,
                totalS: parseFloat(item.totalS)
            });

            // Soma para o total dependendo do ano e mês, sumando o custo do shipping
            const existing = rawData['Total'].find(objeto => objeto.year === item.year && objeto.month === item.month);
            const index = rawDataShipping.findIndex(objeto => objeto.year === item.year && objeto.month === item.month);
            let shipping = 0;
            if (index !== -1) {
                shipping = parseFloat(rawDataShipping[index].totalS);
                rawDataShipping.splice(index, 1); // Remove depois de usar, para nao duplicar
            }
            if (existing) {
                existing.totalS += parseFloat(item.totalS) + shipping;
            } else {
                rawData['Total'].push({
                    year: item.year,
                    month: item.month,
                    totalS: parseFloat(item.totalS) + shipping
                });
            }
        });

        // Ordena por ano e mês dentro de cada categoria, e cria o label "Mes Ano"
        for (const category in rawData) {
            rawData[category].sort((a, b) => {
                if (a.year === b.year) return a.month - b.month;
                return a.year - b.year;
            });

            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            rawData[category] = rawData[category].map(entry => {
                return {
                    month: `${monthNames[entry.month - 1]} ${entry.year}`,
                    totalS: entry.totalS
                };
            });
        }

        const ctx = document.getElementById('categoryChart').getContext('2d');
        let chart;

        function updateCategoryChart(category) {
            let months = [], totals = [];

            const dataPoints = rawData[category] || [];
            months = dataPoints.map(entry => entry.month);
            totals = dataPoints.map(entry => entry.totalS);

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
                        y: { beginAtZero: true },
                        x: {
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#ccc' : '#333'
                            }
                        }
                    }
                }
            });
        }

        // Cria botões dinamicamente, incluindo botão Total
        function createCategoryButtons() {
            const container = document.getElementById('categoryButtons');
            container.innerHTML = '';

            Object.keys(rawData).forEach(category => {
                const btn = document.createElement('button');
                btn.textContent = category.charAt(0).toUpperCase() + category.slice(1);
                btn.className = "px-4 py-2 bg-gray-200 hover:bg-blue-500 hover:text-white rounded dark:bg-gray-700 dark:hover:bg-blue-500 dark:text-white";
                btn.addEventListener('click', () => updateCategoryChart(category));
                container.appendChild(btn);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            createCategoryButtons();
            updateCategoryChart('Total');
        });
    </script>
</x-layouts.main-content>
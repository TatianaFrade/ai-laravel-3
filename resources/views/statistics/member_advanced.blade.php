<x-layouts.main-content title="Advanced Statistics" heading="Advanced Personal View" subheading="Your shopping habits">
    <div class="p-6 space-y-8">

        {{-- Navigation --}}
        <div class="flex gap-4 mb-6">
            <a href="{{ route('statistics.basic') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Basic</a>
            <a href="{{ route('statistics.advanced') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Advanced</a>
        </div>

        {{-- Frequent Products --}}
        <div class="bg-white dark:bg-gray-900 p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2 dark:text-white">🛍️ Most Purchased Products</h3>
            <table class="min-w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-100 dark:bg-gray-800 dark:text-gray-200">
                    <tr>
                        <th class="px-3 py-2">Product</th>
                        <th class="px-3 py-2">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['frequent_products'] as $prod)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-3 py-2">{{ $prod->name }}</td>
                            <td class="px-3 py-2">{{ $prod->total_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Export button --}}
        <a href="{{ route('statistics.export.user_spending') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            📥 Export Spending Data
        </a>

        {{-- Spending by Category --}}
        <div class="bg-white dark:bg-gray-900 p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-2 dark:text-white">💸 Spending by Month & Category</h3>

            <div id="categoryButtons" class="flex flex-wrap gap-3 mb-4">
                {{-- Buttons added by JS --}}
            </div>

            <canvas id="categoryChart" class="mb-4"></canvas>

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

	// Agrupa dados por categoria
        const rawData = {};
        rawDataFlat.forEach(item => {
            if (!rawData[item.category]) rawData[item.category] = [];
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            rawData[item.category].push({
                month: monthNames[item.month - 1] || item.month,
                totalS: parseFloat(item.totalS)
            });
        });

	// Função que calcula o total por mês somando todas as categorias
        function getTotalByMonth() {
            const totalsByMonth = {};
            Object.values(rawData).forEach(categoryData => {
                categoryData.forEach(({month, totalS}) => {
                    totalsByMonth[month] = (totalsByMonth[month] || 0) + totalS;
                });
            });
            const months = Object.keys(totalsByMonth);
            const totals = months.map(m => totalsByMonth[m]);
            return {months, totals};
        }

        const ctx = document.getElementById('categoryChart').getContext('2d');
        let chart;

        function updateCategoryChart(category) {
            let months = [], totals = [];

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
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Spending (€)',
                        data: totals,
                        borderColor: 'rgba(34,197,94,1)',
                        backgroundColor: 'rgba(34,197,94,0.3)',
                        tension: 0.3
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#ccc' : '#333'
                            }
                        },
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

            const totalBtn = document.createElement('button');
            totalBtn.textContent = "Total";
            totalBtn.className = "px-4 py-2 bg-gray-200 hover:bg-green-500 hover:text-white rounded dark:bg-gray-700 dark:text-white dark:hover:bg-green-600";
            totalBtn.addEventListener('click', () => updateCategoryChart('Total'));
            container.appendChild(totalBtn);

            Object.keys(rawData).forEach(category => {
                const btn = document.createElement('button');
                btn.textContent = category.charAt(0).toUpperCase() + category.slice(1);
                btn.className = "px-4 py-2 bg-gray-200 hover:bg-green-500 hover:text-white rounded dark:bg-gray-700 dark:text-white dark:hover:bg-green-600";
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

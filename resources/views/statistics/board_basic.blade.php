<x-layouts.main-content title="Statistics" heading="General Statistics" subheading="Overview of grocery store data">
    <!-- Basic / Advanced Navigation -->
    <div class="flex justify-end gap-4 mb-6">
        <a href="{{ route('statistics.basic') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Basic</a>
        <a href="{{ route('statistics.advanced') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Advanced</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Users -->
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">👥 Users</h3>
            <table class="w-full text-sm text-left text-gray-700">
                <tbody>
                    <tr>
                        <th class="py-1">Total</th>
                        <td class="py-1">{{ $data['total_users'] }}</td>
                    </tr>
                    @foreach($data['users_by_type'] as $group)
                    <tr class="bg-gray-50">
                        <th class="py-1">{{ ucfirst($group->type) }}</th>
                        <td class="py-1">{{ $group->total }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <th class="py-1">Pending</th>
                        <td class="py-1 text-yellow-600 font-medium">{{ $data['pending_members'] }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="py-1">Cards Issued</th>
                        <td class="py-1">{{ $data['total_cards'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Products -->
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">🛒 Products</h3>
            <table class="w-full text-sm text-left text-gray-700">
                <tbody>
                    <tr>
                        <th class="py-1">Available</th>
                        <td class="py-1">{{ $data['products_available'] }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="py-1">Low Stock</th>
                        <td class="py-1 text-red-600 font-medium">{{ $data['products_low_stock'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total Sales -->
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">💰 Total Sales</h3>
            <p class="text-3xl font-bold text-green-600">
                €{{ number_format($data['total_sales_value'], 2, ',', '.') }}
            </p>
        </div>

        <!-- Orders -->
        <div class="bg-white shadow rounded-lg p-4 md:col-span-2 lg:col-span-3">
            <h3 class="text-lg font-semibold mb-2">📦 Orders by Status</h3>
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-1">Status</th>
                        <th class="px-3 py-1">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['orders_by_status'] as $order)
                    <tr class="border-t">
                        <td class="px-3 py-1">{{ ucfirst($order->status) }}</td>
                        <td class="px-3 py-1">{{ $order->total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.main-content>

<x-layouts.main-content title="Statistics" heading="Basic Statistics" subheading="Summary of your store activity">
    <!-- Basic / Advanced Navigation -->
    <div class="flex justify-end gap-4 mb-6">
        <a href="{{ route('statistics.basic') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">Basic</a>
        <a href="{{ route('statistics.advanced') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded shadow hover:bg-gray-300">Advanced</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Orders -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">📦 Total Orders</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $data['total_orders'] }}</p>
        </div>

        <!-- Total Spent -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">💸 Total Spent</h3>
            <p class="text-3xl font-bold text-green-600">
                €{{ number_format($data['total_spent'], 2, ',', '.') }}
            </p>
        </div>

        <!-- Last Order -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">🕓 Last Order</h3>
            @if ($data['last_order'])
                <p><strong>No.:</strong> {{ $data['last_order']->id }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($data['last_order']->date)->format('d/m/Y') }}</p>
                <p><strong>Value:</strong> €{{ number_format($data['last_order']->total, 2, ',', '.') }}</p>
            @else
                <p class="text-gray-500">You haven't placed any orders yet.</p>
            @endif
        </div>
    </div>
</x-layouts.main-content>

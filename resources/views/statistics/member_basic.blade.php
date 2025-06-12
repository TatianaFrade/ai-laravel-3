<x-layouts.main-content title="Statistics" heading="Basic Statistics" subheading="Summary of your store activity">
    <!-- Basic / Advanced Navigation -->
    <div class="flex justify-end gap-4 mb-6">
        <a href="{{ route('statistics.basic') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">Basic</a>
        <a href="{{ route('statistics.advanced') }}" class="px-4 py-2 bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600">Advanced</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Orders -->
        <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2 dark:text-white">📦 Total Orders</h3>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $data['total_orders'] }}</p>
        </div>

        <!-- Total Spent -->
        <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2 dark:text-white">💸 Total Spent</h3>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                €{{ number_format($data['total_spent'], 2, ',', '.') }}
            </p>
        </div>

        <!-- Last Order -->
        <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2 dark:text-white">🕓 Last Order</h3>
            @if ($data['last_order'])
                <p class="text-gray-800 dark:text-gray-200"><strong>No.:</strong> {{ $data['last_order']->id }}</p>
                <p class="text-gray-800 dark:text-gray-200"><strong>Date:</strong> {{ \Carbon\Carbon::parse($data['last_order']->date)->format('d/m/Y') }}</p>
                <p class="text-gray-800 dark:text-gray-200"><strong>Value:</strong> €{{ number_format($data['last_order']->total, 2, ',', '.') }}</p>
            @else
                <p class="text-gray-500 dark:text-gray-400">You haven't placed any orders yet.</p>
            @endif
        </div>
    </div>
</x-layouts.main-content>

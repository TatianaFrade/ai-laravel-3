<x-layouts.main-content title="Estatísticas" heading="Estatísticas Básicas" subheading="Resumo da sua atividade na loja">
    <!-- Navegação Básico / Avançado -->
    <div class="flex justify-end gap-4 mb-6">
        <a href="{{ route('statistics.basic') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">Básico</a>
        <a href="{{ route('statistics.advanced') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded shadow hover:bg-gray-300">Avançado</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total de Encomendas -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">📦 Total de Encomendas</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $data['total_orders'] }}</p>
        </div>

        <!-- Total Gasto -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">💸 Total Gasto</h3>
            <p class="text-3xl font-bold text-green-600">
                €{{ number_format($data['total_spent'], 2, ',', '.') }}
            </p>
        </div>

        <!-- Última Encomenda -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">🕓 Última Encomenda</h3>
            @if ($data['last_order'])
                <p><strong>Nº:</strong> {{ $data['last_order']->id }}</p>
                <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($data['last_order']->date)->format('d/m/Y') }}</p>
                <p><strong>Valor:</strong> €{{ number_format($data['last_order']->total, 2, ',', '.') }}</p>
            @else
                <p class="text-gray-500">Ainda não realizou nenhuma encomenda.</p>
            @endif
        </div>
    </div>
</x-layouts.main-content>

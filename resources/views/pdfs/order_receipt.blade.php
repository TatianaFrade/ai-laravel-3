<h1>Recibo da Encomenda</h1>
<p><strong>Cliente:</strong> {{ $order->user->name }}</p>
<p><strong>Encomenda:</strong> #{{ $order->id }}</p>
<p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
<p><strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}</p>

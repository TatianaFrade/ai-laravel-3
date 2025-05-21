@extends('layouts.app') <!-- ajuste o layout conforme o seu -->

@section('content')
<div class="container">
    <h1>Pagamento da Associação</h1>

    @if(session('error'))
        <div style="color: red;">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif

    <h2>Seu Cartão Virtual</h2>

    <p><strong>Número do Cartão:</strong> {{ $virtualCard->card_number }}</p>
    <p><strong>Data de Expiração:</strong> {{ $virtualCard->expiration_date->format('m/Y') }}</p>

    <form method="POST" action="{{ route('membership.processPayment') }}">
        @csrf
        <button type="submit">Pagar Associação</button>
    </form>
</div>
@endsection

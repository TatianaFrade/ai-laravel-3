@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Adicionar Crédito ao Cartão</h2>
        <form action="{{ route('credit.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="card_id" class="form-label">Cartão ID:</label>
                <input type="number" name="card_id" id="card_id" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="service" class="form-label">Serviço:</label>
                <select name="service" id="service" class="form-control">
                    <option value="Visa">Visa</option>
                    <option value="PayPal">PayPal</option>
                    <option value="MB WAY">MB WAY</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="reference" class="form-label">Referência:</label>
                <input type="text" name="reference" id="reference" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Valor (€):</label>
                <input type="number" name="amount" id="amount" required class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Efetuar Pagamento</button>
        </form>
    </div>
@endsection
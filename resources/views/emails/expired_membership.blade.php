<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Adesão Expirada</title>
</head>
<body class="min-h-screen bg-white flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white rounded-xl shadow p-6 flex flex-col gap-6 text-center">
        <h1 class="text-xl font-semibold text-black">
            A sua adesão expirou
        </h1>

        <p class="text-black">
            Verificámos que tentou realizar uma compra, mas a sua adesão anual já expirou.
        </p>

        <p class="text-black">
            Neste momento, não poderá concluir a compra até renovar a sua adesão.
        </p>

        <a href="{{ route('membershipfees.index') }}"
           class="inline-block w-full px-4 py-2 text-center text-white bg-blue-600 hover:bg-blue-700 rounded-md transition">
            Renovar adesão
        </a>

        <p class="text-sm text-black">
            Se precisar de ajuda, não hesite em contactar-nos.
        </p>

        <p class="text-sm text-black">
            Obrigado,<br>
            {{ config('app.name') }}
        </p>
    </div>
</body>
</html>

@component('mail::message')
# Encomenda Concluída

Olá {{ $order->user->name }},

A sua encomenda #{{ $order->id }} foi marcada como concluída com sucesso.

Em anexo, segue o recibo em PDF.

Obrigado por comprar connosco!

@endcomponent

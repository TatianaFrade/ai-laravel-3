@php
    $mode = $mode ?? 'edit';
    $readonly = $mode === 'show';

    // Se quiseres alguma lógica específica para campos diferentes, adiciona aqui
    // Por exemplo, se alguns campos forem só leitura dependendo de outras condições
    $disableName = $readonly;
    $disableImage = $readonly;
@endphp

<div class="w-full sm:w-96">
   

    <flux:input 
        name="min_value_threshold" 
        label="Minimum value" 
        value="{{ old('min_value_threshold', $cost->min_value_threshold ?? '') }}" 
        :disabled="$disableName" 
        :placeholder="__('Required')" 
    />

    <flux:input 
        name="max_value_threshold" 
        label="Maximum value" 
        value="{{ old('max_value_threshold', $cost->max_value_threshold ?? '') }}" 
        :disabled="$disableName" 
        :placeholder="__('Optional')" 
    />

    <flux:input 
        name="shipping_cost" 
        label="Shipping cost" 
        value="{{ old('shipping_cost', $cost->shipping_cost ?? '') }}" 
        :disabled="$disableName" 
        :placeholder="__('Required')" 
    />

  
</div>



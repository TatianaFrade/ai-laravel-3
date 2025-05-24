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
        name="name" 
        label="Name" 
        value="{{ old('name', $category->name ?? '') }}" 
        :disabled="$disableName" 
        :placeholder="__('Required')" 
    />
    @if ($disableName)
        <input type="hidden" name="name" value="{{ old('name', $category->name ?? '') }}">
    @endif
</div>

@if(isset($category) && isset($category->image) && $category->image)
    <img src="{{ asset('storage/categories/' . $category->image) }}" 
         alt="Category image" 
         class="h-24 w-24 object-cover rounded-full mb-4" />
@endif

<flux:input
    name="image"
    label="{{ __('Category Photo') }}"
    type="file"
    accept="image/*"
    :disabled="$disableImage"
/>
@if ($disableImage)
    <input type="hidden" name="image" value="{{ old('image', $category->image ?? '') }}">
@endif

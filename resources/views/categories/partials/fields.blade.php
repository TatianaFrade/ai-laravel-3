@php
    $imagePath = 'storage/categories/' . ($category->image ?? '');
@endphp

<div class="h-24 w-24 mb-4 rounded-full overflow-hidden">
    @if (!empty($category->image) && file_exists(public_path($imagePath)))
        <img src="{{ asset($imagePath) }}"
             alt="Category image"
             class="w-full h-full object-cover" />
    @else
        <div class="w-full h-full flex items-center justify-center text-sm text-gray-500 border border-dashed border-gray-300">
            No photo
        </div>
    @endif
</div>

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

{{-- Variables are now passed from the controller --}}

@if($mode !== 'create')
    <div class="flex items-start gap-4">
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
    </div>
@endif

@if ($readonly)
    <flux:input
        name="name"
        label="Name"
        value="{{ old('name', $category->name ?? '') }}"
        readonly
    />
    <input type="hidden" name="name" value="{{ old('name', $category->name ?? '') }}">
@else
    <flux:input
        name="name"
        label="Name"
        value="{{ old('name', $category->name ?? '') }}"
        required
    />
@endif

<flux:input
    name="image"
    label="Category Photo"
    type="file"
    accept="image/*"
    :disabled="$disableImage"
/>

@if ($disableImage)
    <input type="hidden" name="image" value="{{ old('image', $category->image ?? '') }}">
@endif

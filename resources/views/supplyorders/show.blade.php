<x-layouts.main-content :title="$supplyorder->name"
    :heading="'supplyorder '. $supplyorder->name">
<div class="flex flex-col space-y-6">
<div class="max-full">
<section>
<div class="mt-6 space-y-4">
    @include('supplyorders.partials.fields', ['mode' => 'show'])
</div>
</form>
</section>
</div>
</div>
</x-layouts.main-content>



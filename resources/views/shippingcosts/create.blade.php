<x-layouts.main-content title="New Shipping Cost"
                        heading="Create a Shipping cost"
                        subheading='Click on "Save" button to store the information.'>
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <form method="POST" action="{{ route('shippingcosts.store') }}" enctype="multipart/form-data">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-900 text-red-300 rounded border border-red-700 shadow-md">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif  

                    <div class="mt-6 space-y-4">
                        @include('shippingcosts.partials.fields', ['mode' => 'create'])
                    </div>                    <div class="flex mt-6">
                        <flux:button variant="primary" type="submit" class="uppercase">Save</flux:button>
                        <flux:button class="uppercase ms-4" href="{{ route('shippingcosts.index') }}">Cancel</flux:button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.main-content>

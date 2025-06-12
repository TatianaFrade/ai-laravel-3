<x-layouts.main-content :title="$order->name"
                        heading="Edit order"
                        :subheading="$order->name">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <form method="POST" action="{{ route('orders.update', ['order' => $order]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')


                    <div class="mt-6 space-y-4">
                        @include('orders.partials.fields', ['mode' => 'edit'])
                    </div>

                    <div class="flex mt-6">
                        <flux:button variant="primary" type="submit" class="uppercase">Save</flux:button>
                        <flux:button class="uppercase ms-4" href="{{ route('orders.index') }}">Cancel</flux:button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.main-content>

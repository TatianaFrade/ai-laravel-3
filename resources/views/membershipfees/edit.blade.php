<x-layouts.main-content :title="$membershipfee->name"
                        heading="Edit Membership Fee"
                        subheading="Value to be paid in order to complete purchases">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <form method="POST" action="{{ route('membershipfees.update', ['membershipfee' => $membershipfee]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-900 text-red-300 rounded bmembershipfee bmembershipfee-red-700 shadow-md">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-6 space-y-4">
                        @include('membershipfees.partials.fields', ['mode' => 'edit'])
                    </div>
                    <div class="flex items-center gap-4 mt-6">
                        <flux:button variant="primary" type="submit" class="uppercase">Save</flux:button>
                        <flux:button class="uppercase" href="{{ route('membershipfees.index') }}">Cancel</flux:button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.main-content>

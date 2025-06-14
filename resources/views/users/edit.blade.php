<x-layouts.main-content :title="$user->name" heading="Edit user" :subheading="$user->name">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <form method="POST" action="{{ route('users.update', ['user' => $user]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mt-6 space-y-4">
                        @include('users.partials.fields', [
                            'mode' => 'edit',
                            'editableFields' => $editableFields
                        ])

                    </div>                    <div class="flex mt-6">
                        <flux:button variant="primary" type="submit" class="uppercase">Save</flux:button>
                        <flux:button class="uppercase ms-4" href="{{ route('users.index') }}">Cancel</flux:button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.main-content>

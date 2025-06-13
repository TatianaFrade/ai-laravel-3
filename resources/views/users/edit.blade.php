<x-layouts.main-content :title="$user->name" heading="Edit user" :subheading="$user->name">
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <form method="POST" action="{{ route('users.update', ['user' => $user]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

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

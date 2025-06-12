<x-layouts.main-content title="New Product"
                        heading="Create a Product"
                        subheading='Click on "Save" button to store the information.'>
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
               <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" id="product-form">
                    @csrf
                    <div class="mt-6 space-y-4">
                        @include('products.partials.fields', ['mode' => 'create'])
                    </div>
                    <div class="flex mt-6">
                        <flux:button variant="primary" type="submit"  class="uppercase">Save</flux:button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    {{-- Validate description field --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('product-form');
            const descriptionField = form.querySelector('[name="description"]');

            form.addEventListener('submit', function(e) {
                const description = descriptionField.value.trim();
                if (!description) {
                    if (!confirm('The description field is empty. Are you sure you want to continue?')) {
                        e.preventDefault();
                        descriptionField.focus();
                    }
                }
            });
        });
    </script>
</x-layouts.main-content>



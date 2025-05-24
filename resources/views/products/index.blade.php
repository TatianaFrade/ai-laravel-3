<x-layouts.main-content :title="__('Products')" heading="List of Products">
	<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
		<div class="flex items-center gap-4 mb-4">
			<flux:button variant="primary" href="{{ route('products.create') }}">Create a new product</flux:button>
		</div>
		<div class="flex justify-start">
			<div class="my-4 p-6 w-full">
				<div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
				<x-products.table
					:products="$allProducts"
					:showView="true"
					:showEdit="true"
					:showDelete="true"
					:showAddToCart="true"
					:showRemoveFromCart="false"
				/>
				</div>
				<div class="mt-4">
					{{ $allProducts->links() }}
				</div>
			</div>
		</div>
	</div>
</x-layouts.main-content>
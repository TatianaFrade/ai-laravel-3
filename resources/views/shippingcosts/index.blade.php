<x-layouts.main-content :title="__('Shipping costs')" heading="Shipping costs to be paid"
    subheading="Costs already defined">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex justify-start">
            <div class="my-4 p-6 w-full">
                <div class="flex items-center gap-4 mb-4">
                    <flux:button variant="primary" href="{{ route('shippingcosts.create') }}">
                        Create a new shipping cost
                    </flux:button>
                </div>
                <div class="my-4 font-base text-sm text-gray-700 dark:text-gray-300">
                    <table class="table w-full border-collapse">
                        <thead>
                            <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                                <th class="px-3 py-2 text-center">#</th>
                                <th class="px-3 py-2 text-center">Min value</th>
                                <th class="px-3 py-2 text-center">Max value</th>
                                <th class="px-3 py-2 text-center">Shipping cost</th>
                                <th class="px-3 py-2 text-center">Status</th>
                                <th class="px-3 py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($costs as $cost)
                                <tr>
                                    <td class="px-3 py-2 text-center">{{ $loop->iteration }}</td>
                                    <td class="px-3 py-2 text-center">{{ $cost->min_value_threshold ?? '—' }}</td>
                                    <td class="px-3 py-2 text-center">{{ $cost->max_value_threshold ?? '—' }}</td>
                                    <td class="px-3 py-2 text-center">{{ $cost->shipping_cost ?? '—' }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @if($cost->trashed())
                                            <span class="text-red-600 font-semibold">Deleted</span>
                                        @else
                                            <span class="text-green-600">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('shippingcosts.show', $cost) }}" title="View">
                                                <flux:icon.eye class="size-5 hover:text-gray-600" />
                                            </a>

                                            @if(!$cost->trashed())
                                                <a href="{{ route('shippingcosts.edit', $cost) }}" title="Edit">
                                                    <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                                                </a>

                                                <form method="POST" action="{{ route('shippingcosts.destroy', $cost) }}"
                                                    class="inline-flex"
                                                    onsubmit="return confirm('Are you sure you want to delete this shipping cost?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete">
                                                        <flux:icon.trash class="size-5 hover:text-red-600" />
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST"
                                                    action="{{ route('shippingcosts.restore', ['shippingcost' => $cost->id]) }}"
                                                    class="inline-flex">
                                                    @csrf
                                                    <button type="submit" title="Restore shipping cost">
                                                        <flux:icon.arrow-path-rounded-square class="size-5 hover:text-green-600" />
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.main-content>

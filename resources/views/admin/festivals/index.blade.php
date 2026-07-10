<x-admin-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-wide text-primary">Marketing</p>
            <h1 class="text-4xl font-black text-ink">Festival Offers</h1>
        </div>
        <a href="{{ route('admin.festivals.create') }}" class="rounded bg-primary px-5 py-2 font-semibold text-white">Add Festival</a>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="p-4">Offer</th>
                    <th class="p-4">Discount</th>
                    <th class="p-4">Categories</th>
                    <th class="p-4">Extra Products</th>
                    <th class="p-4">Dates</th>
                    <th class="p-4">Status</th>
                    <th class="p-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($festivals as $festival)
                    <tr>
                        <td class="p-4 font-semibold text-ink">{{ $festival->title }}</td>
                        <td class="p-4">{{ number_format($festival->discount_percentage, 2) }}%</td>
                        <td class="p-4">{{ $festival->categories_count }}</td>
                        <td class="p-4">{{ $festival->products_count }}</td>
                        <td class="p-4">
                            {{ $festival->starts_at?->format('d M Y') ?? 'Any time' }}
                            -
                            {{ $festival->ends_at?->format('d M Y') ?? 'No end' }}
                        </td>
                        <td class="p-4">{{ $festival->is_active ? 'Active' : 'Hidden' }}</td>
                        <td class="p-4 text-right">
                            <a href="{{ route('festivals.show', $festival) }}" class="mr-3 font-semibold text-ink" target="_blank">View</a>
                            <a href="{{ route('admin.festivals.edit', $festival) }}" class="font-semibold text-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.festivals.destroy', $festival) }}" class="ml-3 inline" onsubmit="return confirm('Delete this festival offer?')">
                                @csrf
                                @method('DELETE')
                                <button class="font-semibold text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-8 text-center text-gray-500">No festival offers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $festivals->links() }}</div>
</x-admin-layout>

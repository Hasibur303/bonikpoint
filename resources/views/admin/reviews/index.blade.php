<x-admin-layout>
    <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-primary">Customer Trust</p>
            <h1 class="mt-1 text-3xl font-black text-ink">Product Reviews</h1>
            <p class="mt-2 text-sm text-gray-500">Approve customer ratings before they appear on product pages.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'all' => 'All'] as $value => $label)
                <a href="{{ route('admin.reviews.index', ['status' => $value]) }}" class="rounded-md px-4 py-2 text-sm font-black transition {{ $status === $value ? 'bg-ink text-white shadow-sm' : 'bg-white text-gray-600 ring-1 ring-gray-100 hover:text-primary' }}">
                    {{ $label }}
                    @if($value === 'pending' && $pendingCount)
                        <span class="ml-1 rounded bg-primary px-1.5 py-0.5 text-[10px] text-white">{{ $pendingCount }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="p-4">Product</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Rating</th>
                        <th class="p-4">Comment</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reviews as $review)
                        <tr class="align-top">
                            <td class="p-4">
                                <a href="{{ $review->product ? route('shop.show', $review->product) : '#' }}" target="_blank" class="font-black text-ink hover:text-primary">
                                    {{ $review->product?->name ?? 'Deleted product' }}
                                </a>
                                <p class="mt-1 text-xs text-gray-400">{{ $review->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="p-4">
                                <p class="font-bold text-ink">{{ $review->user?->name ?? 'Customer' }}</p>
                                <p class="text-xs text-gray-500">{{ $review->order?->order_number }}</p>
                            </td>
                            <td class="p-4">
                                <span class="font-black text-accent">{{ $review->rating }} / 5</span>
                            </td>
                            <td class="max-w-md p-4 text-gray-600">
                                {{ $review->comment ?: 'No comment added.' }}
                            </td>
                            <td class="p-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $review->is_approved ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex justify-end gap-2">
                                    @unless($review->is_approved)
                                        <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-md bg-primary px-3 py-2 text-xs font-black text-white hover:bg-ink">Approve</button>
                                        </form>
                                    @endunless
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-3 py-2 text-xs font-black text-red-600 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-gray-500">No reviews found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
</x-admin-layout>

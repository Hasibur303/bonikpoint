<x-admin-layout>
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-primary">Growth</p>
            <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Festival Offers</h1>
            <p class="mt-2 text-sm text-gray-500">Schedule campaign banners and category-wide discounts.</p>
        </div>
        <a href="{{ route('admin.festivals.create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-primary px-5 text-sm font-black text-white shadow-[0_8px_20px_rgba(8,124,127,0.16)] transition hover:bg-ink"><i class="fa-solid fa-plus text-xs"></i>Add Festival</a>
    </div>

    <section class="overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
        <div class="border-b border-[#e7edeb] px-5 py-3.5 text-xs font-bold text-gray-500">{{ $festivals->total() }} {{ str('campaign')->plural($festivals->total()) }}</div>
        <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full text-left text-sm">
                <thead><tr><th class="px-5 py-3">Campaign</th><th class="px-4 py-3">Discount</th><th class="px-4 py-3">Selection</th><th class="px-4 py-3">Schedule</th><th class="px-4 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse($festivals as $festival)
                        @php
                            $isScheduled = $festival->starts_at && $festival->starts_at->isFuture();
                            $isExpired = $festival->ends_at && $festival->ends_at->isPast();
                        @endphp
                        <tr>
                            <td class="px-5 py-3.5"><div class="flex items-center gap-3">
                                @if($festival->banner)
                                    <img src="{{ $festival->banner_url }}" alt="{{ $festival->title }}" width="80" height="52" loading="lazy" decoding="async" class="h-12 w-20 rounded-md border border-gray-100 bg-gray-50 object-cover">
                                @else
                                    <span class="grid h-12 w-20 shrink-0 place-items-center rounded-md bg-amber-50 text-amber-700"><i class="fa-solid fa-image"></i></span>
                                @endif
                                <div><span class="font-black text-ink">{{ $festival->title }}</span><p class="mt-0.5 text-[11px] text-gray-400">Festival promotion</p></div>
                            </div></td>
                            <td class="px-4 py-3.5"><span class="inline-flex rounded-md bg-[#f0f5d7] px-2.5 py-1 text-xs font-black text-[#617311]">{{ number_format($festival->discount_percentage, 0) }}% off</span></td>
                            <td class="px-4 py-3.5"><span class="block text-xs font-bold text-ink">{{ $festival->categories_count }} categories</span><span class="mt-0.5 block text-[11px] text-gray-400">{{ $festival->products_count }} extra products</span></td>
                            <td class="px-4 py-3.5"><span class="block text-xs font-bold text-gray-700">{{ $festival->starts_at?->format('d M Y') ?? 'Any time' }}</span><span class="mt-0.5 block text-[11px] text-gray-400">to {{ $festival->ends_at?->format('d M Y') ?? 'No end date' }}</span></td>
                            <td class="px-4 py-3.5">
                                @if($isExpired)
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-black text-gray-600 ring-1 ring-gray-200">Expired</span>
                                @elseif($isScheduled && $festival->is_active)
                                    <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-black text-blue-700 ring-1 ring-blue-200">Scheduled</span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black ring-1 {{ $festival->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-gray-100 text-gray-600 ring-gray-200' }}">{{ $festival->is_active ? 'Active' : 'Hidden' }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5"><div class="flex justify-end gap-1.5">
                                <a href="{{ route('festivals.show', $festival) }}" target="_blank" rel="noopener noreferrer" class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-500 hover:border-primary hover:text-primary" aria-label="View {{ $festival->title }}" title="View campaign"><i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i></a>
                                <a href="{{ route('admin.festivals.edit', $festival) }}" class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-500 hover:border-primary hover:text-primary" aria-label="Edit {{ $festival->title }}" title="Edit campaign"><i class="fa-solid fa-pen text-[10px]"></i></a>
                                <form method="POST" action="{{ route('admin.festivals.destroy', $festival) }}" onsubmit="return confirm('Delete this festival offer?')">@csrf @method('DELETE')<button class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-400 hover:border-red-200 hover:bg-red-50 hover:text-red-600" aria-label="Delete {{ $festival->title }}" title="Delete campaign"><i class="fa-regular fa-trash-can text-xs"></i></button></form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-14 text-center"><span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-gray-100 text-gray-400"><i class="fa-solid fa-tags"></i></span><p class="mt-3 font-bold text-ink">No festival offers yet</p><p class="mt-1 text-xs text-gray-500">Create a campaign when your next promotion is ready.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="mt-6">{{ $festivals->links() }}</div>
</x-admin-layout>

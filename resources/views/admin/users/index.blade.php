<x-admin-layout>
    <div class="mb-6">
        <p class="text-xs font-black uppercase tracking-wide text-primary">People</p>
        <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Customers</h1>
        <p class="mt-2 text-sm text-gray-500">Review registered customer accounts. Administrator access is managed separately.</p>
    </div>

    <section class="overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
        <div class="border-b border-[#e7edeb] px-5 py-3.5 text-xs font-bold text-gray-500">{{ $users->total() }} registered {{ str('account')->plural($users->total()) }}</div>
        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full text-left text-sm">
                <thead><tr><th class="px-5 py-3">Customer</th><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Joined</th><th class="px-5 py-3 text-right">Role</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-5 py-4"><div class="flex items-center gap-3"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#e5f4f2] text-sm font-black uppercase text-primary">{{ str($user->name)->substr(0, 1) }}</span><div><span class="font-black text-ink">{{ $user->name }}</span><p class="mt-0.5 text-[11px] text-gray-400">Customer #{{ $user->id }}</p></div></div></td>
                            <td class="px-4 py-4"><span class="block text-xs font-bold text-gray-700">{{ $user->email }}</span><span class="mt-0.5 block text-[11px] text-gray-400">{{ $user->mobile ?: 'No mobile number' }}</span></td>
                            <td class="px-4 py-4"><span class="block text-xs font-bold text-gray-600">{{ $user->created_at?->format('d M Y') }}</span><span class="text-[11px] text-gray-400">{{ $user->created_at?->diffForHumans() }}</span></td>
                            <td class="px-5 py-4 text-right"><span class="inline-flex rounded-full bg-[#edf5f3] px-2.5 py-1 text-[10px] font-black text-primary ring-1 ring-[#cde4df]">Customer</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-14 text-center text-sm text-gray-500">No customer accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="mt-6">{{ $users->links() }}</div>
</x-admin-layout>

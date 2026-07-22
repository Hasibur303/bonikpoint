<x-admin-layout>
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-primary">Security</p>
            <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Administrators</h1>
            <p class="mt-2 text-sm text-gray-500">Manage staff accounts with access to the admin panel.</p>
        </div>

        <a href="{{ route('admin.users.admins.create') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-ink px-4 text-xs font-black text-white shadow-sm hover:bg-primary">
            <i class="fa-solid fa-user-plus"></i>
            Add Administrator
        </a>
    </div>

    <section class="overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
        <div class="border-b border-[#e7edeb] px-5 py-3.5 text-xs font-bold text-gray-500">{{ $admins->total() }} administrator {{ str('account')->plural($admins->total()) }}</div>
        <div class="overflow-x-auto">
            <table class="min-w-[760px] w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-5 py-3">Administrator</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Joined</th>
                        <th class="px-5 py-3 text-right">Access</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-amber-50 text-sm font-black uppercase text-amber-700">{{ str($admin->name)->substr(0, 1) }}</span>
                                    <div>
                                        <span class="font-black text-ink">{{ $admin->name }}</span>
                                        @if($admin->is(auth()->user()))
                                            <p class="mt-0.5 text-[11px] font-bold text-primary">Current admin</p>
                                        @else
                                            <p class="mt-0.5 text-[11px] text-gray-400">Admin #{{ $admin->id }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="block text-xs font-bold text-gray-700">{{ $admin->email }}</span>
                                <span class="mt-0.5 block text-[11px] text-gray-400">{{ $admin->mobile ?: 'No mobile number' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="block text-xs font-bold text-gray-600">{{ $admin->created_at?->format('d M Y') }}</span>
                                <span class="text-[11px] text-gray-400">{{ $admin->created_at?->diffForHumans() }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if($admin->is(auth()->user()))
                                    <span class="inline-flex rounded-full bg-[#edf5f3] px-2.5 py-1 text-[10px] font-black text-primary ring-1 ring-[#cde4df]">Protected</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.update', $admin) }}" onsubmit="return confirm('Remove administrator access for this account?')" class="inline-flex">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="utype" value="usr">
                                        <button class="inline-flex h-9 items-center gap-1.5 rounded-md border border-red-200 px-3 text-xs font-black text-red-600 transition hover:bg-red-50">
                                            <i class="fa-solid fa-user-minus text-[10px]"></i>
                                            Remove Admin
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-14 text-center text-sm text-gray-500">No administrator accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">{{ $admins->links() }}</div>
</x-admin-layout>

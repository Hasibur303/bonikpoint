<x-admin-layout>
    <div class="mb-6"><p class="text-sm font-bold uppercase tracking-wide text-primary">People</p><h1 class="text-4xl font-black text-ink">Users</h1></div>
    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-4">Name</th><th class="p-4">Email</th><th class="p-4">Mobile</th><th class="p-4">Role</th><th class="p-4"></th></tr></thead>
            <tbody class="divide-y">
                @foreach($users as $user)
                    <tr>
                        <td class="p-4 font-semibold text-ink">{{ $user->name }}</td>
                        <td class="p-4">{{ $user->email }}</td>
                        <td class="p-4">{{ $user->mobile }}</td>
                        <td class="p-4">{{ $user->utype === 'adm' ? 'Admin' : 'User' }}</td>
                        <td class="p-4 text-right">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="inline-flex gap-2">
                                @csrf @method('PATCH')
                                <select name="utype" class="rounded border-gray-200 text-sm">
                                    <option value="usr" @selected($user->utype === 'usr')>User</option>
                                    <option value="adm" @selected($user->utype === 'adm')>Admin</option>
                                </select>
                                <button class="rounded bg-primary px-3 py-2 text-sm font-semibold text-white">Save</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
</x-admin-layout>

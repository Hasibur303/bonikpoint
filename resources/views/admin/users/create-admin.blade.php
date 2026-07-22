<x-admin-layout>
    <div class="mb-6">
        <p class="text-xs font-black uppercase tracking-wide text-primary">Security</p>
        <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Add Administrator</h1>
        <p class="mt-2 text-sm text-gray-500">Create a separate staff account for admin panel access.</p>
    </div>

    <form method="POST" action="{{ route('admin.users.admins.store') }}" class="max-w-3xl rounded-lg border border-[#dfe7e5] bg-white p-5 shadow-sm sm:p-6">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="name" class="mb-1.5 block text-xs font-black uppercase text-gray-500">Full Name</label>
                <input id="name" name="name" value="{{ old('name') }}" required class="h-11 w-full rounded-md border-gray-200 text-sm focus:border-primary focus:ring-primary">
                @error('name')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-xs font-black uppercase text-gray-500">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="h-11 w-full rounded-md border-gray-200 text-sm focus:border-primary focus:ring-primary">
                @error('email')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="mobile" class="mb-1.5 block text-xs font-black uppercase text-gray-500">Mobile</label>
                <input id="mobile" name="mobile" value="{{ old('mobile') }}" class="h-11 w-full rounded-md border-gray-200 text-sm focus:border-primary focus:ring-primary">
                @error('mobile')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-xs font-black uppercase text-gray-500">Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="h-11 w-full rounded-md border-gray-200 text-sm focus:border-primary focus:ring-primary">
                @error('password')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-1.5 block text-xs font-black uppercase text-gray-500">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="h-11 w-full rounded-md border-gray-200 text-sm focus:border-primary focus:ring-primary">
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.users.admins') }}" class="inline-flex h-11 items-center justify-center rounded-md border border-gray-200 px-5 text-sm font-black text-ink hover:border-primary hover:text-primary">Cancel</a>
            <button class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-ink px-5 text-sm font-black text-white hover:bg-primary">
                <i class="fa-solid fa-user-shield"></i>
                Create Administrator
            </button>
        </div>
    </form>
</x-admin-layout>

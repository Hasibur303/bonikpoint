@php($layout = auth()->user()?->isAdmin() ? 'admin-layout' : 'user-dashboard-layout')

<x-dynamic-component :component="$layout">
    <div class="mb-6 sm:mb-8">
        <p class="text-xs font-black uppercase tracking-wide text-primary">Account settings</p>
        <h1 class="mt-1 text-2xl font-black text-ink sm:text-3xl">Profile & Security</h1>
        <p class="mt-2 text-sm text-gray-500">Keep your contact details and password up to date.</p>
    </div>

    <div class="max-w-4xl space-y-5">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            @include('profile.partials.update-password-form')
        </div>

        @unless(auth()->user()?->isAdmin())
            <div class="rounded-lg border border-red-100 bg-white p-5 shadow-sm sm:p-6">
                @include('profile.partials.delete-user-form')
            </div>
        @endunless
    </div>
</x-dynamic-component>

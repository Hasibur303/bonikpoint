<x-user-dashboard-layout>
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-wide text-primary">My Account</p>
        <h1 class="text-4xl font-black text-ink">Profile</h1>
    </div>

    <div class="max-w-4xl space-y-6">
        <div class="rounded-lg bg-white p-6 shadow-sm">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-user-dashboard-layout>

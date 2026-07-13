<x-guest-layout :wide="true">
    <style>
        @media (min-width: 1024px) {
            .login-brand-panel {
                background-color: #0f172a !important;
            }
        }
    </style>
    <div class="grid lg:min-h-[620px] lg:grid-cols-[0.9fr_1.1fr]">
        <section class="login-brand-panel relative overflow-hidden bg-white px-6 py-4 text-white lg:flex lg:flex-col lg:justify-between lg:px-12 lg:py-12">
            <div class="absolute inset-x-0 top-0 hidden h-1.5 bg-lime-500 lg:block"></div>

            <div class="flex justify-center lg:hidden">
                <a href="{{ route('home.index') }}" class="inline-flex" aria-label="Bonik Point home">
                    <x-application-logo class="h-12 max-h-12 w-auto object-contain" style="height: 48px; max-height: 48px; width: auto;" />
                </a>
            </div>

            <div class="hidden lg:block">
                <a href="{{ route('home.index') }}" class="inline-flex rounded-md bg-white p-2 shadow-lg" aria-label="Bonik Point home">
                    <x-application-logo class="h-14 w-auto sm:h-16 lg:h-20" />
                </a>

                <div class="mt-6 max-w-sm lg:mt-10">
                    <p class="text-sm font-semibold uppercase tracking-widest text-lime-400">Bonik Point Store</p>
                    <h1 class="mt-2 text-3xl font-black leading-tight sm:text-4xl lg:mt-3">Welcome back</h1>
                    <p class="mt-3 text-sm leading-6 text-slate-300 sm:text-base sm:leading-7 lg:mt-4">Sign in to manage your orders, delivery updates, reviews, and account details.</p>
                </div>
            </div>

            <div class="mt-10 hidden border-t border-slate-700 pt-5 text-sm text-slate-300 lg:block">
                <p class="font-semibold text-white">Need help?</p>
                <a href="tel:01540381020" class="mt-1 inline-block hover:text-lime-400">24 Hours Customer Service: 01540381020</a>
            </div>
        </section>

        <section class="flex items-center px-6 py-8 sm:px-10 lg:px-14 lg:py-12">
            <div class="mx-auto w-full max-w-md">
                <a href="{{ route('home.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-500 transition hover:text-teal-700">
                    <span aria-hidden="true" class="mr-2">&larr;</span> Back to store
                </a>

                <div class="mt-7">
                    <h2 class="text-2xl font-black text-slate-900">Sign in to your account</h2>
                    <p class="mt-2 text-sm text-slate-500">Enter your account email and password.</p>
                </div>

                <x-auth-session-status class="mt-5 rounded-md bg-teal-50 p-3 text-sm font-medium text-teal-800" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-7" x-data="{ showPassword: false }">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700">Email address</label>
                        <input id="email" class="mt-2 block w-full rounded-md border-slate-300 px-4 py-3 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-teal-600 focus:ring-teal-600" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-5">
                        <div class="flex items-center justify-between gap-4">
                            <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-sm font-semibold text-teal-700 transition hover:text-teal-900" href="{{ route('password.request') }}">Forgot password?</a>
                            @endif
                        </div>

                        <div class="relative mt-2">
                            <input id="password" class="block w-full rounded-md border-slate-300 py-3 pl-4 pr-16 text-slate-900 shadow-sm focus:border-teal-600 focus:ring-teal-600" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password">
                            <button type="button" class="absolute inset-y-0 right-0 px-4 text-xs font-bold uppercase text-slate-500 transition hover:text-teal-700 focus:outline-none" @click="showPassword = ! showPassword" x-text="showPassword ? 'Hide' : 'Show'" aria-label="Show or hide password"></button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <label for="remember_me" class="mt-5 inline-flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                        <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-teal-600 shadow-sm focus:ring-teal-500" name="remember">
                        <span>Keep me signed in</span>
                    </label>

                    <button type="submit" class="mt-6 inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-slate-900/20 transition hover:bg-teal-700 hover:shadow-teal-700/25 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                        Sign In
                    </button>

                    <div class="mt-7 border-t border-slate-200 pt-6 text-center">
                        <p class="text-sm text-slate-500">Don't have an account?</p>
                        <a href="{{ route('register') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-md border-2 border-lime-500 bg-lime-50 px-5 py-3 text-sm font-bold text-slate-900 shadow-lg shadow-lime-500/20 transition hover:bg-lime-500 hover:shadow-lime-500/30 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2">
                            Create Account
                        </a>
                        @if(\App\Http\Controllers\CartController::count() > 0)
                            <div class="mt-5 rounded-md border border-teal-100 bg-teal-50 p-4">
                                <p class="text-sm font-semibold text-slate-800">Want to order without an account?</p>
                                <a href="{{ route('guest.checkout.create') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-md border-2 border-teal-700 bg-teal-50 px-5 py-3 text-sm font-bold text-teal-900 shadow-lg shadow-teal-700/20 transition hover:bg-teal-700 hover:text-white hover:shadow-teal-700/30 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                    Order Without Account
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </section>
    </div>
</x-guest-layout>

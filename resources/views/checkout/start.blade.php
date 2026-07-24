@section('title', 'Choose Checkout Option | Bonik Point')
@section('meta_description', 'Choose guest checkout, sign in, or create an account to continue your Bonik Point order.')
@section('canonical', route('checkout.start'))
@section('robots', 'noindex,follow')

<x-app-layout>
    <main class="bg-[#f4f7f6] py-6 md:py-12">
        <div class="container max-w-4xl">
            <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:text-ink sm:text-sm">
                <i class="fa-solid fa-arrow-left"></i>
                Back to cart
            </a>

            <div class="mt-5 text-center md:mt-8">
                <p class="text-xs font-black uppercase tracking-wide text-primary">Secure Checkout</p>
                <h1 class="mt-2 text-2xl font-black text-ink sm:text-4xl">Choose how you want to order</h1>
                <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-gray-600 sm:text-base">Your cart is saved. Select the option that feels easiest for you.</p>
            </div>

            <div class="mx-auto mt-6 max-w-2xl rounded-lg border border-[#cddf8a] bg-[#f5f9df] px-4 py-4 text-left shadow-sm sm:mt-8 sm:px-5">
                <div class="flex gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#d5e77a] text-ink"><i class="fa-solid fa-circle-info"></i></span>
                    <div>
                        <h2 class="font-black text-ink">অ্যাকাউন্ট ছাড়াই অর্ডার করতে পারবেন</h2>
                        <p class="mt-1 text-sm leading-6 text-[#405248]">অ্যাকাউন্ট না থাকলেও সমস্যা নেই। নিচের <span class="font-black">Continue as Guest</span> বাটনে ক্লিক করে সহজে অর্ডার করুন।</p>
                    </div>
                </div>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-3 md:items-stretch">
                <section class="flex flex-col rounded-lg border-2 border-primary bg-white p-5 shadow-[0_14px_35px_rgba(8,124,127,0.13)]">
                    <span class="grid h-11 w-11 place-items-center rounded-md bg-primary text-white"><i class="fa-solid fa-bag-shopping"></i></span>
                    <h2 class="mt-4 text-lg font-black text-ink">Continue as Guest</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Place your order without a password or account.</p>
                    <p class="mt-3 rounded-md bg-amber-50 px-3 py-2 text-xs font-semibold leading-5 text-amber-800">Guest orders require advance delivery-charge payment when it applies.</p>
                    <a href="{{ route('guest.checkout.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-primary px-4 text-sm font-black text-white transition hover:bg-ink"><i class="fa-solid fa-arrow-right"></i>Continue as Guest</a>
                </section>

                <section class="flex flex-col rounded-lg border border-[#dfe7e5] bg-white p-5 shadow-sm">
                    <span class="grid h-11 w-11 place-items-center rounded-md bg-blue-50 text-blue-700"><i class="fa-solid fa-right-to-bracket"></i></span>
                    <h2 class="mt-4 text-lg font-black text-ink">Sign In</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Use your saved details and view your order history.</p>
                    <a href="{{ route('checkout.account', 'login') }}" class="mt-auto inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-primary bg-white px-4 text-sm font-black text-primary transition hover:bg-[#edf3f1]"><i class="fa-solid fa-right-to-bracket"></i>Sign In</a>
                </section>

                <section class="flex flex-col rounded-lg border border-[#dfe7e5] bg-white p-5 shadow-sm">
                    <span class="grid h-11 w-11 place-items-center rounded-md bg-[#f0f5d7] text-[#617311]"><i class="fa-solid fa-user-plus"></i></span>
                    <h2 class="mt-4 text-lg font-black text-ink">Create Account</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Save time later, manage orders, and use Pay Later when available.</p>
                    <a href="{{ route('checkout.account', 'register') }}" class="mt-auto inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-[#aabc44] bg-[#f5f9df] px-4 text-sm font-black text-ink transition hover:bg-[#d5e77a]"><i class="fa-solid fa-user-plus"></i>Create Account</a>
                </section>
            </div>

            <div class="mt-5 flex items-center justify-between rounded-lg border border-[#dfe7e5] bg-white px-5 py-4 text-sm shadow-sm">
                <span class="font-semibold text-gray-600">{{ count($cartItems) }} item{{ count($cartItems) === 1 ? '' : 's' }} in your cart</span>
                <span class="text-lg font-black text-ink">BDT {{ number_format($subtotal, 2) }}</span>
            </div>
        </div>
    </main>
</x-app-layout>

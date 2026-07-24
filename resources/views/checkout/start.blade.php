@section('title', 'Choose Checkout Option | Bonik Point')
@section('meta_description', 'Choose guest checkout, sign in, or create an account to continue your Bonik Point order.')
@section('canonical', route('checkout.start'))
@section('robots', 'noindex,follow')

<x-app-layout>
    <main class="bg-[#f4f7f6] py-4 sm:py-6 md:py-12">
        <div class="container max-w-4xl">
            <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:text-ink sm:text-sm">
                <i class="fa-solid fa-arrow-left"></i>
                Back to cart
            </a>

            <div class="mt-4 text-center md:mt-8">
                <p class="text-xs font-black uppercase tracking-wide text-primary">Secure Checkout</p>
                <h1 class="mt-2 text-[1.55rem] font-black leading-tight text-ink sm:text-4xl">আপনার অর্ডার কীভাবে করতে চান?</h1>
                <p class="mx-auto mt-3 hidden max-w-2xl text-sm leading-6 text-gray-600 sm:block sm:text-base">আপনার কার্টে রাখা পণ্যগুলো নিরাপদে সংরক্ষিত আছে। আপনার জন্য সহজ অপশনটি বেছে নিন।</p>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3 md:mt-5 md:grid-cols-[1.25fr_1fr_1fr] md:gap-4 md:items-stretch">
                <section class="col-span-2 flex flex-col rounded-lg border-2 border-primary bg-white p-4 shadow-[0_14px_35px_rgba(8,124,127,0.13)] sm:p-6 md:col-span-1">
                    <span class="grid h-10 w-10 place-items-center rounded-md bg-primary text-base text-white shadow-sm sm:h-12 sm:w-12 sm:text-lg"><i class="fa-solid fa-bag-shopping"></i></span>
                    <h2 class="mt-3 text-lg font-black text-ink sm:mt-4 sm:text-xl">অ্যাকাউন্ট ছাড়া অর্ডার করুন</h2>
                    <p class="mt-1 text-sm leading-5 text-gray-600 sm:mt-2 sm:leading-6">পাসওয়ার্ড বা অ্যাকাউন্ট ছাড়াই আপনার অর্ডারটি সম্পন্ন করুন।</p>
                    <a href="{{ route('guest.checkout.create') }}" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-md bg-primary px-4 text-sm font-black text-white shadow-[0_8px_18px_rgba(8,124,127,0.2)] transition hover:bg-ink sm:mt-5 sm:min-h-12 sm:text-base"><i class="fa-solid fa-arrow-right"></i>অর্ডার করুন</a>
                    <p class="mt-3 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-[11px] font-semibold leading-4 text-amber-800 sm:mt-4 sm:py-2.5 sm:text-xs sm:leading-5"><i class="fa-solid fa-circle-info mr-1"></i>এই অপশনে প্রয়োজন হলে ডেলিভারি চার্জ আগে পরিশোধ করতে হবে।</p>
                </section>

                <section class="flex flex-col rounded-lg border border-[#dfe7e5] bg-white p-3.5 shadow-sm md:p-5">
                    <span class="grid h-9 w-9 place-items-center rounded-md bg-blue-50 text-blue-700 md:h-11 md:w-11"><i class="fa-solid fa-right-to-bracket"></i></span>
                    <h2 class="mt-3 text-sm font-black text-ink md:mt-4 md:text-lg">সাইন ইন করুন</h2>
                    <p class="mt-2 hidden text-sm leading-6 text-gray-600 md:block">আগের অর্ডার ও সংরক্ষিত তথ্য দেখতে সাইন ইন করুন।</p>
                    <a href="{{ route('checkout.account', 'login') }}" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-md border border-primary bg-white px-2 text-xs font-black text-primary transition hover:bg-[#edf3f1] md:mt-auto md:min-h-11 md:gap-2 md:px-4 md:text-sm"><i class="fa-solid fa-right-to-bracket hidden md:inline-block"></i>সাইন ইন</a>
                </section>

                <section class="flex flex-col rounded-lg border border-[#dfe7e5] bg-white p-3.5 shadow-sm md:p-5">
                    <span class="grid h-9 w-9 place-items-center rounded-md bg-[#f0f5d7] text-[#617311] md:h-11 md:w-11"><i class="fa-solid fa-user-plus"></i></span>
                    <h2 class="mt-3 text-sm font-black text-ink md:mt-4 md:text-lg">অ্যাকাউন্ট খুলুন</h2>
                    <p class="mt-2 hidden text-sm leading-6 text-gray-600 md:block">পরের বার দ্রুত অর্ডার করুন ও নিজের অর্ডারগুলো দেখুন।</p>
                    <a href="{{ route('checkout.account', 'register') }}" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-md border border-[#aabc44] bg-[#f5f9df] px-2 text-xs font-black text-ink transition hover:bg-[#d5e77a] md:mt-auto md:min-h-11 md:gap-2 md:px-4 md:text-sm"><i class="fa-solid fa-user-plus hidden md:inline-block"></i>অ্যাকাউন্ট খুলুন</a>
                </section>
            </div>

            <div class="mt-4 flex items-center justify-between rounded-lg border border-[#dfe7e5] bg-white px-4 py-3 text-xs shadow-sm sm:mt-5 sm:px-5 sm:py-4 sm:text-sm">
                <span class="font-semibold text-gray-600">{{ count($cartItems) }} item{{ count($cartItems) === 1 ? '' : 's' }} in your cart</span>
                <span class="text-base font-black text-ink sm:text-lg">BDT {{ number_format($subtotal, 2) }}</span>
            </div>
        </div>
    </main>
</x-app-layout>

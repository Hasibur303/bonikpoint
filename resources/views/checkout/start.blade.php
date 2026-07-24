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
                <h1 class="mt-2 text-2xl font-black text-ink sm:text-4xl">আপনার অর্ডার কীভাবে করতে চান?</h1>
                <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-gray-600 sm:text-base">আপনার কার্টে রাখা পণ্যগুলো নিরাপদে সংরক্ষিত আছে। আপনার জন্য সহজ অপশনটি বেছে নিন।</p>
            </div>

            <div class="mx-auto mt-6 max-w-3xl rounded-lg border-2 border-primary bg-white px-4 py-4 text-left shadow-[0_14px_35px_rgba(8,124,127,0.14)] sm:mt-8 sm:px-6 sm:py-5">
                <div class="flex gap-3 sm:gap-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-primary text-lg text-white shadow-sm"><i class="fa-solid fa-bag-shopping"></i></span>
                    <div>
                        <span class="inline-flex rounded-full bg-[#f0f5d7] px-2.5 py-1 text-[10px] font-black tracking-wide text-[#526510]">অ্যাকাউন্ট লাগবে না</span>
                        <h2 class="mt-2 text-lg font-black leading-7 text-ink sm:text-xl">অ্যাকাউন্ট ছাড়া অর্ডার করতে নিচের <span class="text-primary">“অর্ডার করুন”</span> বাটনে ক্লিক করুন</h2>
                        <p class="mt-1.5 text-sm leading-6 text-[#405248]">অ্যাকাউন্ট না থাকলেও কোনো সমস্যা নেই। আপনার নাম, মোবাইল নম্বর ও ঠিকানা দিয়ে খুব সহজে অর্ডার করতে পারবেন।</p>
                    </div>
                </div>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-[1.25fr_1fr_1fr] md:items-stretch">
                <section class="flex flex-col rounded-lg border-2 border-primary bg-white p-5 shadow-[0_14px_35px_rgba(8,124,127,0.13)] sm:p-6">
                    <span class="grid h-12 w-12 place-items-center rounded-md bg-primary text-lg text-white shadow-sm"><i class="fa-solid fa-bag-shopping"></i></span>
                    <h2 class="mt-4 text-xl font-black text-ink">অ্যাকাউন্ট ছাড়া অর্ডার করুন</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">পাসওয়ার্ড বা অ্যাকাউন্ট ছাড়াই আপনার অর্ডারটি সম্পন্ন করুন।</p>
                    <p class="mt-4 rounded-md border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs font-semibold leading-5 text-amber-800"><i class="fa-solid fa-circle-info mr-1"></i>এই অপশনে প্রয়োজন হলে ডেলিভারি চার্জ আগে পরিশোধ করতে হবে।</p>
                    <a href="{{ route('guest.checkout.create') }}" class="mt-5 inline-flex min-h-12 items-center justify-center gap-2 rounded-md bg-primary px-4 text-base font-black text-white shadow-[0_8px_18px_rgba(8,124,127,0.2)] transition hover:bg-ink"><i class="fa-solid fa-arrow-right"></i>অর্ডার করুন</a>
                </section>

                <section class="flex flex-col rounded-lg border border-[#dfe7e5] bg-white p-5 shadow-sm">
                    <span class="grid h-11 w-11 place-items-center rounded-md bg-blue-50 text-blue-700"><i class="fa-solid fa-right-to-bracket"></i></span>
                    <h2 class="mt-4 text-lg font-black text-ink">সাইন ইন করুন</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">আগের অর্ডার ও সংরক্ষিত তথ্য দেখতে সাইন ইন করুন।</p>
                    <a href="{{ route('checkout.account', 'login') }}" class="mt-auto inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-primary bg-white px-4 text-sm font-black text-primary transition hover:bg-[#edf3f1]"><i class="fa-solid fa-right-to-bracket"></i>সাইন ইন করুন</a>
                </section>

                <section class="flex flex-col rounded-lg border border-[#dfe7e5] bg-white p-5 shadow-sm">
                    <span class="grid h-11 w-11 place-items-center rounded-md bg-[#f0f5d7] text-[#617311]"><i class="fa-solid fa-user-plus"></i></span>
                    <h2 class="mt-4 text-lg font-black text-ink">অ্যাকাউন্ট খুলুন</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">পরের বার দ্রুত অর্ডার করুন ও নিজের অর্ডারগুলো দেখুন।</p>
                    <a href="{{ route('checkout.account', 'register') }}" class="mt-auto inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-[#aabc44] bg-[#f5f9df] px-4 text-sm font-black text-ink transition hover:bg-[#d5e77a]"><i class="fa-solid fa-user-plus"></i>অ্যাকাউন্ট খুলুন</a>
                </section>
            </div>

            <div class="mt-5 flex items-center justify-between rounded-lg border border-[#dfe7e5] bg-white px-5 py-4 text-sm shadow-sm">
                <span class="font-semibold text-gray-600">{{ count($cartItems) }} item{{ count($cartItems) === 1 ? '' : 's' }} in your cart</span>
                <span class="text-lg font-black text-ink">BDT {{ number_format($subtotal, 2) }}</span>
            </div>
        </div>
    </main>
</x-app-layout>

@section('title', 'অর্ডার করার নিয়ম | Bonik Point')
@section('meta_description', 'Bonik Point থেকে অর্ডার, অগ্রিম ডেলিভারি চার্জ, মোবাইল পেমেন্ট, Transaction ID এবং পণ্য গ্রহণের নিয়ম জানুন।')
@section('canonical', route('order-instructions'))

<x-app-layout>
    <section class="bg-[#f4f7f6] py-8 md:py-12">
        <div class="container max-w-5xl">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100 md:p-8">
                <p class="text-xs font-bold uppercase tracking-wide text-primary">Customer Guide</p>
                <h1 class="mt-2 text-3xl font-black text-ink md:text-4xl">অর্ডার করার নিয়ম ও প্রয়োজনীয় নির্দেশনা</h1>
                <p class="mt-3 max-w-3xl leading-8 text-gray-600">
                    Bonik Point থেকে অর্ডার করার আগে অথবা অর্ডার করার সময় কোনো বিষয় বুঝতে সমস্যা হলে এই নির্দেশনাগুলো অনুসরণ করুন।
                    প্রয়োজনে আমাদের ২৪ ঘণ্টা কাস্টমার সার্ভিসে কল করতে পারেন।
                </p>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <a href="tel:01540381020" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-5 py-3 text-sm font-bold text-white hover:bg-ink">
                        <i class="fa-solid fa-phone"></i>
                        কল করুন: 01540381020
                    </a>
                    <a href="https://wa.me/8801540381020" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-md border border-primary/20 bg-white px-5 py-3 text-sm font-bold text-primary hover:border-primary hover:bg-primary hover:text-white">
                        <i class="fa-brands fa-whatsapp"></i>
                        WhatsApp করুন
                    </a>
                </div>
            </div>

            <div class="mt-6 grid gap-4">
                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <h2 class="text-xl font-black text-ink">১. অর্ডার করার নিয়ম</h2>
                    <ol class="mt-3 list-decimal space-y-2 pl-5 leading-8 text-gray-600">
                        <li>প্রথমে আপনার পছন্দের পণ্য নির্বাচন করুন।</li>
                        <li>পণ্যের ছবি, দাম, কালার/ভ্যারিয়েশন এবং বিস্তারিত তথ্য দেখে নিন।</li>
                        <li><span class="font-semibold text-ink">Add to Cart</span> অথবা <span class="font-semibold text-ink">Buy Now</span> বাটনে ক্লিক করুন।</li>
                        <li>Cart থেকে পণ্যের পরিমাণ ঠিক করে Checkout পেজে যান।</li>
                        <li>নাম, মোবাইল নম্বর, ইমেইল, জেলা/শহর এবং সম্পূর্ণ ঠিকানা সঠিকভাবে লিখুন।</li>
                        <li>যদি অগ্রিম ডেলিভারি চার্জ প্রয়োজন হয়, তাহলে নির্দেশনা অনুযায়ী ডেলিভারি চার্জ পাঠিয়ে Transaction ID দিন।</li>
                        <li>সব তথ্য যাচাই করে <span class="font-semibold text-ink">Place order</span> বাটনে ক্লিক করুন।</li>
                    </ol>
                </article>

                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <h2 class="text-xl font-black text-ink">২. ডেলিভারি চার্জ কেন আগে দিতে হয়</h2>
                    <p class="mt-3 leading-8 text-gray-600">
                        কিছু পণ্যের ক্ষেত্রে অর্ডার নিশ্চিত করার আগে অগ্রিম ডেলিভারি চার্জ নেওয়া হয়, কারণ কুরিয়ার বুকিং, প্যাকেজিং এবং ডেলিভারি প্রসেস শুরু করতে এই চার্জ প্রয়োজন হয়।
                        এতে ভুয়া অর্ডার কমে এবং আপনার অর্ডার দ্রুত প্রসেস করা যায়।
                    </p>
                    <div class="mt-4 rounded-md bg-[#edf7f6] p-4 text-sm font-semibold text-ink">
                        Dhaka: ৬০ টাকা, Dhaka এর বাইরে: ১২০ টাকা।
                    </div>
                </article>

                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <h2 class="text-xl font-black text-ink">৩. Bkash/Nagad/Rocket এ কিভাবে টাকা পাঠাবেন</h2>
                    <ol class="mt-3 list-decimal space-y-2 pl-5 leading-8 text-gray-600">
                        <li>আপনার Bkash, Nagad অথবা Rocket অ্যাপ খুলুন।</li>
                        <li><span class="font-semibold text-ink">Send Money</span> অপশন নির্বাচন করুন।</li>
                        <li>Checkout পেজে দেওয়া নম্বরটি নির্বাচন করুন।</li>
                        <li>আপনার এলাকার ডেলিভারি চার্জ অনুযায়ী টাকা পাঠান।</li>
                        <li>পেমেন্ট সফল হলে Transaction ID কপি করে Checkout পেজে লিখুন।</li>
                    </ol>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-md bg-gray-50 p-4 ring-1 ring-gray-100">
                            <p class="text-xs font-bold uppercase tracking-wide text-primary">Bkash</p>
                            <p class="mt-1 font-black text-ink">01832510343</p>
                        </div>
                        <div class="rounded-md bg-gray-50 p-4 ring-1 ring-gray-100">
                            <p class="text-xs font-bold uppercase tracking-wide text-primary">Nagad</p>
                            <p class="mt-1 font-black text-ink">01832510343</p>
                        </div>
                        <div class="rounded-md bg-gray-50 p-4 ring-1 ring-gray-100">
                            <p class="text-xs font-bold uppercase tracking-wide text-primary">Rocket</p>
                            <p class="mt-1 font-black text-ink">018325103435</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <h2 class="text-xl font-black text-ink">৪. Transaction ID কোথায় পাবেন</h2>
                    <p class="mt-3 leading-8 text-gray-600">
                        টাকা পাঠানোর পর Bkash/Nagad/Rocket অ্যাপে বা SMS-এ একটি Transaction ID পাওয়া যায়। এটি সাধারণত অক্ষর ও সংখ্যার মিশ্রণ হয়।
                        Checkout পেজে <span class="font-semibold text-ink">Transaction ID</span> বক্সে সেটি লিখুন। ভুল Transaction ID দিলে অর্ডার যাচাই করতে সময় লাগতে পারে।
                    </p>
                </article>

                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <h2 class="text-xl font-black text-ink">৫. Pay Later দিলে অর্ডার কবে Confirm হবে</h2>
                    <p class="mt-3 leading-8 text-gray-600">
                        Pay Later নির্বাচন করলে অর্ডার জমা হবে, কিন্তু ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত অর্ডার সম্পূর্ণভাবে Confirm হবে না।
                        ডেলিভারি চার্জ দেওয়ার পর আপনার My Orders পেজ থেকে পেমেন্ট তথ্য জমা দিন অথবা আমাদের কাস্টমার সার্ভিসে যোগাযোগ করুন।
                    </p>
                </article>

                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <h2 class="text-xl font-black text-ink">৬. পণ্য হাতে পাওয়ার সময় কীভাবে চেক করবেন</h2>
                    <ul class="mt-3 list-disc space-y-2 pl-5 leading-8 text-gray-600">
                        <li>ডেলিভারি ম্যানের সামনে প্যাকেট খুলে পণ্য দেখে নিন।</li>
                        <li>পণ্য ঠিক আছে কিনা, কালার/মডেল ঠিক আছে কিনা এবং কোনো ভাঙা/ক্ষতি আছে কিনা চেক করুন।</li>
                        <li>কোনো সমস্যা পেলে সঙ্গে সঙ্গে ছবি বা ভিডিও প্রমাণ রাখুন।</li>
                        <li>সমস্যা থাকলে ডেলিভারি ম্যান চলে যাওয়ার আগেই আমাদের কাস্টমার সার্ভিসে যোগাযোগ করুন।</li>
                    </ul>
                </article>

                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <h2 class="text-xl font-black text-ink">৭. Return Policy</h2>
                    <p class="mt-3 leading-8 text-gray-600">
                        পণ্য ভুল, ক্ষতিগ্রস্ত, অসম্পূর্ণ, অথবা অর্ডারের সাথে না মিললে ডেলিভারি ম্যানের সামনে প্রমাণসহ রিটার্ন করতে হবে।
                        ডেলিভারি ম্যান চলে যাওয়ার পর সাধারণত রিটার্ন গ্রহণযোগ্য নাও হতে পারে। বিস্তারিত জানতে Return & Refund Policy পেজ দেখুন।
                    </p>
                    <a href="{{ route('return-policy') }}" class="mt-4 inline-flex items-center gap-2 rounded-md border border-primary/20 px-4 py-2 text-sm font-bold text-primary hover:border-primary hover:bg-primary hover:text-white">
                        Return & Refund Policy
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </article>

                <article class="rounded-lg border border-primary/20 bg-primary/10 p-5">
                    <h2 class="text-xl font-black text-ink">৮. Customer Service Number</h2>
                    <p class="mt-3 leading-8 text-gray-700">
                        অর্ডার, পেমেন্ট, ডেলিভারি বা রিটার্ন সম্পর্কে কোনো প্রশ্ন থাকলে যোগাযোগ করুন:
                    </p>
                    <p class="mt-2 text-2xl font-black text-primary">01540381020</p>
                    <p class="mt-1 text-sm font-semibold text-gray-600">২৪ ঘণ্টা কাস্টমার সার্ভিস</p>
                </article>
            </div>
        </div>
    </section>
</x-app-layout>

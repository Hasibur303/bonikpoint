@section('title', 'Return and Refund Policy | Bonik Point')
@section('meta_description', 'Bonik Point পণ্য গ্রহণ, ডেলিভারি ম্যানের সামনে পরীক্ষা, প্রমাণ সংরক্ষণ এবং রিটার্ন ও রিফান্ডের নিয়ম জানুন।')
@section('canonical', route('return-policy'))

<x-app-layout>
    <section class="bg-white py-12">
        <div class="container max-w-4xl">
            <p class="text-sm font-bold uppercase tracking-wide text-primary">Bonik Point Policy</p>
            <h1 class="mt-2 text-4xl font-black text-ink">রিটার্ন ও রিফান্ড পলিসি</h1>
            <p class="mt-4 leading-8 text-gray-600">
                Bonik Point থেকে পণ্য গ্রহণের সময় কাস্টমারকে ডেলিভারি ম্যানের সামনে পণ্যটি ভালোভাবে খুলে দেখে নিতে হবে।
                পণ্য ভুল, ক্ষতিগ্রস্ত, অসম্পূর্ণ, বা অর্ডারের সাথে না মিললে তখনই প্রমাণসহ রিটার্ন করতে হবে।
            </p>

            <div class="mt-8 grid gap-5">
                <article class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                    <h2 class="text-xl font-black text-ink">রিটার্ন গ্রহণের নিয়ম</h2>
                    <p class="mt-3 leading-8 text-gray-600">
                        রিটার্ন শুধুমাত্র ডেলিভারির সময় গ্রহণযোগ্য। ডেলিভারি ম্যান চলে যাওয়ার পর এবং পণ্য গ্রহণ সম্পন্ন হলে
                        সাধারণত রিটার্ন গ্রহণ করা হবে না।
                    </p>
                </article>

                <article class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                    <h2 class="text-xl font-black text-ink">প্রমাণ সংরক্ষণ</h2>
                    <p class="mt-3 leading-8 text-gray-600">
                        পণ্যে কোনো সমস্যা পাওয়া গেলে কাস্টমারকে সঙ্গে সঙ্গে পরিষ্কার ছবি বা ভিডিও প্রমাণ নিতে হবে।
                        প্রমাণ ছাড়া রিটার্ন বা অভিযোগ যাচাই করা কঠিন হতে পারে।
                    </p>
                </article>

                <article class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                    <h2 class="text-xl font-black text-ink">যে ক্ষেত্রে রিটার্ন করা যাবে</h2>
                    <ul class="mt-3 list-disc space-y-2 pl-5 leading-8 text-gray-600">
                        <li>ভুল পণ্য ডেলিভারি হলে</li>
                        <li>পণ্য ভাঙা, নষ্ট, বা ক্ষতিগ্রস্ত হলে</li>
                        <li>পণ্যের প্রয়োজনীয় অংশ বা এক্সেসরিজ না থাকলে</li>
                        <li>পণ্য অর্ডারের বর্ণনার সাথে না মিললে</li>
                    </ul>
                </article>

                <article class="rounded-lg border border-accent/40 bg-accent/10 p-5">
                    <h2 class="text-xl font-black text-ink">সাপোর্ট</h2>
                    <p class="mt-3 leading-8 text-gray-700">
                        কোনো সমস্যা হলে দ্রুত আমাদের কাস্টমার সার্ভিসে যোগাযোগ করুন:
                        <span class="font-bold text-ink">01540381020</span>
                    </p>
                </article>
            </div>
        </div>
    </section>
</x-app-layout>

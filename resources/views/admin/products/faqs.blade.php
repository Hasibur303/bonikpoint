<x-admin-layout>
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-bold uppercase tracking-wide text-primary">Product FAQ</p>
            <h1 class="text-3xl font-black text-ink">{{ $product->name }}</h1>
            <p class="mt-2 text-sm text-gray-500">Manage only customer questions for this product.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('shop.show', $product) }}" target="_blank" rel="noopener noreferrer" class="rounded border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-ink hover:border-primary hover:text-primary">View Product</a>
            <a href="{{ route('admin.products.index') }}" class="rounded bg-ink px-4 py-2 text-sm font-semibold text-white hover:bg-primary">Back to Products</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.products.faqs.update', $product) }}" class="rounded-lg bg-white p-6 shadow-sm">
        @csrf
        @method('PATCH')

        <div class="rounded-lg border border-primary/10 bg-primary/5 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-black text-ink">Questions & Answers</h2>
                    <p class="mt-1 text-sm text-gray-600">Add common questions about delivery, product usage, warranty, return, compatibility, or size.</p>
                </div>
                <button type="button" id="add-faq-row" class="rounded bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-ink">Add FAQ</button>
            </div>
        </div>

        @if($product->faqs->isNotEmpty())
            <div class="mt-5 space-y-3">
                @foreach($product->faqs as $faq)
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)]">
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Question</label>
                                <input name="existing_faqs[{{ $faq->id }}][question]" value="{{ old('existing_faqs.'.$faq->id.'.question', $faq->question) }}" class="w-full rounded border-gray-200 bg-white">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Answer</label>
                                <textarea name="existing_faqs[{{ $faq->id }}][answer]" rows="2" class="w-full rounded border-gray-200 bg-white">{{ old('existing_faqs.'.$faq->id.'.answer', $faq->answer) }}</textarea>
                            </div>
                        </div>
                        <label class="mt-3 flex items-center gap-2 text-xs font-semibold text-red-600">
                            <input type="checkbox" name="delete_faqs[]" value="{{ $faq->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            Delete this FAQ
                        </label>
                    </div>
                @endforeach
            </div>
        @else
            <div class="mt-5 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-gray-500">
                No FAQ added yet. Add the first question below.
            </div>
        @endif

        @php($newFaqRows = old('faqs', [['question' => '', 'answer' => '']]))
        <div id="faq-rows" class="mt-5 space-y-3">
            @foreach($newFaqRows as $index => $faq)
                <div class="faq-row rounded-lg border border-dashed border-gray-300 bg-white p-4">
                    <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)]">
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-500">New Question</label>
                            <input name="faqs[{{ $index }}][question]" value="{{ $faq['question'] ?? '' }}" placeholder="Example: Is this product original?" class="w-full rounded border-gray-200">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-500">New Answer</label>
                            <textarea name="faqs[{{ $index }}][answer]" rows="2" placeholder="Write the answer customers should see." class="w-full rounded border-gray-200">{{ $faq['answer'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @error('existing_faqs.*.question')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('existing_faqs.*.answer')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('faqs.*.question')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('faqs.*.answer')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button class="rounded bg-primary px-6 py-3 font-semibold text-white hover:bg-ink">Save FAQ</button>
            <a href="{{ route('admin.products.edit', $product) }}" class="rounded border border-gray-200 px-6 py-3 font-semibold text-ink hover:border-primary hover:text-primary">Edit Product Details</a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addButton = document.getElementById('add-faq-row');
            const rows = document.getElementById('faq-rows');

            if (!addButton || !rows) {
                return;
            }

            addButton.addEventListener('click', function () {
                const index = rows.querySelectorAll('.faq-row').length;
                const wrapper = document.createElement('div');
                wrapper.className = 'faq-row rounded-lg border border-dashed border-gray-300 bg-white p-4';
                wrapper.innerHTML = `
                    <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)]">
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-500">New Question</label>
                            <input name="faqs[${index}][question]" placeholder="Example: Is this product original?" class="w-full rounded border-gray-200">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-500">New Answer</label>
                            <textarea name="faqs[${index}][answer]" rows="2" placeholder="Write the answer customers should see." class="w-full rounded border-gray-200"></textarea>
                        </div>
                    </div>
                `;
                rows.appendChild(wrapper);
            });
        });
    </script>
</x-admin-layout>

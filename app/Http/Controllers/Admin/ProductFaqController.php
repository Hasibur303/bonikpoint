<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFaq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductFaqController extends Controller
{
    public function edit(Product $product): View
    {
        return view('admin.products.faqs', [
            'product' => $product->load('faqs'),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'existing_faqs' => ['nullable', 'array'],
            'existing_faqs.*.question' => ['nullable', 'string', 'max:255'],
            'existing_faqs.*.answer' => ['nullable', 'string'],
            'faqs' => ['nullable', 'array'],
            'faqs.*.question' => ['nullable', 'string', 'max:255'],
            'faqs.*.answer' => ['nullable', 'string'],
            'delete_faqs' => ['nullable', 'array'],
            'delete_faqs.*' => ['integer', 'exists:product_faqs,id'],
        ]);

        $this->deleteSelectedFaqs($request, $product);
        $this->updateExistingFaqs($request, $product);
        $this->storeNewFaqs($request, $product);

        return redirect()->route('admin.products.faqs.edit', $product)->with('success', 'Product FAQ updated.');
    }

    private function storeNewFaqs(Request $request, Product $product): void
    {
        $faqs = collect($request->input('faqs', []))
            ->map(fn ($faq) => [
                'question' => trim((string) ($faq['question'] ?? '')),
                'answer' => trim((string) ($faq['answer'] ?? '')),
            ])
            ->filter(fn ($faq) => $faq['question'] !== '' && $faq['answer'] !== '')
            ->values();

        if ($faqs->isEmpty()) {
            return;
        }

        $nextSortOrder = ((int) $product->faqs()->max('sort_order')) + 1;

        $faqs->each(function (array $faq) use ($product, &$nextSortOrder): void {
            $product->faqs()->create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'sort_order' => $nextSortOrder++,
            ]);
        });
    }

    private function updateExistingFaqs(Request $request, Product $product): void
    {
        collect($request->input('existing_faqs', []))
            ->each(function (array $faq, int|string $id) use ($product): void {
                $question = trim((string) ($faq['question'] ?? ''));
                $answer = trim((string) ($faq['answer'] ?? ''));

                $productFaq = ProductFaq::where('product_id', $product->id)->whereKey($id)->first();

                if (! $productFaq) {
                    return;
                }

                if ($question === '' && $answer === '') {
                    $productFaq->delete();
                    return;
                }

                if ($question !== '' && $answer !== '') {
                    $productFaq->update([
                        'question' => $question,
                        'answer' => $answer,
                    ]);
                }
            });
    }

    private function deleteSelectedFaqs(Request $request, Product $product): void
    {
        $faqIds = collect($request->input('delete_faqs', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->all();

        if ($faqIds === []) {
            return;
        }

        ProductFaq::where('product_id', $product->id)
            ->whereIn('id', $faqIds)
            ->delete();
    }
}

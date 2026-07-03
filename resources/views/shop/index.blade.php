<x-app-layout>
    <section class="bg-white py-10">
        <div class="container">
            <h1 class="text-4xl font-black text-ink">Shop Products</h1>
            <form action="{{ route('shop.index') }}" class="mt-6 grid gap-3 md:grid-cols-[1fr_220px_auto]">
                <input name="search" value="{{ $search }}" placeholder="Search products" class="rounded-lg border-gray-200 focus:border-primary focus:ring-primary">
                <select name="category" class="rounded-lg border-gray-200 focus:border-primary focus:ring-primary">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>
                        @foreach($category->children as $child)
                            <option value="{{ $child->slug }}" @selected($selectedCategory === $child->slug)>-- {{ $child->name }}</option>
                        @endforeach
                    @endforeach
                </select>
                <button class="rounded-lg bg-primary px-6 py-2 font-semibold text-white hover:bg-ink">Filter</button>
            </form>
        </div>
    </section>

    <section class="py-12">
        <div class="container">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full rounded-lg bg-white p-10 text-center text-gray-500">No products found.</div>
                @endforelse
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        </div>
    </section>
</x-app-layout>

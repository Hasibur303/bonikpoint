@if(auth()->user()?->utype === 'adm')
    <div class="container py-2">
        <form method="POST" action="{{ route('admin.storefront.edit-mode') }}" class="flex items-center justify-end gap-3 text-xs font-semibold text-ink">
            @csrf
            <span>Storefront edit mode</span>
            <input type="hidden" name="enabled" value="{{ session('storefront_edit_mode') ? '0' : '1' }}">
            <button type="submit" role="switch" aria-checked="{{ session('storefront_edit_mode') ? 'true' : 'false' }}" aria-label="Storefront edit mode"
                class="inline-flex min-h-10 items-center gap-2 rounded-md border border-gray-300 bg-white px-3">
                <i class="fa-solid {{ session('storefront_edit_mode') ? 'fa-toggle-on text-primary' : 'fa-toggle-off text-gray-500' }} text-xl" aria-hidden="true"></i>
                {{ session('storefront_edit_mode') ? 'On' : 'Off' }}
            </button>
        </form>
    </div>
    @if(session('storefront_edit_mode'))
        <dialog id="storefront-product-editor" class="fixed inset-0 m-auto h-[95dvh] max-h-none w-full max-w-5xl flex-col overflow-hidden rounded-lg border border-gray-200 bg-white p-0 shadow-xl backdrop:bg-black/50 open:flex">
            <div class="flex shrink-0 items-center justify-between gap-3 border-b bg-white px-4 py-3">
                <h2 id="storefront-editor-title" class="text-base font-bold text-ink">Edit product</h2>
                <button type="button" data-storefront-editor-close class="grid h-10 w-10 place-items-center rounded-md text-ink hover:bg-gray-100" aria-label="Close product editor" title="Close"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            </div>
            <iframe title="Product editor" class="min-h-0 w-full flex-1 border-0"></iframe>
        </dialog>
    @endif
@endif

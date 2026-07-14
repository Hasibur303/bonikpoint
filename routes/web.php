<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminComtroller;
use App\Http\Middleware\AuthAdmin;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FestivalController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\FestivalController as AdminFestivalController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProfitController as AdminProfitController;
use App\Http\Controllers\Admin\ProductFaqController as AdminProductFaqController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Models\Category;
use App\Models\Festival;
use App\Models\Product;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ShopController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/category/{category:slug}', [ShopController::class, 'category'])->name('categories.show');
Route::get('/category/{parent:slug}/{category:slug}', [ShopController::class, 'subcategory'])
    ->withoutScopedBindings()
    ->name('categories.subcategory');
Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml')."\n", 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');
Route::get('/sitemap.xml', function () {
    $today = today()->toDateString();
    $urls = collect([
        ['loc' => route('home.index'), 'priority' => '1.0'],
        ['loc' => route('shop.index'), 'priority' => '0.9'],
        ['loc' => route('order-instructions'), 'priority' => '0.5'],
        ['loc' => route('return-policy'), 'priority' => '0.5'],
    ]);

    Category::where('is_active', true)
        ->orderBy('updated_at', 'desc')
        ->get()
        ->each(function (Category $category) use ($urls) {
            $urls->push([
                'loc' => $category->public_url,
                'lastmod' => optional($category->updated_at)->toAtomString(),
                'priority' => $category->parent_id ? '0.7' : '0.8',
            ]);
        });

    Product::where('is_active', true)
        ->orderBy('updated_at', 'desc')
        ->get()
        ->each(function (Product $product) use ($urls) {
            $urls->push([
                'loc' => route('shop.show', $product),
                'lastmod' => optional($product->updated_at)->toAtomString(),
                'priority' => '0.8',
            ]);
        });

    Festival::where('is_active', true)
        ->where(fn ($query) => $query->whereNull('starts_at')->orWhereDate('starts_at', '<=', $today))
        ->where(fn ($query) => $query->whereNull('ends_at')->orWhereDate('ends_at', '>=', $today))
        ->orderBy('updated_at', 'desc')
        ->get()
        ->each(function (Festival $festival) use ($urls) {
            $urls->push([
                'loc' => route('festivals.show', $festival),
                'lastmod' => optional($festival->updated_at)->toAtomString(),
                'priority' => '0.7',
            ]);
        });

    $xml = view('sitemap', ['urls' => $urls])->render();

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');
Route::view('/return-refund-policy', 'pages.return-policy')->name('return-policy');
Route::view('/order-instructions', 'pages.order-instructions')->name('order-instructions');
Route::get('/festivals/{festival:slug}', [FestivalController::class, 'show'])->name('festivals.show');
Route::get('/products/{product}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/snapshot', [CartController::class, 'snapshotResponse'])->name('cart.snapshot');
Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/guest-checkout', [CheckoutController::class, 'guestCreate'])->name('guest.checkout.create');
Route::post('/guest-checkout', [CheckoutController::class, 'guestStore'])->name('guest.checkout.store');
Route::get('/guest-orders/{order:order_number}/{token}', [OrderController::class, 'guestShow'])->name('guest.orders.show');
Route::get('/guest-orders/{order:order_number}/{token}/receipt', [OrderController::class, 'guestReceipt'])->name('guest.orders.receipt');

Route::get('/dashboard', function () {
    if (auth()->user()?->utype === 'adm') {
        return redirect()->route('admin.index');
    }

    return redirect()->route('shop.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/delivery-payment', [OrderController::class, 'deliveryPayment'])->name('orders.delivery-payment');
    Route::patch('/orders/{order}/delivery-payment', [OrderController::class, 'updateDeliveryPayment'])->name('orders.delivery-payment.update');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::post('/orders/{order}/reviews', [ProductReviewController::class, 'store'])->name('orders.reviews.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->middleware([AuthAdmin::class])->group(function () {
    Route::get('/', [AdminComtroller::class, 'index'])->name('index');
    Route::resource('categories', AdminCategoryController::class);
    Route::get('products/{product}/faqs', [AdminProductFaqController::class, 'edit'])->name('products.faqs.edit');
    Route::patch('products/{product}/faqs', [AdminProductFaqController::class, 'update'])->name('products.faqs.update');
    Route::resource('products', AdminProductController::class)->except('show');
    Route::resource('festivals', AdminFestivalController::class)->except('show');
    Route::get('profit', [AdminProfitController::class, 'index'])->name('profit.index');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::patch('settings', [AdminSettingController::class, 'update'])->name('settings.update');
});


require __DIR__.'/auth.php';

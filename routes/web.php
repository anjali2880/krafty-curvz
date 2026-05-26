<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomizationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Seo\SitemapController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SiteSettingsController;

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/category/{categorySlug}', [ProductController::class, 'index'])->name('category.show');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place-order');
    Route::get('/order/confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('order.confirmation');
    Route::get('/my-orders', [CheckoutController::class, 'myOrders'])->name('orders.my');
    Route::get('/my-orders/{order}', [CheckoutController::class, 'showMyOrder'])->name('orders.show');
    Route::post('/my-orders/{order}/parcel-details', [CheckoutController::class, 'uploadParcelDetails'])->name('orders.parcel-details');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

Route::post('/customization/upload-image', [CustomizationController::class, 'uploadImage'])->name('customization.upload-image');
Route::post('/customization/save-design', [CustomizationController::class, 'saveDesign'])->name('customization.save-design');

// Admin Auth Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout.get');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('products', AdminProductController::class);
        Route::post('products/{product}/publish-toggle', [AdminProductController::class, 'togglePublish'])->name('products.toggle-publish');
        Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.images.delete');
        Route::post('products/{product}/images/{image}/primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.images.primary');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');

        Route::get('settings', [SiteSettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SiteSettingsController::class, 'update'])->name('settings.update');
    });
});

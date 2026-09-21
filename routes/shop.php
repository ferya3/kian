<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| فروشگاه
|--------------------------------------------------------------------------
|
| این پرونده فقط وقتی بارگذاری می‌شود که کلید فروشگاه روشن باشد — شرطش در
| routes/public.php است. پس با کلیدِ خاموش، این مسیرها اصلاً وجود ندارند و
| /shop یک ۴۰۴ عادی است، نه صفحه‌ای که بگوید «به‌زودی».
|
| همه زیر پیشوند زبان‌اند، چون در گروه زبان include می‌شوند.
*/

Route::get('shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('shop/{product:slug}', [ShopController::class, 'show'])->name('shop.show');

Route::get('cart', [CartController::class, 'show'])->name('cart.show');
Route::post('cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('cart/{offer}', [CartController::class, 'update'])->name('cart.update');
Route::delete('cart/{offer}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('checkout', [CheckoutController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('checkout.store');

/* پیگیری سفارش با نشانیِ غیرقابل‌حدس، بدون حساب کاربری */
Route::get('orders/{order:token}', [CheckoutController::class, 'done'])->name('orders.show');

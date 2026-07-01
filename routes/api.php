<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ShippingMethodController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API mobile (React Native) — authentification par token (Sanctum)
|--------------------------------------------------------------------------
*/

// Public — auth routes with rate limiting (20 attempts per minute)
Route::middleware('throttle:20,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/auth/social', [SocialAuthController::class, 'social']);
});

// Public — listing routes with moderate rate limiting (60 requests per minute)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/filters', [ProductController::class, 'filters']);
    Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/search/suggestions', [SearchController::class, 'suggestions']);
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
    Route::get('/shipping-methods', [ShippingMethodController::class, 'index']);
});

// Protégé (Bearer token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateProfile']);
    Route::put('/me/password', [AuthController::class, 'changePassword']);
    Route::post('/me/avatar', [AuthController::class, 'updateAvatar']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Avis clients
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);

    // Favoris (wishlist)
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::get('/wishlist/{productId}', [WishlistController::class, 'check']);
    Route::post('/wishlist/{productId}', [WishlistController::class, 'toggle']);
    Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{productId}', [CartController::class, 'update']);
    Route::delete('/cart/{productId}', [CartController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Bons / Portefeuille
    Route::get('/coupons', [CouponController::class, 'index']);
    Route::post('/coupons/apply', [CouponController::class, 'apply']);
    Route::get('/wallet', [WalletController::class, 'index']);
    Route::post('/wallet/topup', [WalletController::class, 'topup']);
    Route::post('/wallet/transfer', [WalletController::class, 'transfer']);

    // Espace admin (modération paiements / rechargements)
    Route::prefix('admin')->group(function () {
        Route::get('/summary', [AdminController::class, 'summary']);
        Route::get('/payments', [AdminController::class, 'payments']);
        Route::post('/payments/{id}/confirm', [AdminController::class, 'confirmPayment']);
        Route::post('/payments/{id}/reject', [AdminController::class, 'rejectPayment']);
        Route::get('/topups', [AdminController::class, 'topups']);
        Route::post('/topups/{id}/confirm', [AdminController::class, 'confirmTopup']);
        Route::post('/topups/{id}/reject', [AdminController::class, 'rejectTopup']);
    });

    // Adresses de livraison
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    Route::post('/addresses/{id}/default', [AddressController::class, 'setDefault']);
});

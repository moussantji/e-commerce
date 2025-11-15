<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Controllers\CustomerPanel\CustomerController;
use App\Http\Controllers\AdminPanel\ProductController;
use App\Http\Controllers\AdminPanel\CategoryController;
use App\Http\Controllers\AdminPanel\OrderController;
use App\Http\Controllers\AdminPanel\UserController;

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Routes authentifiées utilisateur
Route::middleware(['auth', 'verified'])->group(function () {
    // Tableau de bord client
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes d'administration
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Tableau de bord admin
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Récupération des données de vente (AJAX)
    Route::get('/sales-data/{month}', [\App\Http\Controllers\Admin\DashboardController::class, 'getSalesData'])
        ->where('month', '[0-9]{4}-[0-9]{2}')
        ->name('sales.data');
    
    // Gestion des produits
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/update-status', [ProductController::class, 'updateStatus'])->name('products.update-status');
    
    // Gestion des catégories
    Route::resource('categories', CategoryController::class);
    
    // Gestion des commandes
    Route::resource('orders', OrderController::class);
    
    // Gestion des utilisateurs
    Route::resource('users', UserController::class);
    
    // Gestion des tags
    Route::resource('tags', \App\Http\Controllers\AdminPanel\TagController::class);
    
    // Pages d'informations
    Route::get('/privacy-policy', function () {
        return view('admin.pages.privacy-policy');
    })->name('privacy.policy');
    
    Route::get('/terms', function () {
        return view('admin.pages.terms');
    })->name('terms');
    
    Route::get('/contact', function () {
        return view('admin.pages.contact');
    })->name('contact');
    
    // Gestion des coupons
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class)
         ->except(['show'])
         ->names('coupons');
         
    // Gestion des méthodes de paiement
    Route::resource('payment-methods', \App\Http\Controllers\Admin\PaymentMethodController::class)
         ->except(['show'])
         ->names('payment-methods')
         ->parameters(['payment-methods' => 'paymentMethod']);
    
    // Gestion des méthodes de livraison
    Route::resource('shipping-methods', \App\Http\Controllers\Admin\ShippingMethodController::class)
         ->except(['show'])
         ->names('shipping-methods')
         ->parameters(['shipping-methods' => 'shippingMethod']);
    
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    
    // Gestion du profil administrateur
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AdminController::class, 'updatePassword'])->name('profile.password');
    
    // Paramètres du site
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

// Fichier d'authentification
require __DIR__.'/auth.php';


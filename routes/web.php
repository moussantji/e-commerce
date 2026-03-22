<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CaracteristiqueController;
use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Controllers\AdminPanel\CategoryController;
use App\Http\Controllers\AdminPanel\OrderController;
use App\Http\Controllers\AdminPanel\ProductController;
use App\Http\Controllers\AdminPanel\UserController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\BrandController as ClientBrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CheckoutShow;
use App\Http\Controllers\ClientCategoryController;
use App\Http\Controllers\CustomerPanel\CustomerComtroller;
use App\Http\Controllers\CustomerPanel\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhotoControler;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

$idRegex = '[0-9]+';
$slugRegex = '[0-9a-z\-]+';

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/panier', [CartController::class, 'index'])->name('panier');
Route::get('/produits', [HomeController::class, 'allProduits'])->name('products');
Route::get('/favoris', [HomeController::class, 'favoris'])->name('favoris');
Route::get('/brands', [ClientBrandController::class, 'index'])->name('client.brands.index');
Route::get('produits/{slug}-{id}', [HomeController::class, 'produits'])->name('produits.show')->where([
    'slug' => $slugRegex,
    'id' => $idRegex
]);

Route::post('/paiement/callback', function (Request $request) {
    $transaction_id = $request->transaction_id;
    $commande = \App\Models\Commandes::where('cinetpay_transaction_id', $transaction_id)
        ->where('statut', 'en_attente')
        ->first();

    if ($commande) {
        $commande->update([
            'statut' => 'payee',
            'date_en_attente' => null
        ]);
    }
    return response('OK', 200);
})->name('paiement.callback');

// Fedapay initiation and webhook
Route::get('/paiement/fedapay', [\App\Http\Controllers\PaymentController::class, 'initiateFedapay'])->name('paiement.fedapay');
Route::post('/paiement/fedapay/callback', [\App\Http\Controllers\PaymentController::class, 'notifyFedapay'])->name('paiement.fedapay.callback');

Route::get('/paiement/success', function () {
    return redirect('/')->with('success', '✅ Paiement réussi !');
})->name('paiement.success');

Route::prefix('categories')->name('categories.')->group(function () use ($slugRegex) {
    // Liste toutes categories
    Route::get('/', [ClientCategoryController::class, 'index'])->name('index');

    // Catégorie + ses sous-categories
    Route::get('{category}', [ClientCategoryController::class, 'show'])->where([
        'category' => $slugRegex,
    ])->name('show');
});


Route::get('commande/{id}', [HomeController::class, 'commande'])
    ->where(['id' => $idRegex])
    ->name('commande.show');

Route::delete('commande/{id}', [HomeController::class, 'destroy'])
    ->where(['id' => $idRegex])
    ->name('commande.destroy');

Route::get('commande/{id}/pdf', [HomeController::class, 'exportPdf'])
->where(['id' => $idRegex])
->name('commande.pdf');


Route::get('avis/{avis}', [AvisController::class, 'show'])->name('avis.show');
Route::get('avis/{avis}/export', [AvisController::class, 'export'])->name('avis.export');
Route::delete('avis/{avis}', [AvisController::class, 'destroy'])->name('avis.destroy');




// Routes authentifiées utilisateur
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Tableau de bord client
    Route::get('/dashboard', [CustomerComtroller::class, 'dashboard'])->name('dashboard');
    Route::delete('wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('cart/{id}', [CartController::class, 'addCart'])->name('cart.add');


    Route::put('/profile/password', [CustomerComtroller::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile', [CustomerComtroller::class, 'updateProfile'])->name('profile.update');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Paiement mobile : envoi preuve (Orange / Malitel / Wave)
    Route::post('/paiement/mobile', [PaymentController::class, 'storeManual'])->name('paiement.mobile');
});

// Routes d'administration
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Tableau de bord admin
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/banners', BannerController::class)->except('show');
    // Récupération des données de vente (AJAX)
    Route::get('/sales-data/{month}', [\App\Http\Controllers\Admin\DashboardController::class, 'getSalesData'])
        ->where('month', '[0-9]{4}-[0-9]{2}')
        ->name('sales.data');

    // Gestion des produits
    Route::resource('products', ProductController::class);
    Route::resource('caracteristiques', CaracteristiqueController::class);

    Route::patch('products/{product}/update-status', [ProductController::class, 'updateStatus'])->name('products.update-status');

    // Gestion des catégories
    Route::resource('categories', CategoryController::class);

    // Upload de médias
    Route::post('/media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])
        ->name('media.upload');

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

    Route::resource('brands', BrandController::class);

    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Confirmation manuelle du paiement par l'admin (preuve mobile money)
    Route::patch('orders/{order}/confirm-payment', [\App\Http\Controllers\PaymentController::class, 'confirmPayment'])->name('orders.confirm-payment');

    Route::delete('photo/{photo}', [PhotoControler::class, 'destroyPhoto'])->name('photo.destroy');

    // Gestion du profil administrateur
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AdminController::class, 'updatePassword'])->name('profile.password');

    // Paramètres du site
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

Route::middleware('auth')->group(function () {
    // Page liste complète
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.delete');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

Route::get('/images/{path}', [ImageController::class, 'show'])->where('path', '.*')->name('glide.image');



// Fichier d'authentification
require __DIR__ . '/auth.php';

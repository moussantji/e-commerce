<div class="shop-topbar">
<div class="shop-wrap">
<div class="top-left"><svg class="ic ic-sm"><use href="#i-truck"/></svg><b>Livraison offerte dès 25 000 FCFA</b></div>
<span class="sep hide-m"></span><span class="hide-m">Paiement à la livraison</span>
<span class="sep hide-m"></span><span class="hide-m">Orange Money</span>
<span class="sep hide-m"></span><span class="hide-m">Moov Money</span>
<span class="sep hide-m"></span><span class="hide-m">Wave</span>
<div class="top-push"><a href="{{ route('home') }}#aide">Aide</a><a href="tel:+22382019583">+223 82 01 95 83</a></div>
</div>
</div>
<header class="shop-header">
<div class="shop-wrap header-row">
<a class="brand" href="{{ route('home') }}" aria-label="Accueil"><span class="mark"><svg class="ic"><use href="#i-store"/></svg></span><span class="brand-name">Boutique</span></a>
<form class="shop-search desktop" action="{{ route('products') }}" method="GET" role="search" onsubmit="return true">
<svg class="ic"><use href="#i-search"/></svg>
<input id="shopSearchInput" type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit..." aria-label="Rechercher un produit" autocomplete="off" readonly>
</form>
<div class="shop-actions">
<a href="{{ auth()->check() ? route('favoris') : route('login') }}" aria-label="Favoris"><svg class="ic"><use href="#i-heart"/></svg></a>
<a href="{{ route('panier') }}" aria-label="Panier"><svg class="ic"><use href="#i-bag"/></svg>@livewire('navbar-cart-count')</a>
@auth
<a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" data-account aria-label="Mon compte"><svg class="ic"><use href="#i-user"/></svg></a>
@else
<a href="{{ route('login') }}" data-account aria-label="Se connecter"><svg class="ic"><use href="#i-user"/></svg></a>
@endauth
</div>
</div>
<div class="shop-search-mobile">
<form class="shop-search" action="{{ route('products') }}" method="GET" role="search" style="flex:1">
<svg class="ic"><use href="#i-search"/></svg>
<input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher..." aria-label="Rechercher">
</form>
</div>
</header>
<nav class="shop-nav" aria-label="Catégories">
<div class="shop-wrap nav-row">
@if(!empty($categories) && count($categories))
@foreach($categories as $cat)
<a href="{{ route('categories.show', $cat->slug ?? $cat->id) }}">{{ $cat->name ?? $cat->nom }}</a>
@endforeach
@else
<a href="{{ route('products', ['category' => 'electronique']) }}">Électronique</a>
<a href="{{ route('products', ['category' => 'mode']) }}">Mode</a>
<a href="{{ route('categories.index') }}">Maison &amp; Jardin</a>
<a href="{{ route('products') }}">Beauté</a>
<a href="{{ route('products') }}">Sport</a>
<a href="{{ route('products') }}">Jouets</a>
@endif
<a class="hot" href="{{ route('products') }}#promos">Promos</a>
</div>
</nav>

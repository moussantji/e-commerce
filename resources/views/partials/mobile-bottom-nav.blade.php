@php
$dockCartCount = auth()->check()
? (int) \Illuminate\Support\Facades\DB::table('panier_produit')->join('paniers','panier_produit.paniers_id','=','paniers.id')->where('paniers.user_id',auth()->id())->sum('panier_produit.quantite')
: (int) session('cart_count',0);
if(!auth()->check()){$dockProfileUrl=route('login');$dockProfileLabel='Connexion';}
elseif(auth()->user()->isAdmin()){$dockProfileUrl=route('admin.dashboard');$dockProfileLabel='Compte';}
else{$dockProfileUrl=route('dashboard');$dockProfileLabel='Compte';}
@endphp
<nav class="dock" aria-label="Navigation mobile">
<a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><svg class="ic"><use href="#i-home"/></svg><span>Accueil</span></a>
<a href="{{ route('products') }}" class="{{ request()->routeIs('products') ? 'active' : '' }}"><svg class="ic"><use href="#i-grid"/></svg><span>Catalogue</span></a>
<a href="{{ route('panier') }}" class="{{ request()->routeIs('panier') ? 'active' : '' }}"><svg class="ic"><use href="#i-bag"/></svg><span>Panier</span>@if($dockCartCount>0)<span class="dock-badge">{{ $dockCartCount }}</span>@endif</a>
<a href="{{ auth()->check() ? route('favoris') : route('login') }}" class="{{ request()->routeIs('favoris') ? 'active' : '' }}"><svg class="ic"><use href="#i-heart"/></svg><span>Favoris</span></a>
<a href="{{ $dockProfileUrl }}" data-account><svg class="ic"><use href="#i-user"/></svg><span>{{ $dockProfileLabel }}</span></a>
</nav>

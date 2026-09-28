<div class="grid">
@forelse($topDeals as $produit)
@php
$hasPromo = $produit->sale_price && $produit->sale_price < $produit->price;
$discount = $hasPromo && $produit->price > 0 ? round((($produit->price - $produit->sale_price) / $produit->price) * 100) : 0;
$fallbacks = [1,2,3,4,5,6,7,8,10,12,16,17,18,19,20,21,23,24,25,26,27];
$img = $produit->getPhoto() ? $produit->getPhoto()->getImageUrl(530,530) : asset('assets/img/products/' . $fallbacks[$produit->id % count($fallbacks)] . '.png');
$inWishlist = auth()->check() && auth()->user()->wishlistProducts->contains($produit->id);
$pUrl = route('produits.show', ['slug' => $produit->getSlug(), 'id' => $produit->id]);
@endphp
<article class="card rv">
<div class="thumb" style="background-image:url('{{ $img }}')">
@if($hasPromo)<span class="off">-{{ $discount }}%</span>@endif
@if(auth()->check())
<button wire:click="toggleWishlist({{ $produit->id }})" class="fav {{ $inWishlist ? 'on' : '' }}" title="Favori" aria-label="{{ $inWishlist ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"><svg class="ic"><use href="#i-heart"/></svg><span class="tip">{{ $inWishlist ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</span></button>
@else
<a class="fav" href="{{ route('login') }}" title="Favori" aria-label="Ajouter aux favoris"><svg class="ic"><use href="#i-heart"/></svg><span class="tip">Connectez-vous pour liker</span></a>
@endif
</div>
<div class="body">
<div class="name">{{ Str::limit($produit->name, 32) }}</div>
@if($hasPromo)<div class="was">{{ $this->formatFcfa($produit->price) }}</div>@endif
<div class="price">@if($hasPromo){{ $this->formatFcfa($produit->sale_price) }}@else{{ $this->formatFcfa($produit->price) }}@endif</div>
<a href="{{ $pUrl }}" class="add">Voir le produit</a>
</div>
</article>
@empty
<p class="empty">Aucun produit pour le moment</p>
@endforelse
</div>

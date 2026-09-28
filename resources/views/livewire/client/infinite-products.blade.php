<div>
<div class="grid">
@forelse($products as $product)
@php
$hasPromo = $product->sale_price && $product->sale_price < $product->price;
$discount = $hasPromo && $product->price > 0 ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
$img = $product->getPhoto() ? $product->getPhoto()->getImageUrl(530,530) : asset('assets/img/products/1.png');
$inWishlist = in_array($product->id, $wishlistItems ?? []);
$pUrl = route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]);
@endphp
<article class="card rv in">
<div class="thumb" style="background-image:url('{{ $img }}')">
@if($hasPromo)<span class="off">-{{ $discount }}%</span>@endif
@if(auth()->check())
<button wire:click="toggleWishlist({{ $product->id }})" class="fav {{ $inWishlist ? 'on' : '' }}" title="Favori" aria-label="{{ $inWishlist ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"><svg class="ic"><use href="#i-heart"/></svg><span class="tip">{{ $inWishlist ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</span></button>
@else
<a class="fav" href="{{ route('login') }}" title="Favori" aria-label="Ajouter aux favoris"><svg class="ic"><use href="#i-heart"/></svg><span class="tip">Connectez-vous pour liker</span></a>
@endif
</div>
<div class="body">
<div class="name">{{ Str::limit($product->name, 30) }}</div>
@if($hasPromo)<div class="was">{{ $this->formatFcfa($product->price) }}</div>@endif
<div class="price">{{ $this->formatFcfa($hasPromo ? $product->sale_price : $product->price) }}</div>
<a href="{{ $pUrl }}" class="add">Voir le produit</a>
</div>
</article>
@empty
<p class="empty">Aucun produit pour le moment.</p>
@endforelse
</div>
@if($hasMore ?? false)
<div id="infinite-sentinel" style="text-align:center;padding:20px 0" x-data x-intersect.margin.400px="$wire.loadMore()">
<div wire:loading wire:target="loadMore" style="color:#6b7280;font-size:13px">Chargement…</div>
</div>
@endif
</div>

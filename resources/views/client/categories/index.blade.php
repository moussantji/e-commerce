@extends('base')

@section('title', isset($category) ? $category->name . ' — Boutique' : 'Catégories — Boutique')

@section('content')
<div class="page-loader" id="pageLoader"><div class="loader-circle"></div></div>
@include('section-begin')
<main>
<div class="shop-wrap" style="padding-top:18px;font-size:12.5px;color:#6b7280">
<a href="{{ route('home') }}" style="color:#6d28d9;font-weight:600">Accueil</a>
<span style="margin:0 6px">/</span>
@if(isset($category))
<a href="{{ route('categories.index') }}" style="color:#6d28d9;font-weight:600">Catégories</a>
<span style="margin:0 6px">/</span>
<span>{{ $category->name }}</span>
@else
<span>Toutes les catégories</span>
@endif
</div>

@if(!isset($category))
<section class="sec">
<div class="shop-wrap">
<div class="sec-head rv"><h2>Toutes les catégories</h2></div>
<div class="grid">
@foreach($categories as $cat)
@php $n = \App\Models\Produits::where('is_active', true)->whereIn('category_id', \App\Models\Categories::where('parent_id', $cat->id)->pluck('id')->push($cat->id))->count(); @endphp
<a href="{{ route('categories.show', $cat->slug) }}" class="card rv" style="padding:22px;gap:6px">
<div class="name" style="font-size:15px;font-weight:700;color:#111827">{{ $cat->name }}</div>
<div class="was">{{ $n }} produit{{ $n > 1 ? 's' : '' }}</div>
<span class="add">Voir <svg class="ic ic-sm" style="display:inline;vertical-align:-2px"><use href="#i-chevron"/></svg></span>
</a>
@endforeach
</div>
</div>
</section>
@else
<section class="sec">
<div class="shop-wrap">
<div class="sec-head rv"><h2>{{ $category->name }}</h2><a class="more" href="{{ route('products') }}">Tout voir <svg class="ic ic-sm"><use href="#i-chevron"/></svg></a></div>
@if($subcategories->count())
<div class="cats" style="padding-top:0">
@foreach($subcategories as $sub)
<a class="cat rv" href="{{ route('products', ['category' => $sub->slug]) }}">{{ $sub->name }}</a>
@endforeach
</div>
@endif
<div class="grid" style="margin-top:16px">
@forelse($products as $item)
@php
$promo = $item->sale_price && $item->sale_price < $item->price;
$discount = $promo && $item->price > 0 ? round((($item->price - $item->sale_price) / $item->price) * 100) : 0;
$fallbacks = [1,2,3,4,5,6,7,8,10,12,16,17,18,19,20,21,23,24,25,26,27];
$photo = $item->getPhoto() ? $item->getPhoto()->getImageUrl(530,530) : asset('assets/img/products/' . $fallbacks[$item->id % count($fallbacks)] . '.png');
@endphp
<article class="card rv">
<div class="thumb" style="background-image:url('{{ $photo }}')">
@if($promo)<span class="off">-{{ $discount }}%</span>@endif
<a class="fav" href="{{ auth()->check() ? route('favoris') : route('login') }}" aria-label="Favori"><svg class="ic"><use href="#i-heart"/></svg></a>
</div>
<div class="body">
<div class="name">{{ Str::limit($item->name, 32) }}</div>
@if($promo)<div class="was">{{ number_format($item->price, 0, ',', ' ') }} FCFA</div>@endif
<div class="price">{{ number_format($promo ? $item->sale_price : $item->price, 0, ',', ' ') }} FCFA</div>
<a href="{{ route('produits.show', ['slug' => $item->getSlug(), 'id' => $item->id]) }}" class="add">Voir le produit</a>
</div>
</article>
@empty
<p class="empty">Aucun produit dans cette catégorie pour le moment.</p>
@endforelse
</div>
</div>
</section>
@endif
</main>
@include('partials.footer')
@endsection

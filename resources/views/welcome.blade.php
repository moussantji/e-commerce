@extends('base')
@section('title', 'Boutique en ligne — Accueil')
@section('content')
<div class="page-loader" id="pageLoader"><div class="loader-circle"></div></div>
@include('section-begin')
<main>
<section class="hero">
<div class="shop-wrap hero-grid">
<div class="banner">
<span class="pill rise" style="animation-delay:.05s">#MégaSoldes</span>
<h1 class="rise" style="animation-delay:.12s">ÉCONOMISEZ<br>GROS</h1>
<p class="sub rise" style="animation-delay:.2s">Jusqu'à -50% sur la sélection</p>
<a class="btn-buy rise" style="animation-delay:.28s" href="{{ route('products') }}">ACHETEZ MAINTENANT <svg class="ic ic-sm"><use href="#i-chevron"/></svg></a>
</div>
<div class="promos">
<a href="#promos" class="promo light rise" style="animation-delay:.16s"><h2>SOLDES</h2><p>Jusqu'à -50% sur la sélection</p></a>
@php
$spot = $promoSpotlight ?? null;
$spotOff = $spot && $spot->sale_price && $spot->sale_price < $spot->price ? round((($spot->price - $spot->sale_price) / $spot->price) * 100) : 0;
$spotFb = [1,2,3,4,5,6,7,8,10,12,16,17,18,19,20,21,23,24,25,26,27];
$spotImg = $spot ? ($spot->getPhoto() ? $spot->getPhoto()->getImageUrl(530,530) : asset('assets/img/products/' . $spotFb[$spot->id % count($spotFb)] . '.png')) : null;
@endphp
@if($spot)
<a href="{{ route('produits.show', ['slug' => $spot->getSlug(), 'id' => $spot->id]) }}" class="promo spot rise" style="animation-delay:.22s">
<span class="tile" style="background-image:url('{{ $spotImg }}');background-size:cover;background-position:center"></span>
@if($spotOff)<span class="spot-tag">-{{ $spotOff }}%</span>@endif
<span class="spot-info"><span class="spot-nm">{{ Str::limit($spot->name, 26) }}</span><span class="spot-pr">{{ number_format($spot->sale_price && $spot->sale_price < $spot->price ? $spot->sale_price : $spot->price, 0, ',', ' ') }} FCFA</span></span>
</a>
@else
<a href="{{ route('products') }}" class="promo img rise" style="animation-delay:.22s"><span class="tile" style="background-image:url('{{ asset('img/shop/tile-1.jpg') }}');background-size:cover;background-position:center"></span></a>
@endif
<a href="{{ route('products') }}" class="promo solid rise" style="animation-delay:.28s"><h2>NOUVEAUTÉS</h2><p>Les derniers arrivages</p></a>
@php
$nw = $newSpotlight ?? null;
$nwImg = $nw ? ($nw->getPhoto() ? $nw->getPhoto()->getImageUrl(530,530) : asset('assets/img/products/' . $spotFb[$nw->id % count($spotFb)] . '.png')) : null;
@endphp
@if($nw)
<a href="{{ route('produits.show', ['slug' => $nw->getSlug(), 'id' => $nw->id]) }}" class="promo spot rise" style="animation-delay:.34s">
<span class="tile" style="background-image:url('{{ $nwImg }}');background-size:cover;background-position:center"></span>
<span class="spot-tag new">NEW</span>
<span class="spot-info"><span class="spot-nm">{{ Str::limit($nw->name, 26) }}</span><span class="spot-pr">{{ number_format($nw->sale_price && $nw->sale_price < $nw->price ? $nw->sale_price : $nw->price, 0, ',', ' ') }} FCFA</span></span>
</a>
@else
<a href="{{ route('products') }}" class="promo img rise" style="animation-delay:.34s"><span class="tile" style="background-image:url('{{ asset('img/shop/tile-washer.jpg') }}');background-size:cover;background-position:center"></span></a>
@endif
</div>
</div>
</section>
<div class="trust">
<div class="shop-wrap trust-row">
<div class="item rv"><svg class="ic"><use href="#i-truck"/></svg><div><b>Livraison offerte</b><span>Dès 25 000 FCFA</span></div></div>
<div class="item rv"><svg class="ic"><use href="#i-bolt"/></svg><div><b>Vente Flash</b><span>Voir plus</span></div></div>
<div class="item rv"><svg class="ic"><use href="#i-card"/></svg><div><b>Paiement Mobile Money</b><span>Orange · Moov · Wave</span></div></div>
<div class="item rv"><svg class="ic"><use href="#i-headset"/></svg><div><b>Support 7j/7</b><span>+223 82 01 95 83</span></div></div>
</div>
</div>
<section class="sec" id="promos">
<div class="shop-wrap">
<div class="sec-head rv">
<h2><span>🔥</span> Offres flash</h2>
@if(!empty($flashCode))<span class="off" style="position:static">-{{ $flashCode->type === 'percentage' ? $flashCode->value . '%' : number_format($flashCode->value, 0, ',', ' ') . ' FCFA' }} avec {{ $flashCode->code }}</span>@endif
<div class="count" role="timer" aria-label="Fin de l'offre" @if(!empty($flashEndsAt)) data-ends-at="{{ $flashEndsAt->timestamp }}" @endif>@if(!empty($flashEndsAt))<b id="d" class="idle">00</b><i>J</i><b id="h" class="idle">00</b><i>H</i><b id="m" class="idle">00</b><i>M</i><b id="s" class="idle">00</b><i>S</i>@else<b id="h" class="idle">02</b><i>:</i><b id="m" class="idle">14</b><i>:</i><b id="s" class="idle">36</b>@endif</div>
<a class="more" href="{{ route('products') }}">Tout voir <svg class="ic ic-sm"><use href="#i-chevron"/></svg></a>
</div>
<div class="grid">
@forelse($selection ?? [] as $item)
@php
$promo = $item->sale_price && $item->sale_price < $item->price;
$discount = $promo && $item->price > 0 ? round((($item->price - $item->sale_price) / $item->price) * 100) : 0;
$fallbacks = [1,2,3,4,5,6,7,8,10,12,16,17,18,19,20,21,23,24,25,26,27];
$photo = $item->getPhoto() ? $item->getPhoto()->getImageUrl(530,530) : asset('assets/img/products/' . $fallbacks[$item->id % count($fallbacks)] . '.png');
$itemUrl = route('produits.show', ['slug' => $item->getSlug(), 'id' => $item->id]);
@endphp
<article class="card rv">
<div class="thumb" style="background-image:url('{{ $photo }}')">
@if($promo)<span class="off">-{{ $discount }}%</span>@endif
<a class="fav" href="{{ auth()->check() ? route('favoris') : route('login') }}" aria-label="Voir mes favoris" title="Favori"><svg class="ic"><use href="#i-heart"/></svg><span class="tip">Mes favoris</span></a>
</div>
<div class="body">
<div class="name">{{ Str::limit($item->name, 32) }}</div>
@if($promo)<div class="was">{{ number_format($item->price, 0, ',', ' ') }} FCFA</div>@endif
<div class="price">{{ number_format($promo ? $item->sale_price : $item->price, 0, ',', ' ') }} FCFA</div>
<a href="{{ $itemUrl }}" class="add">Voir le produit</a>
</div>
</article>
@empty
<p class="empty">Aucun produit en sélection pour le moment.</p>
@endforelse
</div>
</div>
</section>
<section class="sec">
<div class="shop-wrap">
<div class="sec-head rv"><h2>Tous les produits</h2></div>
<livewire:client.top-deals />
</div>
</section>
</main>
@include('partials.footer')
<script>
(function(){var h=document.getElementById('h'),m=document.getElementById('m'),s=document.getElementById('s'),d=document.getElementById('d');if(!h)return;function pad(n){return String(n).padStart(2,'0');}function set(el,val){if(!el||el.textContent===val)return;el.textContent=val;el.classList.remove('idle');void el.offsetWidth;el.style.animation='none';void el.offsetWidth;el.style.animation='';}
var box=h.closest('.count'),fin=box&&box.getAttribute('data-ends-at')?parseInt(box.getAttribute('data-ends-at'),10):0;
if(fin>0){
function tick(){var r=Math.max(0,fin-Math.floor(Date.now()/1000));set(d,pad(Math.floor(r/86400)));set(h,pad(Math.floor(r%86400/3600)));set(m,pad(Math.floor(r%3600/60)));set(s,pad(r%60));if(r<=0){clearInterval(iv);var head=box.closest('.sec-head');if(head&&!head.querySelector('.flash-done')){var x=document.createElement('span');x.className='off flash-done';x.style.position='static';x.textContent='Offre terminée';head.appendChild(x);}}}
tick();var iv=setInterval(tick,1000);
}else{
var TOTAL=2*3600+14*60+36,reste=TOTAL;function loop(){if(reste<0)reste=TOTAL;set(h,pad(Math.floor(reste/3600)));set(m,pad(Math.floor(reste%3600/60)));set(s,pad(reste%60));reste--;}loop();setInterval(loop,1000);
}})();
</script>
@endsection

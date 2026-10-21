@extends('base')

@php
    $siteName = config('app.name', 'MandenBaoubab');
    $seoUrl = route('produits.show', ['slug' => $produit->getSlug(), 'id' => $produit->id]);
    $seoImage = $produit->getPhoto() ? $produit->getPhoto()->getImageUrl(1200, 1200) : asset('img/shop/hero-bg.jpg');
    $seoPrice = (float) (($produit->sale_price && $produit->sale_price < $produit->price) ? $produit->sale_price : $produit->price);
    $seoDesc = trim(preg_replace('/\s+/u', ' ', strip_tags($produit->description ?? '')));
    $seoDesc = mb_strlen($seoDesc) >= 60
        ? \Illuminate\Support\Str::limit($seoDesc, 160, '…')
        : 'Achetez ' . $produit->name . ' à ' . number_format($seoPrice, 0, ',', ' ') . ' FCFA sur ' . $siteName . '. Livraison rapide au Mali et paiement Mobile Money ou à la livraison.';
    $seoInStock = (int) ($produit->stock ?? 0) > 0;
    $seoCategory = $produit->category;
    $seoReviewsCount = $produit->reviews()->count();
    $seoRating = $seoReviewsCount > 0 ? round((float) $produit->reviews()->avg('nb_etoiles'), 1) : null;
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $produit->name,
        'url' => $seoUrl,
        'image' => [$seoImage],
        'description' => $seoDesc,
        'sku' => $produit->sku ?? 'PRD-' . str_pad($produit->id, 5, '0', STR_PAD_LEFT),
        'brand' => [
            '@type' => 'Brand',
            'name' => optional($produit->brand)->name ?? $siteName,
        ],
        'offers' => [
            '@type' => 'Offer',
            'url' => $seoUrl,
            'priceCurrency' => 'XOF',
            'price' => number_format($seoPrice, 2, '.', ''),
            'availability' => $seoInStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
            'seller' => ['@type' => 'Organization', 'name' => $siteName],
        ],
    ];
    if ($seoCategory) {
        $productSchema['category'] = $seoCategory->name;
    }
    if ($seoRating !== null) {
        $productSchema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => $seoRating,
            'reviewCount' => $seoReviewsCount,
        ];
    }
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('home')],
        ],
    ];
    if ($seoCategory) {
        $breadcrumbSchema['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $seoCategory->name,
            'item' => route('categories.show', $seoCategory->slug),
        ];
    }
    $breadcrumbSchema['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => count($breadcrumbSchema['itemListElement']) + 1,
        'name' => $produit->name,
        'item' => $seoUrl,
    ];
@endphp

@section('title', \Illuminate\Support\Str::limit($produit->name, 45, '…'))
@section('meta_description', $seoDesc)
@section('canonical', $seoUrl)
@section('og_image', $seoImage)
@section('og_image_alt', $produit->name)
@section('og_type', 'product')

@push('head')
    <script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a id="crumCat"
                href="{{ $seoCategory ? route('categories.show', $seoCategory->slug) : route('products') }}">{{ optional($produit->category)->name ?? 'Produits' }}</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here" id="crumNom">{{ $produit->name }}</span>
        </div>
    </nav>

    <section>
        <div class="wrap">
            @livewire('produit-detail', ['product' => $produit, 'slug' => $slug])
        </div>
    </section>

    @include('partials.footer')
@endsection

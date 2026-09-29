@extends('base')

@section('title', 'Mes produits — Vendeur')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('vendeur.dashboard') }}">Espace vendeur</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Mes produits</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Mes produits</h1>
            <p>Gérez votre catalogue vendeur.</p>
            <div class="cats" style="padding:14px 0 0">
                <a class="cat" href="{{ route('vendeur.dashboard') }}">Tableau de bord</a>
                <a class="cat hot" href="{{ route('vendeur.products.index') }}">Mes produits</a>
                <a class="cat" href="{{ route('vendeur.orders.index') }}">Commandes reçues</a>
            </div>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <form action="{{ route('vendeur.products.index') }}" method="GET"
                    style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1">
                    <input class="ctrl" style="border-radius:12px;min-width:200px" type="search" name="search"
                        placeholder="Nom, référence..." value="{{ request('search') }}" aria-label="Rechercher">
                    @if (request('search'))
                        <a class="btn-ghost-sm" href="{{ route('vendeur.products.index') }}">Effacer</a>
                    @endif
                </form>
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('vendeur.products.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Ajouter un produit</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-grid" />
                    </svg> Catalogue ({{ $products->total() }})</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nom</th>
                                <th>Prix</th>
                                <th>Stock</th>
                                <th>Actif</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @php
                                    $img = $product->getPhoto() ? $product->getPhoto()->getImageUrl(100, 100) : asset('assets/img/products/1.png');
                                    $stk = (int) ($product->stock ?? 0);
                                @endphp
                                <tr>
                                    <td><img src="{{ $img }}" alt="{{ $product->name }}"
                                            style="width:48px;height:48px;object-fit:cover;border-radius:12px;border:1px solid var(--line)">
                                    </td>
                                    <td><b>{{ Str::limit($product->name, 45) }}</b><br><small
                                            class="muted-sm">{{ $product->sku ?? '' }}</small></td>
                                    <td><b>{{ number_format($product->sale_price && $product->sale_price < $product->price ? $product->sale_price : $product->price, 0, ',', ' ') }}
                                            FCFA</b></td>
                                    <td><span
                                            class="stk {{ $stk <= 0 ? 'out' : ($stk <= 5 ? 'low' : 'ok') }}">{{ $stk }}</span>
                                    </td>
                                    <td>{!! $product->is_active ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}</td>
                                    <td style="white-space:nowrap">
                                        <a class="btn-ghost-sm"
                                            href="{{ route('vendeur.products.edit', $product) }}">Modifier</a>
                                        <form action="{{ route('vendeur.products.destroy', $product) }}" method="POST"
                                            style="display:inline"
                                            onsubmit="return confirm('Supprimer ce produit ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost-sm" style="color:var(--pink)">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="muted-sm">Aucun produit. Ajoutez votre premier article !</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($products->hasPages())
                    <div class="pager">
                        @if ($products->onFirstPage())
                            <button disabled>‹</button>
                        @else
                            <a href="{{ $products->previousPageUrl() }}"><button type="button">‹</button></a>
                        @endif
                        <button class="on">{{ $products->currentPage() }}</button>
                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}"><button type="button">›</button></a>
                        @else
                            <button disabled>›</button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection

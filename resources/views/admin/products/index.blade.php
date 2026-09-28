@extends('admin.base')

@section('title', 'Liste des produits')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Produits</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Produits</h1>
            <p>{{ $products->count() }} produit{{ $products->count() > 1 ? 's' : '' }} au catalogue.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <form action="{{ route('admin.products.index') }}" method="GET"
                    style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1">
                    <input class="ctrl" style="border-radius:12px;min-width:200px" type="search" name="search"
                        placeholder="Rechercher des produits..." value="{{ request('search') }}"
                        aria-label="Rechercher">
                    <select class="ctrl" name="category" onchange="this.form.submit()" aria-label="Catégorie">
                        <option value="">Toutes les catégories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <select class="ctrl" name="status" onchange="this.form.submit()" aria-label="Statut">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @if (request('search') || request('category') || request('status'))
                        <a class="btn-ghost-sm" href="{{ route('admin.products.index') }}">Effacer</a>
                    @endif
                </form>
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.products.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Ajouter un produit</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-grid" />
                    </svg> Catalogue</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nom du produit</th>
                                <th>Prix</th>
                                <th>Catégorie</th>
                                <th>Stock</th>
                                <th>Tags</th>
                                <th>Actif</th>
                                <th>Publié le</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @php
                                    $img = $product->getPhoto() ? $product->getPhoto()->getImageUrl(200, 200) : asset('assets/img/products/1.png');
                                    $stk = (int) ($product->stock ?? 0);
                                    $stkCls = $stk <= 0 ? 'out' : ($stk <= 5 ? 'low' : 'ok');
                                @endphp
                                <tr>
                                    <td><a class="th"
                                            style="display:block;width:52px;height:52px;border-radius:12px;border:1px solid var(--line);background:#f5f3ff center/cover no-repeat;background-image:url('{{ $img }}')"
                                            href="{{ route('admin.products.edit', $product) }}"
                                            aria-label="{{ $product->name }}"></a></td>
                                    <td><b><a
                                                href="{{ route('admin.products.edit', $product) }}">{{ Str::limit($product->name, 45) }}</a></b>
                                    </td>
                                    <td><b>{{ number_format($product->price, 0, ',', ' ') }} FCFA</b>
                                        @if ($product->sale_price)
                                            <br><small class="muted-sm">Soldé :
                                                {{ number_format($product->sale_price, 0, ',', ' ') }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $product->category->name ?? 'Sans catégorie' }}</td>
                                    <td><span class="stk {{ $stkCls }}">{{ $stk }}</span></td>
                                    <td>
                                        @forelse($product->tags as $tag)
                                            <small
                                                style="display:inline-block;font-size:11px;font-weight:600;background:var(--lav-1);border:1px solid #ddd6fe;color:var(--violet-800);border-radius:999px;padding:2px 9px;margin:0 4px 4px 0">{{ $tag->name }}</small>
                                        @empty
                                            <span class="muted-sm">—</span>
                                        @endforelse
                                    </td>
                                    <td>{!! $product->is_active ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}</td>
                                    <td>{{ $product->created_at?->format('d/m/Y') }}</td>
                                    <td style="white-space:nowrap">
                                        <a class="btn-ghost-sm"
                                            href="{{ route('admin.products.edit', $product) }}">Modifier</a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                            style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost-sm" style="color:var(--pink)"
                                                onclick="return confirm('Supprimer ce produit ?')">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="empty" style="margin:0">
                                            <h3 style="font-size:16px">Aucun produit trouvé</h3>
                                            @if (request('search') || request('category') || request('status'))
                                                <div class="pdp-actions" style="justify-content:center;margin-top:14px">
                                                    <a class="btn-line"
                                                        href="{{ route('admin.products.index') }}">Réinitialiser les
                                                        filtres</a>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

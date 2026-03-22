<table class="table fs-9 mb-0">
    <thead>
        <tr>
            <th class="white-space-nowrap fs-9 align-middle ps-0" style="max-width:20px; width:18px;">
                <div class="form-check mb-0 fs-8"><input class="form-check-input" id="checkbox-bulk-products-select"
                        type="checkbox" data-bulk-select='{"body":"products-table-body"}' /></div>
            </th>
            <th class="sort white-space-nowrap align-middle fs-10" scope="col" style="width:70px;"></th>
            <th class="sort white-space-nowrap align-middle ps-4" scope="col" style="width:350px;"
                data-sort="product">NOM DU PRODUIT</th>
            <th class="sort align-middle text-end ps-4" scope="col" data-sort="price" style="width:150px;">PRIX</th>
            <th class="sort align-middle ps-4" scope="col" data-sort="category" style="width:150px;">CATEGORIE</th>
            <th class="sort align-middle ps-3" scope="col" data-sort="tags" style="width:250px;">
                TAGS</th>
            <th class="sort align-middle fs-8 text-center ps-4" scope="col" style="width:125px;">
            </th>
            <th class="sort align-middle ps-4" scope="col" data-sort="time" style="width:50px;">
                DATE DE PUBLICATION</th>
            <th class="sort text-end align-middle pe-0 ps-4" scope="col"></th>
        </tr>
    </thead>
    <tbody class="list" id="products-table-body">
        @forelse($products as $product)
            <tr class="position-static">
                <td class="fs-9 align-middle">
                    <div class="form-check mb-0 fs-8">
                        <input class="form-check-input" type="checkbox"
                            data-bulk-select-row='{"product":"{{ $product->name }}","productImage":"{{ $product->image ? asset('storage/' . $product->image) : '' }}","price":"{{ number_format($product->price, 2, ',', ' ') }} €","category":"{{ $product->category->name ?? 'Sans catégorie' }}","star":{{ $product->is_active ? 'true' : 'false' }}}' />
                    </div>
                </td>
                <td class="align-middle white-space-nowrap py-0">
                    <a class="d-block border border-translucent rounded-2"
                        href="{{ route('admin.products.edit', $product) }}">
                        @if ($product->getPhoto())
                            <img src="{{ $product->getPhoto()->getImageUrl(530, 530) }}" alt="{{ $product->name }}"
                                width="53" />
                        @endif
                    </a>
                </td>
                <td class="product align-middle ps-4">
                    <a class="fw-semibold line-clamp-3 mb-0" href="{{ route('admin.products.edit', $product) }}">
                        {{ $product->name }}
                    </a>
                </td>
                <td class="price align-middle white-space-nowrap text-end fw-bold text-body-tertiary ps-4">
                    {{ number_format($product->price, 2, ',', ' ') }} FCFA
                </td>
                <td class="category align-middle white-space-nowrap text-body-quaternary fs-9 ps-4 fw-semibold">
                    {{ $product->category->name ?? 'Sans catégorie' }}
                </td>
                <td class="tags align-middle review pb-2 ps-3" style="min-width:225px;">
                    @forelse($product->tags as $tag)
                        <span class="badge bg-primary me-1 mb-1">{{ $tag->name }}</span>
                    @empty
                        <span class="text-muted small">Aucun tag</span>
                    @endforelse
                </td>
                <td class="align-middle review fs-8 text-center ps-4">
                    <div class="d-toggle-container">
                        @if ($product->is_active)
                            <div class="d-block-hover"><span class="fas fa-star text-warning"></span></div>
                        @else
                            <div class="d-none-hover"><span class="far fa-star text-warning"></span></div>
                        @endif
                    </div>
                </td>
                <td class="time align-middle white-space-nowrap text-body-tertiary text-opacity-85 ps-4">
                    {{ $product->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="align-middle white-space-nowrap text-end pe-0 ps-4 btn-reveal-trigger">
                    <div class="btn-reveal-trigger position-static">
                        <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10"
                            type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true"
                            aria-expanded="false" data-bs-reference="parent">
                            <span class="fas fa-ellipsis-h fs-10"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end py-2">
                            <a class="dropdown-item" href="{{ route('admin.products.edit', $product) }}">Modifier</a>
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <p class="h5 mb-2">Aucun produit trouvé</p>
                        @if (request('search') || request('category') || request('status'))
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary mt-2">
                                <i class="fas fa-sync-alt me-1"></i> Réinitialiser les filtres
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

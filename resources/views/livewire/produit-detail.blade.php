<div>
    @php
        $mainImg = $images[0] ?? asset('assets/img/products/1.png');
        $stock = (int) ($product->stock ?? 0);
        $discount = $original > $price && $original > 0 ? round((($original - $price) / $original) * 100) : 0;
        $stCls = $stock <= 0 ? 'out' : ($stock <= 8 ? 'low' : 'ok');
        $stTxt = $stock <= 0 ? 'Rupture de stock' : ($stock <= 8 ? 'Plus que ' . $stock . ' en stock' : "En stock — expédié aujourd'hui");
        $catName = optional($product->category)->name ?? 'Produits';
        $ref = $product->sku ?? 'REF-' . $product->id;
        $desc = strip_tags($product->description ?? '');
        $short = mb_strlen($desc) > 180 ? mb_substr($desc, 0, 180) . '...' : $desc;
        $noteFmt = number_format((float) $ratingAvg, 1, ',', '');
        $favIds = auth()->check() ? auth()->user()->wishlistProducts()->pluck('produits.id')->all() : [];
        $hexMap = ['noir' => '#111827', 'blanc' => '#ffffff', 'rouge' => '#dc2626', 'bleu' => '#2563eb', 'vert' => '#16a34a', 'jaune' => '#facc15', 'rose' => '#f472b6', 'gris' => '#9ca3af', 'violet' => '#7c3aed', 'orange' => '#f97316', 'marron' => '#92400e', 'beige' => '#e7d8b7', 'dor' => '#d4af37', 'argent' => '#c0c0c0'];
        $hexOf = function ($nom) use ($hexMap) {
            $n = mb_strtolower(trim((string) $nom));
            foreach ($hexMap as $k => $h) {
                if (str_contains($n, $k)) {
                    return $h;
                }
            }
            return '#e5e7eb';
        };
        $catLower = mb_strtolower($catName);
        $catIcon = 'i-phones';
        if (str_contains($catLower, 'montre')) {
            $catIcon = 'i-watch';
        } elseif (str_contains($catLower, 'audio') || str_contains($catLower, 'enceinte') || str_contains($catLower, 'casque') || str_contains($catLower, 'son')) {
            $catIcon = 'i-speaker';
        } elseif (str_contains($catLower, 'electrom') || str_contains($catLower, 'lave') || str_contains($catLower, 'cuisine') || str_contains($catLower, 'maison')) {
            $catIcon = 'i-washer';
        } elseif (str_contains($catLower, 'ventil') || str_contains($catLower, 'clim')) {
            $catIcon = 'i-fan';
        } elseif (str_contains($catLower, 'phone') || str_contains($catLower, 'tel') || str_contains($catLower, 'smart') || str_contains($catLower, 'electron') || str_contains($catLower, 'informatique')) {
            $catIcon = 'i-phone';
        }
    @endphp

    <div class="pdp" id="pdp">
        <div class="pdp-gal">
            <div class="g-main" id="gMain" style="background-image:url('{{ $mainImg }}')">
                @if ($discount > 0)
                    <span class="off">-{{ $discount }}%</span>
                @endif
                @auth
                    <button class="fav{{ $iswishlisted ? ' on' : '' }}" type="button" aria-label="{{ $iswishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris' }}" title="Favori"
                        wire:click="toggleWishlist({{ $product->id }})"><svg class="ic">
                            <use href="#i-heart" />
                        </svg><span class="tip">{{ $iswishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</span></button>
                @else
                    <a class="fav" href="{{ route('login') }}" aria-label="Ajouter aux favoris" title="Favori"><svg class="ic">
                            <use href="#i-heart" />
                        </svg><span class="tip">Connectez-vous pour liker</span></a>
                @endauth
                <button class="g-nav prev" type="button" data-gal="-1" aria-label="Image précédente"><svg class="ic">
                        <use href="#i-chevron" />
                    </svg></button>
                <button class="g-nav next" type="button" data-gal="1" aria-label="Image suivante"><svg class="ic">
                        <use href="#i-chevron" />
                    </svg></button>
            </div>
            <div class="g-thumbs">
                @foreach ($images as $i => $img)
                    <button type="button" class="g-th{{ $i === 0 ? ' on' : '' }}" data-th="{{ $i }}"
                        data-src="{{ $img }}" style="background-image:url('{{ $img }}')"><span>Vue
                            {{ $i + 1 }}</span></button>
                @endforeach
            </div>
        </div>

        <div class="pdp-info">
            @if ($prevProduct || $nextProduct)
                <div class="siblings">
                    @if ($prevProduct)
                        <a class="sib"
                            href="{{ route('produits.show', ['slug' => $prevProduct['slug'], 'id' => $prevProduct['id']]) }}"
                            title="{{ $prevProduct['name'] }}"><span class="sib-k">← Précédent</span><span
                                class="sib-n">{{ $prevProduct['name'] }}</span></a>
                    @endif
                    @if ($prevProduct)
                        <span class="sib-c">{{ $prevProduct['pos'] }} / {{ $prevProduct['total'] }}</span>
                    @endif
                    @if ($nextProduct)
                        <a class="sib next"
                            href="{{ route('produits.show', ['slug' => $nextProduct['slug'], 'id' => $nextProduct['id']]) }}"
                            title="{{ $nextProduct['name'] }}"><span class="sib-k">Suivant →</span><span
                                class="sib-n">{{ $nextProduct['name'] }}</span></a>
                    @endif
                </div>
            @endif
            <div class="tagline"><svg class="ic">
                    <use href="#i-b2-tag" />
                </svg><span>{{ $catName }} · Réf. {{ $ref }}</span></div>
            <h1>{{ $product->name ?? 'Produit' }}</h1>
            <div class="rating-row">
                <span class="stars" aria-hidden="true">@for ($i = 1; $i <= 5; $i++)<svg viewBox="0 0 24 24"
                        class="{{ $i <= round($ratingAvg) ? '' : 'empty' }}">
                        <use href="#i-b2-star" />
                    </svg>@endfor</span>
                <b>{{ $noteFmt }}</b><span>({{ $ratingCount }} avis)</span>
                <span class="sep2"></span><span>{{ $vendus }} vendus</span>
                <span class="sep2"></span><span class="stock {{ $stCls }}">{{ $stTxt }}</span>
            </div>
            <div class="pricebox">
                <span class="now">{{ $this->formatFcfa($price) }}</span>
                @if ($original > $price)
                    <span class="was">{{ $this->formatFcfa($original) }}</span>
                    <span class="save">Vous économisez {{ $this->formatFcfa($original - $price) }}</span>
                @endif
            </div>
            @if ($original > $price)
                <div class="flash-line"><svg class="ic" style="fill:currentColor;stroke:currentColor">
                        <use href="#i-bolt" />
                    </svg><span>Offre flash — se termine dans</span><span class="count" id="flash"><b>05</b><i>:</i><b>59</b><i>:</i><b>59</b></span>
                </div>
            @endif
            @if ($short)
                <p class="lead">{{ $short }}</p>
            @endif

            @if (!empty($couleurs) && count($couleurs))
                <div class="opt" id="optCouleur">
                    <label>Couleur : <b data-lab>{{ $couleurs->first()->pivot->value ?? '' }}</b></label>
                    <div class="swatches">
                        @foreach ($couleurs as $i => $c)
                            <button type="button" class="swatch{{ $i === 0 ? ' on' : '' }}"
                                data-optval="{{ $c->pivot->value ?? '' }}" style="background:{{ $hexOf($c->pivot->value ?? '') }}"
                                title="{{ $c->pivot->value ?? '' }}" aria-label="{{ $c->pivot->value ?? '' }}"></button>
                        @endforeach
                    </div>
                </div>
            @endif

            @php
                $iconFor = function ($type) {
                    $t = mb_strtolower(trim((string) $type));
                    if (str_contains($t, 'marque') || str_contains($t, 'brand')) return '#i-b2-tag';
                    if (str_contains($t, 'stockage') || str_contains($t, 'disque') || str_contains($t, 'mémoire') || str_contains($t, 'memoire') || str_contains($t, 'rom')) return '#i-phone';
                    if (str_contains($t, 'ram')) return '#i-bolt';
                    if (str_contains($t, 'ecran') || str_contains($t, 'affichage') || str_contains($t, 'taille')) return '#i-grid';
                    if (str_contains($t, 'batterie') || str_contains($t, 'autonomie')) return '#i-card';
                    if (str_contains($t, 'process') || str_contains($t, 'cpu') || str_contains($t, 'puce')) return '#i-bolt';
                    if (str_contains($t, 'cam') || str_contains($t, 'photo')) return '#i-search';
                    if (str_contains($t, 'poids')) return '#i-bag';
                    if (str_contains($t, 'garantie')) return '#i-b2-lock';
                    return '#i-b2-check';
                };
                $selOpts = $this->selectedOptions ?? [];
            @endphp

            {{-- Options sélectionnables à l'achat : toutes les caractéristiques groupées par type --}}
            @if (!empty($optionGroups))
                <div class="opt" id="optChoix">
                    <label>Choisir vos options <span style="font-weight:500;color:var(--grey)">· prises en
                            compte dans le panier</span></label>
                    @foreach ($optionGroups as $grp)
                        @php
                            $gKey = $grp['key'];
                            $gSel = $selOpts[$gKey] ?? null;
                            $isColor = in_array($gKey, ['couleur', 'couleur_tissu']);
                        @endphp
                        <div class="opt-grp" data-opt-group="{{ $gKey }}">
                            <label>{{ $grp['label'] }} : <b>{{ $gSel ?? '—' }}</b></label>
                            @if ($isColor)
                                <div class="swatches">
                                    @foreach ($grp['values'] as $v)
                                        <button type="button" class="swatch{{ $gSel === $v['display'] ? ' on' : '' }}"
                                            wire:click="selectOption({{ Js::from($gKey) }}, {{ Js::from($v['display']) }})"
                                            style="background:{{ $hexOf($v['value']) }}"
                                            title="{{ $v['display'] }}"
                                            aria-label="{{ $grp['label'] }} {{ $v['display'] }}"></button>
                                    @endforeach
                                </div>
                            @else
                                <div class="opt-pills">
                                    @foreach ($grp['values'] as $v)
                                        <button type="button" class="opt-pill{{ $gSel === $v['display'] ? ' on' : '' }}"
                                            wire:click="selectOption({{ Js::from($gKey) }}, {{ Js::from($v['display']) }})"
                                            aria-label="{{ $grp['label'] }} {{ $v['display'] }}">{{ $v['display'] }}</button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                    @if (!empty($selOpts))
                        <div class="sel-recap"><svg class="ic">
                                <use href="#i-b2-check" />
                            </svg><span><b>Sélection :</b>
                                @foreach ($this->selectedOptionsLabels() as $lk => $lv)
                                    {{ $lk }} : <b>{{ $lv }}</b>@if (!$loop->last) · @endif
                                @endforeach
                            </span></div>
                    @endif
                </div>
            @endif

            @if (!empty($autresCaracs) && count($autresCaracs))
                <div class="opt" id="optSpecs">
                    <label>Détails produit <span style="font-weight:500;color:var(--grey)">· {{ count($autresCaracs) }} infos</span></label>
                    <div class="spec-chips">
                        @foreach ($autresCaracs->take(6) as $c)
                            <span class="spec-chip" title="{{ $c->name }} : {{ $c->pivot->value ?? '' }}{{ $c->unite ? ' '.$c->unite : '' }}">
                                <span class="e"><svg class="ic"><use href="{{ $iconFor($c->type ?? $c->name) }}" /></svg></span>
                                <span class="k">{{ $c->name }}</span>
                                <span class="v">{{ $c->pivot->value ?? '—' }}{{ $c->unite ? ' '.$c->unite : '' }}</span>
                            </span>
                        @endforeach
                    </div>
                    @if (count($autresCaracs) > 6 || $tab !== 'spec')
                        <button type="button" class="spec-more" wire:click="$set('tab', 'spec')">Voir toutes les caractéristiques <svg class="ic ic-sm"><use href="#i-chevron" /></svg></button>
                    @endif
                </div>
            @endif

            <div class="qty-row">
                <div class="stepper">
                    <button type="button" wire:click="decrementQty" aria-label="Diminuer"><svg class="ic">
                            <use href="#i-b2-minus" />
                        </svg></button>
                    <input type="number" min="1" max="{{ max($stock, 1) }}" wire:model.live.debounce.300ms="quantity"
                        aria-label="Quantité">
                    <button type="button" wire:click="incrementQty" aria-label="Augmenter"><svg class="ic">
                            <use href="#i-b2-plus" />
                        </svg></button>
                </div>
                <span class="hint">Total : <b style="color:var(--pink);font-size:15px">{{ $this->formatFcfa($price * max(1, (int) $quantity)) }}</b></span>
            </div>

            <div class="pdp-actions">
                @auth
                    <button class="btn-solid" type="button" wire:click="addToCart({{ $product->id }})"
                        @if (!$inStock) disabled @endif><svg class="ic">
                            <use href="#i-bag" />
                        </svg> Ajouter au panier</button>
                    <button class="btn-line" type="button" wire:click="addToCart({{ $product->id }})"
                        @if (!$inStock) disabled @endif><svg class="ic">
                            <use href="#i-bolt" />
                        </svg> Acheter maintenant</button>
                    <button class="btn-line{{ $iswishlisted ? ' on' : '' }}" type="button"
                        wire:click="toggleWishlist({{ $product->id }})" aria-label="Favoris"><svg class="ic">
                            <use href="#i-heart" />
                        </svg> Favoris</button>
                    <a class="btn-line" href="{{ $this->whatsappOrderUrl() }}" target="_blank" rel="noopener"
                        aria-label="Commander {{ $product->name }} sur WhatsApp" title="Commander sur WhatsApp"><svg class="ic ic-wa">
                            <use href="#i-whatsapp" />
                        </svg> WhatsApp</a>
                @else
                    <a class="btn-solid" href="{{ route('login') }}"><svg class="ic">
                            <use href="#i-bag" />
                        </svg> Ajouter au panier</a>
                    <a class="btn-line" href="{{ route('login') }}"><svg class="ic">
                            <use href="#i-bolt" />
                        </svg> Acheter maintenant</a>
                    <a class="btn-line" href="{{ route('login') }}" aria-label="Favoris"><svg class="ic">
                            <use href="#i-heart" />
                        </svg> Favoris</a>
                    <a class="btn-line" href="{{ $this->whatsappOrderUrl() }}" target="_blank" rel="noopener"
                        aria-label="Commander {{ $product->name }} sur WhatsApp" title="Commander sur WhatsApp"><svg class="ic ic-wa">
                            <use href="#i-whatsapp" />
                        </svg> WhatsApp</a>
                @endauth
            </div>

            <div class="assurances">
                <div><svg class="ic">
                        <use href="#i-truck" />
                    </svg><span><b>Livraison offerte dès 25 000 FCFA</b>Bamako 24 h · Régions 48-72 h · sinon calculée au
                        panier</span></div>
                <div><svg class="ic">
                        <use href="#i-card" />
                    </svg><span><b>Paiement Mobile Money</b>Orange Money · Moov Money · Wave · espèces à la
                        livraison</span></div>
                <div><svg class="ic">
                        <use href="#i-headset" />
                    </svg><span><b>Support 7j/7 — +223 82 01 95 83</b>Conseil produit et suivi de commande</span></div>
            </div>
        </div>
    </div>

    <div id="tabs">
        <div class="tabs">
            <div class="tabs-head">
                <button type="button" class="tab-btn{{ $tab === 'desc' ? ' on' : '' }}"
                    wire:click="$set('tab', 'desc')">Description</button>
                <button type="button" class="tab-btn{{ $tab === 'spec' ? ' on' : '' }}"
                    wire:click="$set('tab', 'spec')">Caractéristiques</button>
                <button type="button" class="tab-btn{{ $tab === 'avis' ? ' on' : '' }}"
                    wire:click="$set('tab', 'avis')">Avis ({{ $ratingCount }})</button>
                <button type="button" class="tab-btn{{ $tab === 'exp' ? ' on' : '' }}"
                    wire:click="$set('tab', 'exp')">Livraison &amp; retour</button>
            </div>
            <div class="tabs-body">
                @if ($tab === 'desc')
                    {!! $product->description ?? '<p>Aucune description.</p>' !!}
                @elseif($tab === 'spec')
                    @php $specList = ($allCaracs ?? $product->caracteristiques ?? collect()); @endphp
                    @if ($specList && $specList->isNotEmpty())
                        <div class="spec-chips" style="margin-bottom:16px">
                            @foreach ($specList as $c)
                                <span class="spec-chip">
                                    <span class="e"><svg class="ic"><use href="{{ $iconFor($c->type ?? $c->name) }}" /></svg></span>
                                    <span class="k">{{ $c->name }}</span>
                                    <span class="v">{{ $c->pivot->value ?? '—' }}@if ($c->unite) {{ $c->unite }}@endif</span>
                                </span>
                            @endforeach
                        </div>
                        <table class="spec">
                            <tbody>
                                @foreach ($specList as $c)
                                    <tr>
                                        <td><span style="display:inline-flex;align-items:center;gap:8px"><svg class="ic ic-sm" style="color:var(--violet-600)"><use href="{{ $iconFor($c->type ?? $c->name) }}" /></svg>{{ $c->name }}</span>@if($c->type)<br><small style="color:#9ca3af;font-size:11px">{{ ucfirst($c->type) }}</small>@endif</td>
                                        <td><b>{{ $c->pivot->value ?? '—' }}</b>@if ($c->unite) {{ $c->unite }}@endif</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty" style="margin:0"><svg class="ic"><use href="#i-b2-info" /></svg>
                            <h3 style="font-size:16px">Caractéristiques en cours d'ajout</h3>
                            <p>Notre équipe complète la fiche technique de ce produit.</p>
                        </div>
                    @endif
                @elseif($tab === 'avis')
                    @forelse($reviews as $review)
                        @php $rn = optional($review->user)->name ?? 'Client'; @endphp
                        <div class="review"><span
                                class="av">{{ mb_strtoupper(mb_substr(trim($rn), 0, 1)) }}</span>
                            <div style="flex:1"><b>{{ $rn }}</b>
                                <small>{{ optional($review->created_at)->format('d/m/Y') }}</small>
                                <p>{{ $review->commentaire ?? '' }}</p>
                                @if ($review->relationLoaded('photos') && $review->photos->isNotEmpty())
                                    <div class="rphotos">
                                        @foreach ($review->photos as $rp)
                                            <img src="{{ $rp->getImageUrl(200, 200) }}" alt="Photo avis {{ $rn }}"
                                                loading="lazy">
                                        @endforeach
                                    </div>
                                @endif
                                @if ($review->relationLoaded('response') && $review->response)
                                    <div class="rrep"><b>Réponse de la boutique</b>{{ $review->response->message }}</div>
                                @endif
                            </div>
                            <span class="stars" aria-hidden="true">@for ($i = 1; $i <= 5; $i++)<svg
                                    viewBox="0 0 24 24" class="{{ $i <= round((float) ($review->nb_etoiles ?? 0)) ? '' : 'empty' }}">
                                    <use href="#i-b2-star" />
                                </svg>@endfor</span>
                        </div>
                    @empty
                        <p>Aucun avis pour le moment. Soyez le premier à partager votre expérience !</p>
                    @endforelse
                    @if ($reviews->hasPages())
                        <div class="pager">
                            <button type="button" class="pg-nav" wire:click="previousPage"
                                @if ($reviews->onFirstPage()) disabled @endif>‹ Précédent</button>
                            @foreach ($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                                <button type="button" wire:click="gotoPage({{ $page }})"
                                    class="{{ $page == $reviews->currentPage() ? 'on' : '' }}">{{ $page }}</button>
                            @endforeach
                            <button type="button" class="pg-nav" wire:click="nextPage"
                                @if (!$reviews->hasMorePages()) disabled @endif>Suivant ›</button>
                        </div>
                    @endif
                    <div class="avis-card">
                        @auth
                            <div class="avis-card-head"><svg class="ic">
                                    <use href="#i-b2-star" />
                                </svg><b>Laisser un avis</b><span>{{ $rating }}/5</span></div>
                            <div class="avis-card-body">
                                @if (session('review_ok'))
                                    <p class="avis-ok"><svg class="ic" style="width:17px;height:17px">
                                            <use href="#i-b2-check" />
                                        </svg>{{ session('review_ok') }}</p>
                                @endif
                                <div class="field">
                                    <label>Votre note</label>
                                    <div class="rate-pick">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button type="button" wire:click="$set('rating', {{ $i }})"
                                                class="{{ $i <= $rating ? 'on' : '' }}"
                                                aria-label="{{ $i }} étoile(s)">
                                                <svg viewBox="0 0 24 24" width="24" height="24"
                                                    style="stroke-width:1;fill:{{ $i <= $rating ? 'var(--pink)' : '#e5e7eb' }};stroke:{{ $i <= $rating ? 'var(--pink)' : '#d1d5db' }}">
                                                    <use href="#i-b2-star" />
                                                </svg>
                                            </button>
                                        @endfor
                                    </div>
                                    @error('rating') <span class="avis-err">{{ $message }}</span> @enderror
                                </div>
                                <div class="field">
                                    <label for="avisComment">Votre commentaire</label>
                                    <textarea id="avisComment" class="ctrl" rows="4" wire:model="commentaire" placeholder="Qualité, livraison, rapport qualité-prix..."
                                        style="border-radius:12px;resize:vertical;min-height:96px"></textarea>
                                    @error('commentaire') <span class="avis-err">{{ $message }}</span> @enderror
                                </div>
                                <div class="field">
                                    <label for="avisPhotos">Photos (optionnel, 5 max)</label>
                                    <label class="dropzone" for="avisPhotos"><svg class="ic">
                                            <use href="#i-bag" />
                                        </svg><span>Cliquez pour ajouter vos photos<br><small
                                                style="color:var(--grey)">JPG, PNG — 10 Mo max par photo</small></span></label>
                                    <input id="avisPhotos" type="file" wire:model="avisPhotos" multiple
                                        accept="image/*" hidden>
                                    @error('avisPhotos') <span class="avis-err">{{ $message }}</span> @enderror
                                    @error('avisPhotos.*') <span class="avis-err">{{ $message }}</span> @enderror
                                    @if (!empty($avisPhotos))
                                        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px">
                                            @foreach ($avisPhotos as $idx => $ph)
                                                <span class="aprev">
                                                    <img src="{{ $ph->temporaryUrl() }}" alt="Aperçu photo avis">
                                                    <button type="button" wire:click="removeAvisPhoto({{ $idx }})"
                                                        aria-label="Retirer cette photo">×</button>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <button class="btn-solid" type="button" wire:click="submitReview"
                                    wire:loading.attr="disabled" style="font-size:14px;padding:13px 26px">
                                    <svg class="ic"><use href="#i-b2-check" /></svg>
                                    <span wire:loading.remove wire:target="submitReview">Publier mon avis</span>
                                    <span wire:loading wire:target="submitReview">Publication…</span>
                                </button>
                            </div>
                        @else
                            <div class="avis-card-head"><svg class="ic">
                                    <use href="#i-b2-lock" />
                                </svg><b>Partagez votre expérience</b></div>
                            <div class="avis-card-body" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
                                <p style="font-size:13.5px;color:#374151;flex:1;min-width:200px">Connectez-vous pour
                                    noter ce produit et aider les autres clients.</p>
                                <a class="btn-solid" href="{{ route('login') }}"
                                    style="font-size:13.5px;padding:12px 24px">Se connecter</a>
                            </div>
                        @endauth
                    </div>
                @elseif($tab === 'exp')
                    <table class="spec">
                        <tbody>
                            <tr>
                                <td>Bamako (24 h)</td>
                                <td>Offerte dès 25 000 FCFA, sinon calculée au panier</td>
                            </tr>
                            <tr>
                                <td>Autres régions (48-72 h)</td>
                                <td>Calculée au panier selon la destination</td>
                            </tr>
                            <tr>
                                <td>Paiement</td>
                                <td>Orange Money, Moov Money, Wave, espèces à la livraison</td>
                            </tr>
                            <tr>
                                <td>Retour</td>
                                <td>7 jours après réception, produit non utilisé dans son emballage</td>
                            </tr>
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    @if (!empty($similarProducts) && count($similarProducts))
        <section class="sec mini">
            <div class="sec-head">
                <h2><span>✨</span> Produits similaires</h2>
                <a class="more" href="{{ route('products') }}">Tout voir <svg class="ic ic-sm">
                        <use href="#i-chevron" />
                    </svg></a>
            </div>
            <div class="grid plist" id="similaires">
                @foreach ($similarProducts->take(4) as $p)
                    @php
                        $pp = $p->sale_price && $p->sale_price < $p->price ? $p->sale_price : $p->price;
                        $po = $p->price;
                        $pd = $po > $pp && $po > 0 ? round((($po - $pp) / $po) * 100) : 0;
                        $pst = (int) ($p->stock ?? 0);
                        $pstCls = $pst <= 0 ? 'out' : ($pst <= 8 ? 'low' : 'ok');
                        $pstTxt = $pst <= 0 ? 'Rupture de stock' : ($pst <= 8 ? 'Plus que ' . $pst . ' en stock' : 'En stock');
                        $pImg = $p->getPhoto() ? $p->getPhoto()->getImageUrl(530, 530) : null;
                        $pAvg = round($p->reviews->avg('nb_etoiles') ?? 5);
                        $pUrl = route('produits.show', ['slug' => $p->getSlug(), 'id' => $p->id]);
                        $pFav = in_array($p->id, $favIds);
                    @endphp
                    <article class="card{{ $pst <= 0 ? ' sold' : '' }}">
                        @if ($pImg)
                            <img class="thumb" src="{{ $pImg }}" alt="{{ $p->name }}" loading="lazy">
                        @else
                            <div class="thumb"><svg class="ic" aria-hidden="true">
                                    <use href="#{{ $catIcon }}" />
                                </svg></div>
                        @endif
                        <div class="body">
                            @if ($pd > 0)
                                <span class="off">-{{ $pd }}%</span>
                            @endif
                            @auth
                                <button class="fav{{ $pFav ? ' on' : '' }}" type="button" aria-label="{{ $pFav ? 'Retirer des favoris' : 'Ajouter aux favoris' }}" title="Favori"
                                    wire:click="toggleWishlist({{ $p->id }})"><svg class="ic">
                                        <use href="#i-heart" />
                                    </svg><span class="tip">{{ $pFav ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</span></button>
                            @else
                                <a class="fav" href="{{ route('login') }}" aria-label="Ajouter aux favoris" title="Favori"><svg class="ic">
                                        <use href="#i-heart" />
                                    </svg><span class="tip">Connectez-vous pour liker</span></a>
                            @endauth
                            <a class="name" href="{{ $pUrl }}">{{ Str::limit($p->name, 55) }}</a>
                            <div class="rate"><span class="stars" aria-hidden="true">@for ($i = 1; $i <= 5; $i++)<svg
                                        viewBox="0 0 24 24" class="{{ $i <= $pAvg ? '' : 'empty' }}">
                                        <use href="#i-b2-star" />
                                    </svg>@endfor</span><span>{{ number_format($p->reviews->avg('nb_etoiles') ?? 5, 1, ',', '') }}
                                    ({{ $p->reviews->count() }})</span></div>
                            @if ($po > $pp)
                                <div class="was">{{ $this->formatFcfa($po) }}</div>
                            @endif
                            <div class="prices">
                                <div class="price">{{ $this->formatFcfa($pp) }}</div>
                            </div>
                            <div class="stock {{ $pstCls }}">{{ $pstTxt }}</div>
                            @auth
                                <button class="add" type="button" wire:click="addToCart({{ $p->id }}, 1)"
                                    @if ($pst <= 0) disabled @endif>{{ $pst <= 0 ? 'Indisponible' : 'Ajouter au panier' }}</button>
                            @else
                                <a class="add" href="{{ route('login') }}"
                                    style="display:block">{{ $pst <= 0 ? 'Indisponible' : 'Ajouter au panier' }}</a>
                            @endauth
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="buybar" id="buybar">
        <span class="now">{{ $this->formatFcfa($price * max(1, (int) $quantity)) }}</span>
        @auth
            <button class="btn-solid" type="button" wire:click="addToCart({{ $product->id }})"
                @if (!$inStock) disabled @endif><svg class="ic">
                    <use href="#i-bag" />
                </svg> Ajouter</button>
        @else
            <a class="btn-solid" href="{{ route('login') }}"><svg class="ic">
                    <use href="#i-bag" />
                </svg> Ajouter</a>
        @endauth
    </div>

    <script>
        (function() {
            if (window._pdInit) return;
            window._pdInit = true;
            document.body.classList.add('has-buybar');
            window._pdImg = window._pdImg || 0;
            function thumbs() {
                return Array.prototype.slice.call(document.querySelectorAll('#pdp .g-th'));
            }
            function montrer(i) {
                var list = thumbs();
                if (!list.length) return;
                i = ((i % list.length) + list.length) % list.length;
                window._pdImg = i;
                var main = document.getElementById('gMain');
                if (main && list[i]) main.style.backgroundImage = 'url(' + list[i].getAttribute('data-src') + ')';
                list.forEach(function(b, j) {
                    b.classList.toggle('on', j === i);
                });
            }
            document.addEventListener('click', function(e) {
                var t = e.target;
                if (!t.closest) return;
                var th = t.closest('#pdp .g-th');
                if (th) {
                    montrer(thumbs().indexOf(th));
                    return;
                }
                var nav = t.closest('#pdp [data-gal]');
                if (nav) {
                    montrer((window._pdImg || 0) + parseInt(nav.getAttribute('data-gal'), 10));
                    return;
                }
            });
            function tic() {
                var el = document.getElementById('flash');
                if (!el) return;
                var fin = new Date();
                fin.setHours(23, 59, 59, 999);
                var s = Math.max(0, Math.floor((fin - new Date()) / 1000));
                var h = Math.floor(s / 3600),
                    m = Math.floor((s % 3600) / 60),
                    sec = s % 60;
                function p2(n) {
                    return String(n).padStart(2, '0');
                }
                el.innerHTML = '<b>' + p2(h) + '</b><i>:</i><b>' + p2(m) + '</b><i>:</i><b>' + p2(sec) + '</b>';
            }
            function startFlash() {
                if (window._pdTimer) {
                    try {
                        clearInterval(window._pdTimer);
                    } catch (err) {}
                    window._pdTimer = null;
                }
                if (document.getElementById('flash')) {
                    tic();
                    window._pdTimer = setInterval(tic, 1000);
                }
            }
            startFlash();
            /* Rafraîchit la galerie après navigation SPA (le script inline ne se ré-exécute pas). */
            window.refreshPDP = function() {
                window._pdImg = 0;
                montrer(0);
                startFlash();
            };
            document.addEventListener('livewire:init', function() {
                if (window.Livewire) {
                    try {
                        Livewire.hook('morph.updated', function() {
                            montrer(window._pdImg || 0);
                            tic();
                        });
                    } catch (err) {}
                }
            });
        })();
    </script>
</div>

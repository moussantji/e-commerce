<div>
    <style>
        /* ===== Modal Commander — ultra premium ===== */
        .lux-veil{position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center;padding:18px;background:radial-gradient(1200px 600px at 50% -10%,rgba(139,92,246,.28),transparent 60%),rgba(30,10,70,.58);-webkit-backdrop-filter:blur(10px) saturate(1.2);backdrop-filter:blur(10px) saturate(1.2);opacity:0;pointer-events:none;transition:opacity .3s ease}
        .lux-veil.open{opacity:1;pointer-events:auto}
        .lux-panel{width:min(600px,100%);max-height:90vh;overflow:auto;background:#fff;border-radius:26px;box-shadow:0 30px 80px rgba(46,16,101,.4),0 0 0 1px rgba(255,255,255,.6) inset;position:relative;transform:translateY(18px) scale(.965);opacity:0;transition:transform .38s cubic-bezier(.16,1,.3,1),opacity .3s ease;scrollbar-width:thin}
        .lux-veil.open .lux-panel{transform:none;opacity:1}
        .lux-panel::before{content:"";position:absolute;top:0;left:0;right:0;height:5px;border-radius:26px 26px 0 0;background:linear-gradient(90deg,#4c1d95,#7c3aed 45%,#e11d48)}
        .lux-head{background:linear-gradient(135deg,#4c1d95 0%,#6d28d9 55%,#7c3aed 100%);color:#fff;padding:22px 22px 18px;display:flex;gap:14px;align-items:center;position:sticky;top:0;z-index:2;border-radius:22px 22px 0 0;overflow:hidden}
        .lux-head::after{content:"";position:absolute;right:-60px;top:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.1)}
        .lux-ico{width:52px;height:52px;border-radius:16px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.3);display:grid;place-items:center;flex:none;position:relative;z-index:1}
        .lux-ico .ic{width:26px;height:26px}
        .lux-head h3{font-size:18px;font-weight:800;letter-spacing:-.3px;position:relative;z-index:1;margin:0}
        .lux-head p{font-size:12.5px;opacity:.85;margin:3px 0 0;position:relative;z-index:1}
        .lux-x{margin-left:auto;width:38px;height:38px;border-radius:12px;display:grid;place-items:center;background:rgba(255,255,255,.14);color:#fff;flex:none;position:relative;z-index:1;transition:.25s}
        .lux-x:hover{background:rgba(255,255,255,.26);transform:rotate(90deg)}
        .lux-body{padding:20px 22px}
        .lux-sec{margin-bottom:18px}
        .lux-sec-t{font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#6d28d9;margin-bottom:10px;display:flex;align-items:center;gap:8px}
        .lux-sec-t::after{content:"";flex:1;height:1px;background:linear-gradient(90deg,#ede9fe,transparent)}
        .lux-item{display:flex;gap:12px;align-items:center;padding:10px;border:1px solid var(--line);border-radius:16px;margin-bottom:8px;background:#fff;transition:.2s}
        .lux-item:hover{border-color:#ddd6fe;background:#faf9ff}
        .lux-item .th{width:52px;height:52px;border-radius:12px;background:#f5f3ff center/cover no-repeat;border:1px solid var(--line);flex:none}
        .lux-item b{font-size:13px;display:block;line-height:1.35}
        .lux-item small{font-size:11.5px;color:var(--grey)}
        .lux-item .pr{margin-left:auto;font-weight:800;font-size:13.5px;color:var(--pink);white-space:nowrap}
        .lux-pills{display:grid;grid-template-columns:1fr 1fr;gap:10px}
        @media(max-width:520px){.lux-pills{grid-template-columns:1fr}}
        .lux-pick{border:1.5px solid var(--line);border-radius:16px;padding:12px 13px;cursor:pointer;display:flex;gap:10px;align-items:center;background:#fff;transition:.22s;text-align:left;width:100%}
        .lux-pick:hover{border-color:#a78bfa;transform:translateY(-1px);box-shadow:0 8px 18px rgba(109,40,217,.12)}
        .lux-pick.on{border-color:#7c3aed;background:linear-gradient(180deg,#f5f3ff,#fff);box-shadow:0 0 0 4px rgba(139,92,246,.14)}
        .lux-pick .dot{width:20px;height:20px;border-radius:50%;border:2px solid #d1d5db;display:grid;place-items:center;flex:none}
        .lux-pick.on .dot{border-color:#7c3aed}
        .lux-pick.on .dot::after{content:"";width:10px;height:10px;border-radius:50%;background:#7c3aed}
        .lux-pick b{font-size:13px;display:block}
        .lux-pick small{font-size:11.5px;color:var(--grey)}
        .lux-pick .tag{margin-left:auto;font-size:11px;font-weight:800;color:var(--pink);white-space:nowrap}
        .lux-addr{background:linear-gradient(180deg,#f5f3ff,#fff);border:1px solid #ddd6fe;border-radius:16px;padding:13px 15px;font-size:13px;color:#374151;display:flex;gap:10px;line-height:1.6}
        .lux-addr .ic{color:#7c3aed;flex:none;margin-top:2px}
        .lux-tot{background:#0f0b2a;color:#fff;border-radius:18px;padding:15px 17px;position:relative;overflow:hidden}
        .lux-tot::before{content:"";position:absolute;inset:0;background:radial-gradient(300px 140px at 90% 0%,rgba(139,92,246,.5),transparent 60%),radial-gradient(240px 120px at 0% 100%,rgba(225,29,72,.35),transparent 60%)}
        .lux-tot>*{position:relative}
        .lux-tot .r{display:flex;justify-content:space-between;font-size:13px;padding:3px 0;opacity:.9}
        .lux-tot .grand{display:flex;justify-content:space-between;align-items:baseline;border-top:1px dashed rgba(255,255,255,.25);margin-top:9px;padding-top:11px}
        .lux-tot .grand b{font-size:24px;font-weight:800;letter-spacing:-.5px}
        .lux-tot .free{display:inline-flex;align-items:center;gap:6px;background:#10b981;color:#fff;font-size:11px;font-weight:800;padding:4px 10px;border-radius:999px;margin-top:8px}
        .lux-foot{display:flex;gap:10px;padding:0 22px 22px}
        .lux-btn-cancel{flex:1;border:1.5px solid var(--line);border-radius:999px;padding:14px;font-weight:700;font-size:14px;color:#374151;background:#fff;transition:.2s}
        .lux-btn-cancel:hover{border-color:#c4b5fd;background:#f5f3ff}
        .lux-btn-ok{flex:1.4;border:0;border-radius:999px;padding:14px 18px;font-weight:800;font-size:14.5px;color:#fff;background:linear-gradient(135deg,#6d28d9,#8b5cf6);display:inline-flex;align-items:center;justify-content:center;gap:9px;position:relative;overflow:hidden;box-shadow:0 12px 28px rgba(109,40,217,.4);transition:.2s}
        .lux-btn-ok:hover{transform:translateY(-2px);box-shadow:0 18px 36px rgba(109,40,217,.45)}
        .lux-btn-ok::after{content:"";position:absolute;top:0;bottom:0;width:45%;left:-60%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.5),transparent);transform:skewX(-18deg)}
        .lux-btn-ok:hover::after{animation:luxshine .8s ease}
        @keyframes luxshine{to{left:130%}}
        .lux-bar{height:8px;background:#ede9fe;border-radius:999px;overflow:hidden;margin:8px 0}
        .lux-bar i{display:block;height:100%;background:linear-gradient(90deg,#7c3aed,#e11d48);border-radius:999px;transition:width .5s ease}
        /* remove modal */
        .lux-panel.sm{width:min(430px,100%)}
        .lux-warn{width:64px;height:64px;border-radius:20px;margin:0 auto;background:linear-gradient(135deg,#fff1f2,#ffe4e6);border:1px solid #fecdd3;display:grid;place-items:center;color:#e11d48}
        .lux-warn .ic{width:30px;height:30px}
        .lux-prod{display:flex;gap:12px;align-items:center;background:#faf9ff;border:1px solid #ede9fe;border-radius:16px;padding:11px 13px;margin:14px 0}
        .lux-prod .th{width:56px;height:56px;border-radius:12px;background:#eee center/cover;border:1px solid var(--line);flex:none}
    </style>

    @if (!$panier || $itemsCount <= 0)
        <div class="empty"><svg class="ic">
                <use href="#i-bag" />
            </svg>
            <h3>Votre panier est vide</h3>
            <p>Parcourez le catalogue et ajoutez vos articles préférés.</p>
            <div class="pdp-actions" style="justify-content:center;margin-top:20px">
                <a class="btn-solid" href="{{ route('products') }}">Voir les produits</a>
                <a class="btn-line" href="{{ route('home') }}">Retour à l'accueil</a>
            </div>
        </div>
    @else
        @php
            $franco = 25000;
            $restant = max(0, $franco - $total);
            $pct = min(100, round($total / $franco * 100));
        @endphp
        <div class="steps">
            <div class="on">1. Panier</div>
            <div>2. Livraison</div>
            <div>3. Paiement</div>
            <div>4. Confirmation</div>
        </div>
        <div class="cart-grid">
            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-bag" />
                    </svg> Articles ({{ $itemsCount }})</h2>
                @foreach ($panier->products as $product)
                    @php
                        $img = $product->getPhoto()
                            ? $product->getPhoto()->getImageUrl(200, 200)
                            : asset('assets/img/products/1.png');
                    @endphp
                    <div class="cline">
                        <a class="th" style="background-image:url('{{ $img }}')"
                            href="{{ route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]) }}"
                            aria-label="{{ $product->name }}"></a>
                        <div>
                            <h3><a
                                    href="{{ route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]) }}">{{ Str::limit($product->name, 80) }}</a>
                            </h3>
                            @php
                                $cartOpts = null;
                                try {
                                    $cartOpts = $product->pivot->options ?? null;
                                    if (is_string($cartOpts)) {
                                        $cartOpts = json_decode($cartOpts, true);
                                    }
                                } catch (\Throwable $e) {
                                    $cartOpts = null;
                                }
                            @endphp
                            @if (!empty($cartOpts) && is_array($cartOpts))
                                <div class="opts" style="display:flex;gap:6px;flex-wrap:wrap;margin-top:6px">
                                    @foreach ($cartOpts as $ok => $ov)
                                        <span
                                            style="font-size:11.5px;font-weight:600;background:var(--lav-1);border:1px solid #ddd6fe;color:var(--violet-800);border-radius:999px;padding:3px 10px">{{ $ok }}
                                            : {{ $ov }}</span>
                                    @endforeach
                                </div>
                            @else
                                <div class="opts">{{ $product->couleur ?? '' }}</div>
                            @endif
                            <div class="unit">Prix unitaire : {{ $this->formatFcfa($product->pivot->prix_unitaire ?? 0) }}
                            </div>
                            <div class="stepper">
                                <button type="button" wire:click="decreaseQuantity({{ $product->id }})"
                                    aria-label="Diminuer"><svg class="ic">
                                        <use href="#i-b2-minus" />
                                    </svg></button>
                                <input type="number" min="1" value="{{ $product->pivot?->quantite ?? 1 }}"
                                    disabled aria-label="Quantité">
                                <button type="button" wire:click="increaseQuantity({{ $product->id }})"
                                    aria-label="Augmenter"><svg class="ic">
                                        <use href="#i-b2-plus" />
                                    </svg></button>
                            </div>
                            <button type="button" class="rm"
                                wire:click="askRemove({{ $product->id }})"><svg class="ic"
                                    style="width:15px;height:15px">
                                    <use href="#i-b2-trash" />
                                </svg> Retirer</button>
                        </div>
                        <div class="lt">{{ $this->formatFcfa($product->pivot?->total_ligne ?? 0) }}</div>
                    </div>
                @endforeach
            </div>

            <div class="panel summary">
                <h2><svg class="ic">
                        <use href="#i-b2-tag" />
                    </svg> Récapitulatif</h2>
                <div class="bar"><i style="width:{{ $pct }}%"></i></div>
                <p style="font-size:12.5px;color:var(--mut);margin-bottom:6px">
                    @if ($restant > 0)
                        Plus que <b style="color:var(--violet-700)">{{ $this->formatFcfa($restant) }}</b> pour la
                        livraison offerte
                    @else
                        🎉 Livraison offerte sur cette commande !
                    @endif
                </p>
                <div class="srow"><span>Sous-total ({{ $itemsCount }}
                        article{{ $itemsCount > 1 ? 's' : '' }})</span><b>{{ $this->formatFcfa($total) }}</b></div>
                @if ($discount > 0)
                    <div class="srow ok"><span>Remise ({{ $voucherCode }})</span><b>−
                            {{ $this->formatFcfa($discount) }}</b></div>
                @endif
                <div class="srow"><span>Livraison</span><b>{{ $this->formatFcfa($shippingCost) }}</b></div>
                @if ($deliveryMethods && count($deliveryMethods))
                    <div class="field" style="margin-top:10px">
                        <label for="liv">Mode de livraison</label>
                        <select class="ctrl" id="liv" wire:model.live="deliveryMethodId">
                            @foreach ($deliveryMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->method_name }} —
                                    {{ $this->formatFcfa($method->price) }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="stotal"><span>Total</span><b>{{ $this->formatFcfa($this->getFinalTotal()) }}</b></div>
                <div class="promo-row">
                    <input class="ctrl" wire:model="voucherCode" placeholder="Code promo" aria-label="Code promo">
                    <button class="btn-ghost-sm" type="button" wire:click="applyVoucher"
                        wire:loading.attr="disabled">Appliquer</button>
                </div>
                <div class="promo-msg {{ $voucherError ? 'ko' : ($discount > 0 ? 'ok' : '') }}">
                    {{ $voucherError ?: ($discount > 0 ? 'Code ' . $voucherCode . ' appliqué.' : '') }}</div>
                <div class="pdp-actions" style="margin-top:16px">
                    <button class="btn-solid" style="flex:1" type="button" wire:click="openCheckoutModal"
                        wire:loading.attr="disabled"><svg class="ic">
                            <use href="#i-b2-lock" />
                        </svg> Commander · {{ $this->formatFcfa($this->getFinalTotal()) }}</button>
                </div>
                <div class="pdp-actions" style="margin-top:10px">
                    <a class="btn-line" style="flex:1" href="{{ route('products') }}">Continuer mes achats</a>
                </div>
                <p style="font-size:11.5px;color:#9ca3af;text-align:center;margin-top:12px">🔒 Paiement 100% sécurisé · Mobile Money & espèces</p>
            </div>
        </div>

        {{-- ===== MODAL COMMANDER ULTRA-PREMIUM ===== --}}
        <div class="lux-veil {{ $showConfirmModal ? 'open' : '' }}" @if($showConfirmModal) wire:click="closeCheckoutModal" @endif>
            <div class="lux-panel" role="dialog" aria-modal="true" aria-label="Confirmer la commande" wire:click.stop>
                <div class="lux-head">
                    <span class="lux-ico"><svg class="ic"><use href="#i-bag" /></svg></span>
                    <div>
                        <h3>Finaliser ma commande</h3>
                        <p>{{ $itemsCount }} article{{ $itemsCount > 1 ? 's' : '' }} · {{ $this->formatFcfa($this->getFinalTotal()) }} · vérification express</p>
                    </div>
                    <button class="lux-x" type="button" wire:click="closeCheckoutModal" aria-label="Fermer"><svg class="ic"><use href="#i-close" /></svg></button>
                </div>
                <div class="lux-body">
                    <div class="lux-sec">
                        <div class="lux-sec-t">🧾 Vos articles</div>
                        @foreach ($panier->products as $product)
                            @php
                                $im = $product->getPhoto() ? $product->getPhoto()->getImageUrl(200,200) : asset('assets/img/products/1.png');
                                $mOpts = $product->pivot->options ?? null;
                                if (is_string($mOpts)) { try { $mOpts = json_decode($mOpts, true); } catch (\Throwable $e) { $mOpts = null; } }
                                $mQty = (int) ($product->pivot?->quantite ?? 1);
                                $mPu = (float) ($product->pivot->prix_unitaire ?? 0);
                                $mTotal = (float) ($product->pivot?->total_ligne ?? $mQty * $mPu);
                            @endphp
                            <div class="lux-item">
                                <span class="th" style="background-image:url('{{ $im }}')"></span>
                                <div style="min-width:0;flex:1">
                                    <b>{{ Str::limit($product->name, 45) }}</b>
                                    @if (!empty($mOpts) && is_array($mOpts))
                                        <div style="display:flex;gap:5px;flex-wrap:wrap;margin:4px 0">
                                            @foreach ($mOpts as $ok => $ov)
                                                <small style="font-size:10.5px;font-weight:700;background:var(--lav-1);border:1px solid #ddd6fe;color:var(--violet-800);border-radius:999px;padding:2px 8px">{{ $ok }} : {{ $ov }}</small>
                                            @endforeach
                                        </div>
                                    @endif
                                    <small>Quantité : <b>×{{ $mQty }}</b> · Prix unitaire : <b>{{ $this->formatFcfa($mPu) }}</b></small>
                                </div>
                                <span class="pr">{{ $this->formatFcfa($mTotal) }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($deliveryMethods && count($deliveryMethods))
                        <div class="lux-sec">
                            <div class="lux-sec-t">🚚 Livraison</div>
                            <div class="lux-pills">
                                @foreach ($deliveryMethods as $method)
                                    <button type="button" class="lux-pick {{ (int)$deliveryMethodId === (int)$method->id ? 'on' : '' }}" wire:click="$set('deliveryMethodId', {{ $method->id }})">
                                        <span class="dot"></span>
                                        <span><b>{{ $method->method_name }}</b><small>{{ $method->delivery_time_min ?? '' }}{{ ($method->delivery_time_min ?? 0) || ($method->delivery_time_max ?? 0) ? ' · ' : '' }}{{ $this->formatFcfa($method->price) }}</small></span>
                                        <span class="tag">{{ $this->formatFcfa($method->price) }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($paymentMethods && count($paymentMethods))
                        <div class="lux-sec">
                            <div class="lux-sec-t">💳 Paiement</div>
                            <div class="lux-pills">
                                @foreach ($paymentMethods as $pm)
                                    @php $pn = strtolower($pm->method_name ?? $pm->name ?? ''); $emoji = str_contains($pn,'wave') ? '🌊' : (str_contains($pn,'orange') ? '🟧' : (str_contains($pn,'moov')||str_contains($pn,'malitel') ? '🟦' : (str_contains($pn,'espece')||str_contains($pn,'livraison') ? '💵' : '💳'))); @endphp
                                    <button type="button" class="lux-pick {{ (int)$paymentMethodId === (int)$pm->id ? 'on' : '' }}" wire:click="$set('paymentMethodId', {{ $pm->id }})">
                                        <span class="dot"></span>
                                        <span><b>{{ $emoji }} {{ $pm->method_name ?? $pm->name }}</b><small>{{ $pm->description ?? 'Paiement sécurisé' }}</small></span>
                                    </button>
                                @endforeach
                            </div>
                            @php $pmSel = $paymentMethods->firstWhere('id', (int) $paymentMethodId); @endphp
                            @if ($pmSel && ($pmSel->instructions || $pmSel->account_number))
                                <div class="lux-addr" style="margin-top:12px"><svg class="ic"><use href="#i-b2-info" /></svg>
                                    <span><b>{{ $pmSel->method_name ?? $pmSel->name }}</b>
                                        @if ($pmSel->account_number)
                                            <br>Compte : <b>{{ $pmSel->account_number }}</b>
                                        @endif
                                        @if ($pmSel->instructions)
                                            <br>{{ $pmSel->instructions }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="lux-sec">
                        <div class="lux-sec-t">📍 Adresse de livraison</div>
                        <div class="lux-addr"><svg class="ic"><use href="#i-truck" /></svg><span><b>{{ auth()->user()->name ?? '' }}</b><br>{{ auth()->user()->adresse ?? 'Ajoutez votre adresse dans votre profil' }} · {{ auth()->user()->telephone ?? '' }}</span></div>
                    </div>

                    <div class="lux-sec" style="margin-bottom:6px">
                        <div class="lux-tot">
                            <div class="r"><span>Sous-total</span><b>{{ $this->formatFcfa($total) }}</b></div>
                            @if($discount > 0)<div class="r" style="color:#6ee7b7"><span>Remise {{ $voucherCode }}</span><b>−{{ $this->formatFcfa($discount) }}</b></div>@endif
                            <div class="r"><span>Livraison</span><b>{{ $shippingCost > 0 ? $this->formatFcfa($shippingCost) : 'Offerte' }}</b></div>
                            <div class="lux-bar"><i style="width:{{ $pct }}%"></i></div>
                            @if($restant > 0)<div style="font-size:11.5px;opacity:.85">Plus que {{ $this->formatFcfa($restant) }} pour la livraison offerte</div>@else<span class="free">🎉 Livraison offerte débloquée</span>@endif
                            <div class="grand"><span style="font-size:12px;letter-spacing:.08em;font-weight:700">TOTAL À PAYER</span><b>{{ $this->formatFcfa($this->getFinalTotal()) }}</b></div>
                        </div>
                    </div>
                </div>
                <div class="lux-foot">
                    <button class="lux-btn-cancel" type="button" wire:click="closeCheckoutModal">Continuer mes achats</button>
                    <button class="lux-btn-ok" type="button" wire:click="confirmCheckout" wire:loading.attr="disabled">
                        <svg class="ic" wire:loading.remove wire:target="confirmCheckout"><use href="#i-b2-lock" /></svg>
                        <span wire:loading.remove wire:target="confirmCheckout">Confirmer · {{ $this->formatFcfa($this->getFinalTotal()) }}</span>
                        <span wire:loading wire:target="confirmCheckout">Traitement…</span>
                    </button>
                </div>
                @if (!empty($checkoutError))
                    <div style="padding:0 22px 22px">
                        <div class="tagline-band"
                            style="background:#ffe4e6;border-color:#fecdd3;color:#be123c;margin:0">
                            <svg class="ic">
                                <use href="#i-b2-alert" />
                            </svg>
                            <span>{{ $checkoutError }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ===== MODAL RETIRER ULTRA-PREMIUM ===== --}}
        <div class="lux-veil {{ $showRemoveModal ? 'open' : '' }}" @if($showRemoveModal) wire:click="closeRemoveModal" @endif>
            <div class="lux-panel sm" role="dialog" aria-modal="true" aria-label="Retirer l'article" wire:click.stop>
                <div class="lux-body" style="text-align:center;padding-top:26px">
                    <div class="lux-warn"><svg class="ic"><use href="#i-b2-trash" /></svg></div>
                    <h3 style="font-size:17px;font-weight:800;margin:14px 0 6px">Retirer cet article ?</h3>
                    <p style="font-size:13px;color:#6b7280">Cette action retirera l'article de votre panier.</p>
                    <div class="lux-prod">
                        <span class="th" style="background-image:url('{{ $productToRemoveImg }}')"></span>
                        <b style="font-size:13px;text-align:left">{{ Str::limit($productToRemoveName, 60) }}</b>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:6px">
                        <button class="lux-btn-cancel" type="button" wire:click="closeRemoveModal">Garder</button>
                        <button class="lux-btn-ok" type="button" style="background:linear-gradient(135deg,#e11d48,#f43f5e);box-shadow:0 12px 28px rgba(225,29,72,.35)" wire:click="confirmRemove">Oui, retirer</button>
                    </div>
                </div>
            </div>
        </div>

        @if($showConfirmModal || $showRemoveModal)
        <script>
            (function(){
                document.body.style.overflow = 'hidden';
                if(!window._luxEsc){
                    window._luxEsc = true;
                    document.addEventListener('keydown', function(e){
                        if(e.key === 'Escape'){
                            var el = document.querySelector('.lux-veil.open .lux-x, .lux-veil.open .lux-btn-cancel');
                            if(el) el.click();
                        }
                    });
                }
            })();
        </script>
        @endif
    @endif
</div>

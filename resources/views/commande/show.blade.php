@extends('base')

@section('title', 'Suivi de commande')

@section('content')
    @include('section-begin')

    @php
        use App\Support\OrderStatus;
        $norm = OrderStatus::normalize($commande->statut);
        $isCancelled = $norm === OrderStatus::ANNULE;
        $etape = in_array($norm, ['en_attente', 'paiement_declare']) ? 1 : ($norm === 'payee' ? 2 : ($norm === 'expedie' ? 3 : ($norm === 'livre' ? 4 : 1)));
        $etapes = [
            ['Confirmée', 'Commande reçue et enregistrée'],
            ['Payée', 'Paiement confirmé'],
            ['Expédiée', 'Remis au livreur'],
            ['Livrée', 'Colis remis au client'],
        ];
        $stClass = ['en_attente' => 'conf', 'paiement_declare' => 'conf', 'payee' => 'prep', 'expedie' => 'exp', 'livre' => 'ok', 'annule' => 'ko'];
        $adr = $commande->adresse_livraison ?? [];
        $num = $commande->numero_commande ?? 'CMD-' . $commande->id;
    @endphp

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('dashboard') }}">Mon espace client</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Suivi de commande</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Suivi de commande</h1>
            <p>Confirmée → en préparation → expédiée → livrée : voyez où en est votre colis.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            @if ($isCancelled)
                <div class="panel">
                    <div class="tagline-band" style="background:#ffe4e6;border-color:#fecdd3;color:#be123c;margin:0">
                        <svg class="ic">
                            <use href="#i-b2-alert" />
                        </svg>
                        Cette commande ({{ $num }}) a été annulée.
                    </div>
                </div>
            @else
                <div class="panel">
                    <div class="ocmd-top" style="margin-bottom:6px">
                        <div>
                            <h2 style="margin:0"><svg class="ic">
                                    <use href="#i-bag" />
                                </svg> Commande {{ $num }}</h2>
                            <span
                                class="muted-sm">{{ $commande->date_commande?->format('d/m/Y') ?? $commande->created_at?->format('d/m/Y') }}</span>
                        </div>
                        <span class="st {{ $stClass[$norm] ?? 'conf' }}">{{ $commande->status_label }}</span>
                    </div>
                    <div class="timeline">
                        @foreach ($etapes as $i => $e)
                            @php $etat = $i + 1 < $etape ? 'fait' : ($i + 1 == $etape ? 'actuel' : 'a-venir'); @endphp
                            <div class="tl-step {{ $etat }}">
                                <span class="tl-dot">
                                    @if ($i + 1 <= $etape)
                                        <svg class="ic">
                                            <use href="#i-b2-check" />
                                        </svg>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </span>
                                <div class="tl-txt"><b>{{ $e[0] }}</b><small>{{ $e[1] }}</small></div>
                                @if ($i < count($etapes) - 1)
                                    <span class="tl-bar"></span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="dash-grid" style="margin-top:16px;margin-bottom:0">
                        <div>
                            <h3 class="h3-mini">Articles</h3>
                            <table class="spec">
                                <tbody>
                                    @foreach ($commande->items as $item)
                                        @php
                                            $itemOpts = $item->options ?? null;
                                            if (is_string($itemOpts)) {
                                                $itemOpts = json_decode($itemOpts, true);
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ optional($item->produit)->name ?? 'Produit' }}<br><small
                                                    class="muted-sm">×{{ $item->quantite }} ·
                                                    {{ number_format($item->prix_unitaire, 0, ',', ' ') }}
                                                    FCFA / article</small>
                                                @if (!empty($itemOpts) && is_array($itemOpts))
                                                    <br>
                                                    @foreach ($itemOpts as $ok => $ov)
                                                        <small
                                                            style="display:inline-block;font-size:11px;font-weight:600;background:var(--lav-1);border:1px solid #ddd6fe;color:var(--violet-800);border-radius:999px;padding:2px 9px;margin:3px 4px 0 0">{{ $ok }}
                                                            : {{ $ov }}</small>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td style="text-align:right">
                                                <b>{{ number_format($item->total ?? $item->prix_unitaire * $item->quantite, 0, ',', ' ') }}
                                                    FCFA</b>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>Sous-total</td>
                                        <td style="text-align:right">
                                            {{ number_format($commande->sous_total, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    @if (($commande->remise ?? 0) > 0)
                                        <tr>
                                            <td>Remise</td>
                                            <td style="text-align:right">−
                                                {{ number_format($commande->remise, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>Livraison</td>
                                        <td style="text-align:right">
                                            @if (($commande->frais_livraison ?? 0) > 0)
                                                {{ number_format($commande->frais_livraison, 0, ',', ' ') }} FCFA
                                            @else
                                                Offerte
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Total</b></td>
                                        <td style="text-align:right"><b
                                                style="color:var(--pink)">{{ number_format($commande->total, 0, ',', ' ') }}
                                                FCFA</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <h3 class="h3-mini">Livraison</h3>
                            <table class="spec">
                                <tbody>
                                    <tr>
                                        <td>Destinataire</td>
                                        <td>{{ $adr['nom'] ?? auth()->user()->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Adresse</td>
                                        <td>{{ $adr['adresse'] ?? $adr['det'] ?? '—' }}{{ isset($adr['ville']) ? ', ' . $adr['ville'] : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Téléphone</td>
                                        <td>{{ $adr['tel'] ?? $adr['telephone'] ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Délai estimé</td>
                                        <td>{{ isset($adr['ville']) && mb_stripos($adr['ville'], 'bamako') !== false ? '24 h' : '48 à 72 h' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            @php $payMethod = $commande->paiement; @endphp
                            @if ($payMethod && ($payMethod->instructions || $payMethod->account_number))
                                <h3 class="h3-mini" style="margin-top:16px">Paiement :
                                    {{ $payMethod->method_name ?? $payMethod->name }}</h3>
                                <div class="tagline-band" style="margin:8px 0 0">
                                    <svg class="ic">
                                        <use href="#i-b2-info" />
                                    </svg>
                                    <span>
                                        @if ($payMethod->account_number)
                                            Compte : <b>{{ $payMethod->account_number }}</b><br>
                                        @endif
                                        {{ $payMethod->instructions ?? $payMethod->description }}
                                    </span>
                                </div>
                            @endif
                            <div class="pdp-actions" style="margin-top:14px">
                                <a class="btn-line" href="{{ route('commande.pdf', $commande->id) }}">Facture PDF</a>
                                <a class="btn-ghost-sm" href="{{ route('dashboard') }}">Mes commandes</a>
                            </div>
                        </div>
                    </div>
                    {{-- ===== Paiement : l'endroit où payer une commande en attente ===== --}}
                    @if ($norm === 'en_attente')
                        <div class="panel" style="margin-top:16px;border-color:#fcd34d">
                            <h2><svg class="ic" style="color:#d97706">
                                    <use href="#i-card" />
                                </svg> Payer ma commande · <b
                                    style="color:var(--pink)">{{ number_format($commande->total, 0, ',', ' ') }}
                                    FCFA</b></h2>
                            <p class="muted-sm" style="margin-bottom:14px">Payez via Mobile Money puis envoyez la
                                preuve : la commande passe en vérification.</p>
                            <form method="POST" action="{{ route('paiement.mobile') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $commande->id }}">
                                <div class="field">
                                    <label>Opérateur</label>
                                    @php
                                        $payName = strtolower(($commande->paiement->method_name ?? '') . ' ' . ($commande->paiement->provider_name ?? ''));
                                        $payCurrent = str_contains($payName, 'orange') ? 'orange' : (str_contains($payName, 'moov') ? 'moov' : (str_contains($payName, 'wave') ? 'wave' : (str_contains($payName, 'mtn') ? 'mtn' : old('provider'))));
                                    @endphp
                                    <div class="pay-pills">
                                        @foreach (['orange' => '🟧 Orange Money', 'moov' => '🟦 Moov Money', 'wave' => '🌊 Wave', 'mtn' => '🟨 MTN MoMo'] as $pv => $pl)
                                            <label class="opt-pill" style="cursor:pointer">
                                                <input type="radio" name="provider" value="{{ $pv }}"
                                                    {{ $payCurrent === $pv ? 'checked' : '' }} required
                                                    style="accent-color:var(--violet-600)"> {{ $pl }}
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('provider') <span class="avis-err">{{ $message }}</span> @enderror
                                </div>
                                @php
                                    $payInfos = [];
                                    foreach (['orange', 'moov', 'wave', 'mtn'] as $pk) {
                                        $pm = \App\Models\Paiements::where('is_active', true)
                                            ->where(function ($q) use ($pk) {
                                                $q->where('method_name', 'like', '%' . $pk . '%')->orWhere('provider_name', 'like', '%' . $pk . '%');
                                            })->orderBy('id')->first();
                                        if ($pm) {
                                            $payInfos[$pk] = [
                                                'nom' => $pm->method_name,
                                                'compte' => $pm->account_number,
                                                'texte' => $pm->instructions ?: $pm->description,
                                            ];
                                        }
                                    }
                                @endphp
                                <div class="tagline-band" id="payInfos" style="margin:0 0 14px">
                                    <svg class="ic">
                                        <use href="#i-b2-info" />
                                    </svg>
                                    <span id="payInfosTxt"></span>
                                </div>
                                <script>
                                    (function() {
                                        var infos = @json($payInfos);
                                        var box = document.getElementById('payInfos');
                                        var txt = document.getElementById('payInfosTxt');

                                        function ech(s) {
                                            return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g,
                                                '&lt;').replace(/>/g, '&gt;');
                                        }

                                        function maj() {
                                            var sel = document.querySelector(
                                                'input[name="provider"]:checked');
                                            var key = sel ? sel.value : null;
                                            var info = key && infos[key] ? infos[key] : null;
                                            if (!info || (!info.compte && !info.texte)) {
                                                box.style.display = 'none';
                                                return;
                                            }
                                            box.style.display = '';
                                            var html = '<b>' + ech(info.nom) + '</b>';
                                            if (info.compte) html += '<br>Compte : <b>' + ech(info
                                                .compte) + '</b>';
                                            if (info.texte) html += '<br>' + ech(info.texte);
                                            txt.innerHTML = html;
                                        }
                                        document.querySelectorAll('input[name="provider"]').forEach(function(r) {
                                            r.addEventListener('change', maj);
                                        });
                                        maj();
                                    })();
                                </script>
                                <div class="dash-grid" style="margin-bottom:0">
                                    <div class="field" style="margin-bottom:0">
                                        <label for="payPhone">Numéro ayant payé (optionnel)</label>
                                        <input id="payPhone" class="ctrl" name="phone" type="tel"
                                            value="{{ old('phone', auth()->user()->telephone ?? '') }}"
                                            placeholder="Ex : 70 00 00 00" style="border-radius:12px">
                                        @error('phone') <span class="avis-err">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="field" style="margin-bottom:0">
                                        <label for="payPhotos">Capture du paiement (photo) *</label>
                                        <input id="payPhotos" class="ctrl" name="photos[]" type="file"
                                            accept="image/*" multiple required style="border-radius:12px">
                                        @error('photos') <span class="avis-err">{{ $message }}</span> @enderror
                                        @error('photos.*') <span class="avis-err">{{ $message }}</span> @enderror
                                        <span class="muted-sm">Obligatoire : sans capture, la commande ne peut pas
                                            être confirmée.</span>
                                    </div>
                                </div>
                                <div class="pdp-actions" style="margin-top:16px">
                                    <button class="btn-solid" type="submit"><svg class="ic">
                                            <use href="#i-b2-check" />
                                        </svg> J'ai payé · envoyer la preuve</button>
                                </div>
                            </form>
                        </div>
                    @elseif($norm === 'paiement_declare')
                        <div class="tagline-band" style="margin-bottom:0">
                            <svg class="ic">
                                <use href="#i-b2-info" />
                            </svg>
                            <span><b>Preuve envoyée.</b> Votre paiement de
                                <b>{{ number_format($commande->total, 0, ',', ' ') }} FCFA</b> est en cours de
                                vérification par notre équipe.</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>

    @include('partials.footer')
@endsection

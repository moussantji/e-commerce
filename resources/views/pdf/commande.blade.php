<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Facture {{ $commande->numero_commande ?? $commande->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #111827;
            background: #ffffff;
            font-size: 10px;
        }

        @page {
            margin: 10mm 12mm;
            size: A4;
        }

        /* ===== Bandeau supérieur ===== */
        .hero {
            background: #4c1d95;
            color: #ffffff;
            border-radius: 10px;
            padding: 18px 22px;
            margin-bottom: 14px;
        }

        .hero h1 {
            font-size: 22px;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .hero .sub {
            font-size: 10px;
            color: #ddd6fe;
            margin: 2px 0 0;
        }

        .hero .meta {
            text-align: right;
            font-size: 10px;
        }

        .hero .meta .num {
            font-size: 14px;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            margin-top: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .badge-paid {
            background: #16a34a;
            color: #ffffff;
        }

        .badge-wait {
            background: #f59e0b;
            color: #ffffff;
        }

        /* ===== Blocs ===== */
        h2 {
            font-size: 11px;
            margin: 14px 0 6px;
            color: #4c1d95;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-bottom: 2px solid #ede9fe;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .info td {
            vertical-align: top;
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            width: 50%;
        }

        .info .k {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .info .v {
            font-size: 11px;
            font-weight: bold;
            margin: 2px 0 6px;
        }

        .info .v small {
            font-weight: normal;
            color: #6b7280;
        }

        /* ===== Produits ===== */
        thead th {
            background: #4c1d95;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        thead th.r,
        tbody td.r {
            text-align: right;
        }

        thead th.c,
        tbody td.c {
            text-align: center;
        }

        tbody td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px 10px;
            vertical-align: top;
        }

        tbody tr.alt td {
            background: #f5f3ff;
        }

        .opt {
            font-size: 8.5px;
            color: #6d28d9;
        }

        .ref {
            font-size: 8.5px;
            color: #9ca3af;
        }

        /* ===== Totaux ===== */
        .totals {
            width: 46%;
            margin: 10px 0 0 auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .totals td {
            padding: 7px 12px;
            border-bottom: 1px solid #f0f0f5;
        }

        .totals tr:last-child td {
            border-bottom: none;
        }

        .totals .lbl {
            color: #6b7280;
        }

        .totals .grand td {
            background: #4c1d95;
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
        }

        .totals .free {
            color: #059669;
            font-weight: bold;
        }

        /* ===== Pied ===== */
        .footer {
            margin-top: 16px;
            border-top: 2px solid #ede9fe;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }

        .footer b {
            color: #4c1d95;
        }

        div,
        p,
        table,
        tr,
        td,
        th {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    @php
        $isPaid = method_exists($commande, 'isPaid') ? $commande->isPaid() : false;
        $fmt = fn($m) => number_format((float) $m, 0, ',', ' ') . ' FCFA';
        $adr = $commande->adresse_livraison ?? [];
        $fac = $commande->adresse_facturation ?? [];
        $tel = data_get($adr, 'tel') ?? data_get($adr, 'telephone') ?? data_get($fac, 'tel') ?? data_get($fac, 'telephone') ?? optional($commande->user)->tel ?? '—';
    @endphp

    <!-- Bandeau -->
    <table class="hero" style="border-radius:10px;">
        <tr>
            <td>
                <h1>Boutique</h1>
                <p class="sub">Bamako, Mali · contact@boutique.ml · +223 82 01 95 83</p>
            </td>
            <td class="meta">
                <div class="num">Facture {{ $commande->numero_commande ?? 'CMD-' . $commande->id }}</div>
                <div>Émise le {{ optional($commande->date_commande)->format('d/m/Y H:i') ?? optional($commande->created_at)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</div>
                <div><span class="badge {{ $isPaid ? 'badge-paid' : 'badge-wait' }}">{{ $isPaid ? 'Payée' : $commande->status_label ?? $commande->statut }}</span></div>
            </td>
        </tr>
    </table>

    <!-- Client + livraison -->
    <h2>Client & livraison</h2>
    <table class="info">
        <tr>
            <td>
                <div class="k">Client</div>
                <div class="v">{{ optional($commande->user)->name ?? 'Client' }}</div>
                <div class="k">Email</div>
                <div class="v"><small>{{ optional($commande->user)->email ?? '—' }}</small></div>
                <div class="k">Téléphone</div>
                <div class="v">{{ $tel }}</div>
            </td>
            <td>
                <div class="k">Adresse de livraison</div>
                <div class="v"><small>
                        @if (is_array($adr) && !empty($adr))
                            {{ data_get($adr, 'adresse') }}<br>
                            {{ data_get($adr, 'ville') }}@if (data_get($adr, 'pays'))
                                , {{ data_get($adr, 'pays') }}
                            @endif
                        @elseif(is_string($adr) && $adr !== '')
                            {{ $adr }}
                        @else
                            —
                        @endif
                    </small></div>
                <div class="k">Mode de livraison</div>
                <div class="v"><small>{{ optional($commande->livraison)->method_name ?? '—' }}</small></div>
                <div class="k">Paiement</div>
                <div class="v"><small>{{ optional($commande->paiement)->method_name ?? '—' }}</small></div>
            </td>
        </tr>
    </table>

    <!-- Articles -->
    <h2>Articles ({{ $commande->items->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th class="c" style="width:7%">#</th>
                <th style="width:48%">Désignation</th>
                <th class="c" style="width:10%">Qté</th>
                <th class="r" style="width:17%">Prix unit.</th>
                <th class="r" style="width:18%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commande->items as $index => $item)
                @php
                    $itemOpts = $item->options ?? null;
                    if (is_string($itemOpts)) {
                        $itemOpts = json_decode($itemOpts, true);
                    }
                @endphp
                <tr class="{{ $index % 2 ? 'alt' : '' }}">
                    <td class="c">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ optional($item->produit)->name ?? 'Produit #' . $item->produit_id }}</strong><br>
                        <span class="ref">Réf : {{ optional($item->produit)->sku ?? 'REF-' . $item->produit_id }}</span>
                        @if (!empty($itemOpts) && is_array($itemOpts))
                            <br>
                            @foreach ($itemOpts as $ok => $ov)
                                <span class="opt">{{ $ok }} : {{ $ov }}</span>@if (!$loop->last), @endif
                            @endforeach
                        @endif
                    </td>
                    <td class="c">×{{ $item->quantite }}</td>
                    <td class="r">{{ $fmt($item->prix_unitaire) }}</td>
                    <td class="r"><strong>{{ $fmt($item->total ?? $item->prix_unitaire * $item->quantite) }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="c" colspan="5">Aucun article</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totaux -->
    <table class="totals">
        <tr>
            <td class="lbl">Sous-total</td>
            <td class="r">{{ $fmt($commande->sous_total ?? $commande->items->sum('total')) }}</td>
        </tr>
        <tr>
            <td class="lbl">Livraison</td>
            <td class="r">
                @if (($commande->frais_livraison ?? 0) > 0)
                    {{ $fmt($commande->frais_livraison) }}
                @else
                    <span class="free">Offerte</span>
                @endif
            </td>
        </tr>
        @if (($commande->remise ?? 0) > 0)
            <tr>
                <td class="lbl">Remise</td>
                <td class="r">−{{ $fmt($commande->remise) }}</td>
            </tr>
        @endif
        @if ($commande->promoCode)
            <tr>
                <td class="lbl">Code {{ $commande->promoCode->code }}</td>
                <td class="r">−{{ $fmt($commande->promo_discount ?? 0) }}</td>
            </tr>
        @endif
        <tr class="grand">
            <td>Total</td>
            <td class="r">{{ $fmt($commande->total) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p><b>Merci pour votre commande !</b> — Support 7j/7 : +223 82 01 95 83</p>
        <p>Prix en FCFA · TVA incluse · Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>

</html>

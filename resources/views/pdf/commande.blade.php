<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Reçu de commande</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #2a3440;
            background-color: #f5f7fa;
            font-size: 10px;
        }

        .page {
            max-width: 210mm;
            height: 280mm;
            margin: auto;
            padding: 6mm;
            background-color: #ffffff;
            border: 1px solid #d8dde5;
            border-radius: 4px;
            page-break-after: avoid;
        }

        .brand {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 3px solid #1f3b6b;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 4px;
            color: #1f3b6b;
        }

        .company {
            text-align: right;
            font-size: 9px;
        }

        h2 {
            font-size: 11px;
            margin: 8px 0 4px;
            color: #1f3b6b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #d8dde5;
            padding-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 9px;
        }

        thead th {
            background-color: #2c5aa0;
            color: #ffffff;
            padding: 6px 8px;
            border: none;
        }

        tbody tr:nth-child(odd) {
            background-color: #f9fafc;
        }

        tbody td {
            border-bottom: 1px solid #d8dde5;
            padding: 5px 8px;
        }

        .totals {
            width: 45%;
            margin: 6px 0 0 auto;
            padding: 8px;
            background: #f9fafc;
            border-radius: 4px;
            display: block;
        }

        .footer {
            margin-top: 10px;
            font-size: 9px;
            text-align: center;
            color: #586575;
            border-top: 1px solid #d8dde5;
            padding-top: 8px;
        }

        div,
        p,
        table,
        tr,
        td,
        th {
            page-break-after: avoid !important;
            page-break-inside: avoid !important;
        }

        body {
            orphans: 1;
            widows: 1;
            page-break-after: avoid;
        }

        @page {
            margin: 4mm;
            size: A4;
        }
    </style>
</head>

<body>
    <div class="page">

        <!-- Titre + infos + numéro de facture -->
        <div class="brand">
            <div>
                <h1>Phoenix Ecommerce</h1>
                <p>36 Greendown Road, California, USA</p>
                <p>support@phoenix.fr | +33 1 23 45 67 89</p>
            </div>
            <div class="company">
                <p><strong>Facture</strong> #{{ $commande->numero_commande ?? $commande->id }}</p>
                <p>Date : {{ optional($commande->date_commande)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}
                </p>
                <p>Statut : {{ $commande->statut ?? 'N/A' }}</p>
            </div>
        </div>

        <h2>Client</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-col">
                    <strong>Nom</strong><br>
                    {{ optional($commande->user)->name ?? 'Client inconnu' }}
                </div>
                <div class="info-col">
                    <strong>Email</strong><br>
                    {{ optional($commande->user)->email ?? '-' }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-col">
                    <strong>Téléphone</strong><br>
                    @php
                        $phone =
                            data_get($commande->adresse_livraison, 'telephone') ?:
                            data_get($commande->adresse_facturation, 'telephone') ?:
                            '-';
                    @endphp
                    {{ is_string($phone) ? $phone : '-' }}
                </div>
                <div class="info-col">
                    <strong>ID utilisateur</strong><br>
                    {{ $commande->user_id ?? '-' }}
                </div>
            </div>
        </div>

        <h2>Adresses</h2>
        <table>
            <tr>
                <th>Facturation</th>
                <th>Livraison</th>
            </tr>
            <tr>
                <td>
                    @php
                        $fact = $commande->adresse_facturation ?? [];
                    @endphp
                    {{ data_get($fact, 'nom', 'Client inconnu') }}<br>
                    {{ data_get($fact, 'adresse') }}<br>
                    {{ data_get($fact, 'ville') }}, {{ data_get($fact, 'pays') }}<br>
                    <strong>Téléphone : </strong>{{ data_get($fact, 'telephone', '-') }}
                </td>
                <td>
                    @php
                        $livraison = $commande->adresse_livraison ?? [];
                    @endphp
                    @if (is_array($livraison) && !empty($livraison))
                        {{ data_get($livraison, 'nom', 'Client inconnu') }}<br>
                        {{ data_get($livraison, 'adresse') }}<br>
                        {{ data_get($livraison, 'ville') }}, {{ data_get($livraison, 'pays') }}<br>
                        <strong>Téléphone : </strong>{{ data_get($livraison, 'telephone', '-') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>

        <h2>Produits</h2>
        <table>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Désignation</th>
                    <th class="text-right">Qté</th>
                    <th class="text-right">Prix unitaire</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commande->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ optional($item->produit)->name ?? 'Produit #' . $item->produit_id }}</td>
                        <td class="text-right">{{ $item->quantite }}</td>
                        <td class="text-right">{{ number_format($item->prix_unitaire, 2, ',', ' ') }} FCFA</td>
                        <td class="text-right">{{ number_format($item->total, 2, ',', ' ') }} FCFA</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="5">Aucun produit</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h2>Récapitulatif</h2>
        <div class="totals">
            <table>
                <tr>
                    <th>Sous-total</th>
                    <td>{{ number_format($commande->sous_total ?? ($commande->items->sum('total') ?? 0), 2, ',', ' ') }}
                        FCFA</td>
                </tr>
                <tr>
                    <th>Frais de livraison</th>
                    <td>{{ number_format($commande->frais_livraison ?? 0, 2, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <th>Remise</th>
                    <td>-{{ number_format($commande->remise ?? 0, 2, ',', ' ') }} FCFA</td>
                </tr>
                @if ($commande->promoCode)
                    <tr>
                        <th>Réduction promo</th>
                        <td>-{{ number_format($commande->promo_discount ?? 0, 2, ',', ' ') }} FCFA</td>
                    </tr>
                @endif
                <tr>
                    <th class="grand-total"><strong>Total</strong></th>
                    <td class="grand-total">
                        <strong>{{ number_format((float) ($commande->total ?? $commande->getTotalAttribute()), 2, ',', ' ') }}
                            FCFA</strong>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Merci pour votre commande.</p>
            <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
        </div>

    </div>
</body>

</html>

@extends('base')

@section('title', "Conditions d'utilisation")

@section('content')
<div class="container py-5" style="max-width: 900px;">
    <h1 class="mb-4">Conditions d'utilisation</h1>
    <p class="text-muted">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>

    <h4 class="mt-4">1. Objet</h4>
    <p>Les présentes conditions régissent l'utilisation de notre boutique en ligne et de
        l'application mobile associée. En créant un compte ou en passant commande, vous
        acceptez ces conditions.</p>

    <h4 class="mt-4">2. Compte</h4>
    <p>Vous êtes responsable de l'exactitude des informations de votre compte et de la
        confidentialité de vos identifiants. Toute activité effectuée depuis votre compte
        vous est imputable.</p>

    <h4 class="mt-4">3. Commandes et paiements</h4>
    <p>Les paiements s'effectuent via les moyens Mobile Money proposés (Orange Money, Moov
        Money, Wave). Après avoir suivi les instructions et effectué le paiement, vous
        déclarez le règlement dans l'application ; la commande est confirmée après
        vérification par le vendeur.</p>

    <h4 class="mt-4">4. Livraison</h4>
    <p>Les délais de livraison sont indicatifs. Une commande non réglée dans les délais
        peut être annulée automatiquement.</p>

    <h4 class="mt-4">5. Retours et remboursements</h4>
    <p>Les demandes de retour ou de remboursement sont traitées selon la politique en
        vigueur et l'état des articles retournés.</p>

    <h4 class="mt-4">6. Portefeuille</h4>
    <p>Le portefeuille permet de conserver un solde, de le recharger via Mobile Money
        (après confirmation du vendeur) et d'effectuer des transferts entre utilisateurs.</p>

    <h4 class="mt-4">7. Responsabilité</h4>
    <p>Nous nous efforçons d'assurer la disponibilité et l'exactitude des informations,
        sans garantie d'absence d'erreurs ou d'interruptions de service.</p>

    <h4 class="mt-4">8. Contact</h4>
    <p>Pour toute question relative à ces conditions, contactez notre support.</p>
</div>
@endsection

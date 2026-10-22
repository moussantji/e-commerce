@extends('base')

@section('title', "Conditions d'utilisation")
@section('meta_description', "Conditions d'utilisation de " . config('app.name') . " : commandes, paiements Mobile Money, livraison, retours et remboursements.")

@section('content')
<x-legal-page
    title="Conditions d'utilisation"
    eyebrow="Informations légales"
    icon="i-b2-check"
    description="Les règles applicables à vos commandes, à vos paiements et à l'utilisation de la boutique."
    updated="22 octobre 2026"
    updated-iso="2026-10-22"
    :sections="[
        'objet' => 'Objet',
        'compte' => 'Compte',
        'commandes-paiements' => 'Commandes et paiements',
        'livraison' => 'Livraison',
        'retours-remboursements' => 'Retours et remboursements',
        'portefeuille' => 'Portefeuille',
        'responsabilite' => 'Responsabilité',
        'contact' => 'Contact',
    ]"
    other-route="confidentialite"
    other-label="Politique de confidentialité"
>
    <section id="objet" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">01</span> Objet</h2>
        <p>Les présentes conditions régissent l'utilisation de notre boutique en ligne et de
            l'application mobile associée. En créant un compte ou en passant commande, vous
            acceptez ces conditions.</p>
    </section>

    <section id="compte" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">02</span> Compte</h2>
        <p>Vous êtes responsable de l'exactitude des informations de votre compte et de la
            confidentialité de vos identifiants. Toute activité effectuée depuis votre compte
            vous est imputable.</p>
    </section>

    <section id="commandes-paiements" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">03</span> Commandes et paiements</h2>
        <p>Les paiements s'effectuent via les moyens Mobile Money proposés (Orange Money, Moov
            Money, Wave). Après avoir suivi les instructions et effectué le paiement, vous
            déclarez le règlement dans l'application ; la commande est confirmée après
            vérification par le vendeur.</p>
    </section>

    <section id="livraison" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">04</span> Livraison</h2>
        <p>Les délais de livraison sont indicatifs. Une commande non réglée dans les délais
            peut être annulée automatiquement.</p>
    </section>

    <section id="retours-remboursements" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">05</span> Retours et remboursements</h2>
        <p>Les demandes de retour ou de remboursement sont traitées selon la politique en
            vigueur et l'état des articles retournés.</p>
    </section>

    <section id="portefeuille" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">06</span> Portefeuille</h2>
        <p>Le portefeuille permet de conserver un solde, de le recharger via Mobile Money
            (après confirmation du vendeur) et d'effectuer des transferts entre utilisateurs.</p>
    </section>

    <section id="responsabilite" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">07</span> Responsabilité</h2>
        <p>Nous nous efforçons d'assurer la disponibilité et l'exactitude des informations,
            sans garantie d'absence d'erreurs ou d'interruptions de service.</p>
    </section>

    <section id="contact" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">08</span> Contact</h2>
        <p>Pour toute question relative à ces conditions, contactez notre support.</p>
    </section>
</x-legal-page>
@endsection

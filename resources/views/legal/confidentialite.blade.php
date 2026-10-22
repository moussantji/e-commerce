@extends('base')

@section('title', 'Politique de confidentialité')
@section('meta_description', 'Politique de confidentialité de ' . config('app.name') . ' : données collectées, utilisation, partage, sécurité et droits des utilisateurs.')

@section('content')
<x-legal-page
    title="Politique de confidentialité"
    eyebrow="Vos données personnelles"
    icon="i-b2-lock"
    description="Comment nous collectons, utilisons et protégeons les informations liées à votre compte et à vos commandes."
    updated="22 octobre 2026"
    updated-iso="2026-10-22"
    :sections="[
        'donnees-collectees' => 'Données collectées',
        'utilisation' => 'Utilisation des données',
        'partage' => 'Partage',
        'localisation' => 'Localisation',
        'securite' => 'Sécurité',
        'droits' => 'Vos droits',
        'contact' => 'Contact',
    ]"
    other-route="conditions"
    other-label="Conditions d'utilisation"
>
    <section id="donnees-collectees" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">01</span> Données collectées</h2>
        <p>Nous collectons les informations que vous fournissez (nom, email, téléphone,
            adresses de livraison) ainsi que des données d'utilisation nécessaires au bon
            fonctionnement du service (commandes, paiements déclarés, favoris).</p>
    </section>

    <section id="utilisation" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">02</span> Utilisation des données</h2>
        <p>Vos données servent à traiter vos commandes et paiements, gérer votre portefeuille,
            vous envoyer des notifications (par email et dans l'application) et améliorer nos
            services.</p>
    </section>

    <section id="partage" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">03</span> Partage</h2>
        <p>Vos données ne sont pas vendues. Elles peuvent être partagées uniquement avec des
            prestataires strictement nécessaires (livraison, paiement) et dans le respect de
            la loi.</p>
    </section>

    <section id="localisation" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">04</span> Localisation</h2>
        <p>Une géolocalisation approximative peut être déduite de votre adresse IP à des fins
            statistiques et de sécurité.</p>
    </section>

    <section id="securite" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">05</span> Sécurité</h2>
        <p>Nous mettons en œuvre des mesures raisonnables pour protéger vos données. Vos mots
            de passe sont stockés de manière chiffrée.</p>
    </section>

    <section id="droits" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">06</span> Vos droits</h2>
        <p>Vous pouvez accéder à vos informations, les corriger depuis votre profil, ou
            demander la suppression de votre compte en contactant le support.</p>
    </section>

    <section id="contact" class="legal-section">
        <h2><span class="legal-num" aria-hidden="true">07</span> Contact</h2>
        <p>Pour toute question relative à vos données personnelles, contactez notre support.</p>
    </section>
</x-legal-page>
@endsection

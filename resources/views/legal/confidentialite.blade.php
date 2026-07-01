@extends('base')

@section('title', 'Politique de confidentialité')

@section('content')
<div class="container py-5" style="max-width: 900px;">
    <h1 class="mb-4">Politique de confidentialité</h1>
    <p class="text-muted">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>

    <h4 class="mt-4">1. Données collectées</h4>
    <p>Nous collectons les informations que vous fournissez (nom, email, téléphone,
        adresses de livraison) ainsi que des données d'utilisation nécessaires au bon
        fonctionnement du service (commandes, paiements déclarés, favoris).</p>

    <h4 class="mt-4">2. Utilisation des données</h4>
    <p>Vos données servent à traiter vos commandes et paiements, gérer votre portefeuille,
        vous envoyer des notifications (par email et dans l'application) et améliorer nos
        services.</p>

    <h4 class="mt-4">3. Partage</h4>
    <p>Vos données ne sont pas vendues. Elles peuvent être partagées uniquement avec des
        prestataires strictement nécessaires (livraison, paiement) et dans le respect de
        la loi.</p>

    <h4 class="mt-4">4. Localisation</h4>
    <p>Une géolocalisation approximative peut être déduite de votre adresse IP à des fins
        statistiques et de sécurité.</p>

    <h4 class="mt-4">5. Sécurité</h4>
    <p>Nous mettons en œuvre des mesures raisonnables pour protéger vos données. Vos mots
        de passe sont stockés de manière chiffrée.</p>

    <h4 class="mt-4">6. Vos droits</h4>
    <p>Vous pouvez accéder à vos informations, les corriger depuis votre profil, ou
        demander la suppression de votre compte en contactant le support.</p>

    <h4 class="mt-4">7. Contact</h4>
    <p>Pour toute question relative à vos données personnelles, contactez notre support.</p>
</div>
@endsection

@extends('admin.base')

@section('title', 'Conditions d'utilisation')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Conditions</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Conditions d'utilisation</h1>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel boutique-texte">
<h4>1. Acceptation des conditions</h4>
                        <p>En accédant et en utilisant ce site web, vous acceptez d'être lié par ces conditions d'utilisation, toutes les lois et réglementations applicables, et vous convenez que vous êtes responsable du respect des lois locales applicables.</p>
                        
                        <h4 class="mt-4">2. Utilisation de la licence</h4>
                        <p>L'autorisation est accordée de télécharger temporairement une copie des documents (informations ou logiciels) sur le site web pour un usage personnel et non commercial uniquement. C'est la concession d'une licence, pas un transfert de titre.</p>
                        
                        <h4 class="mt-4">3. Limitations</h4>
                        <p>Il vous est interdit de :</p>
                        <ul>
                            <li>Modifier ou copier les documents</li>
                            <li>Utiliser le matériel à des fins commerciales ou pour toute affichage public (commercial ou non commercial)</li>
                            <li>Tenter de décompiler ou de faire du reverse engineering sur un logiciel</li>
                            <li>Supprimer tout droit d'auteur ou autres notations de propriété des matériaux</li>
                        </ul>

                        <h4 class="mt-4">4. Responsabilité</h4>
                        <p>En aucun cas, nous ne pourrons être tenus responsables des dommages (y compris, sans limitation, les dommages pour perte de données ou de bénéfices, ou en raison d'une interruption d'activité) résultant de l'utilisation ou de l'impossibilité d'utiliser les matériaux sur le site web.</p>

                        <h4 class="mt-4">5. Modifications</h4>
                        <p>Nous nous réservons le droit de modifier ces conditions à tout moment. En utilisant ce site web, vous vous engagez à respecter la version la plus récente de ces conditions d'utilisation.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

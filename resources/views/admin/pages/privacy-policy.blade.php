@extends('admin.base')

@section('title', 'Politique de confidentialité')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Confidentialité</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Politique de confidentialité</h1>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel boutique-texte">
<h4>1. Collecte des informations</h4>
                        <p>Nous recueillons des informations lorsque vous vous inscrivez sur notre site, passez une commande, vous inscrivez à notre newsletter ou répondez à un sondage. Lors de l'enregistrement ou de la commande sur notre site, il vous sera demandé de saisir votre nom, votre adresse e-mail, votre numéro de téléphone ou votre numéro de carte de crédit.</p>
                        
                        <h4 class="mt-4">2. Utilisation des informations</h4>
                        <p>Toutes les informations que nous recueillons auprès de vous peuvent être utilisées pour :</p>
                        <ul>
                            <li>Personnaliser votre expérience et répondre à vos besoins individuels</li>
                            <li>Fournir un contenu publicitaire personnalisé</li>
                            <li>Améliorer notre site web</li>
                            <li>Améliorer le service client et vos besoins de prise en charge</li>
                            <li>Vous contacter par e-mail</li>
                            <li>Administrer un concours, une promotion ou une enquête</li>
                        </ul>

                        <h4 class="mt-4">3. Confidentialité du commerce en ligne</h4>
                        <p>Nous sommes les seuls propriétaires des informations recueillies sur ce site. Vos informations personnelles ne seront pas vendues, échangées, transférées ou données à une autre société pour n'importe quelle raison, sans votre consentement, en dehors de ce qui est nécessaire pour répondre à une demande et/ou une transaction, comme pour passer une commande.</p>

                        <h4 class="mt-4">4. Divulgation à des tiers</h4>
                        <p>Nous ne vendons, n'échangeons et ne transférons pas vos informations personnelles identifiables à des tiers. Cela ne comprend pas les tierces parties de confiance qui nous aident à exploiter notre site Web ou à mener nos affaires, tant que ces parties conviennent de garder ces informations confidentielles.</p>

                        <h4 class="mt-4">5. Protection des informations</h4>
                        <p>Nous mettons en œuvre une variété de mesures de sécurité pour préserver la sécurité de vos informations personnelles. Nous utilisons un cryptage à la pointe de la technologie pour protéger les informations sensibles transmises en ligne.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

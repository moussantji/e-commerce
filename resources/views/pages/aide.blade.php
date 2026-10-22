@extends('base')

@section('title', 'Aide & support')
@section('meta_description', "Aide " . config('app.name') . " : questions fréquentes sur les commandes, le paiement Mobile Money, la livraison, les retours et l'espace vendeur.")

@section('content')
@include('section-begin')
<style>
.help-page{padding:30px 20px 64px;max-width:1000px}
.help-hero{position:relative;overflow:hidden;text-align:center;padding:clamp(28px,5vw,48px) 22px;border:1px solid var(--line);border-radius:22px;background:radial-gradient(560px 240px at 50% -10%,var(--lav-2),transparent 70%),linear-gradient(180deg,#fff,var(--lav-1));box-shadow:var(--shadow)}
.help-hero .help-eyebrow{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:var(--violet-700);background:#fff;border:1px solid var(--line);border-radius:999px;padding:7px 14px}
.help-hero .help-eyebrow .ic{width:15px;height:15px}
.help-hero h1{font-size:clamp(26px,4vw,40px);line-height:1.12;font-weight:800;letter-spacing:-.6px;color:var(--violet-900);margin:16px auto 10px;max-width:640px}
.help-hero p{max-width:600px;margin:0 auto 22px;color:var(--mut);font-size:14.5px;line-height:1.65}
.help-actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.help-actions .btn-solid,.help-actions .btn-line{padding:13px 24px;font-size:14px}
.help-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:14px;margin-top:16px}
.help-card{display:flex;gap:13px;align-items:flex-start;background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px;box-shadow:var(--shadow)}
.help-card .help-ci{width:40px;height:40px;border-radius:11px;background:var(--lav-1);color:var(--violet-600);display:flex;align-items:center;justify-content:center;flex:none}
.help-card b{display:block;font-size:13.5px;color:var(--ink);margin-bottom:3px}
.help-card a,.help-card span{display:block;font-size:13px;color:var(--mut);line-height:1.55}
.help-card a:hover{color:var(--violet-700)}
.help-faq{margin-top:34px}
.help-faq>h2,.help-links>h2{font-size:20px;font-weight:800;color:var(--violet-900);letter-spacing:-.3px;margin-bottom:14px;display:flex;align-items:center;gap:9px}
.help-faq>h2 .ic,.help-links>h2 .ic{color:var(--violet-600)}
.help-faq details{background:#fff;border:1px solid var(--line);border-radius:13px;box-shadow:var(--shadow);padding:0 18px;transition:border-color var(--t),box-shadow var(--t)}
.help-faq details[open]{border-color:#ddd6fe;box-shadow:var(--shadow-md)}
.help-faq details+details{margin-top:10px}
.help-faq summary{list-style:none;cursor:pointer;position:relative;padding:16px 36px 16px 0;font-size:14.5px;font-weight:600;color:var(--ink)}
.help-faq summary::-webkit-details-marker{display:none}
.help-faq summary::after{content:"+";position:absolute;right:2px;top:50%;transform:translateY(-50%);width:24px;height:24px;border-radius:50%;background:var(--lav-1);color:var(--violet-700);font-size:17px;font-weight:700;line-height:22px;text-align:center;transition:background var(--t),color var(--t)}
.help-faq details[open] summary::after{content:"−";background:var(--violet-600);color:#fff}
.help-faq summary:hover{color:var(--violet-700)}
.help-faq .help-a{padding:12px 0 17px;border-top:1px solid var(--lav-1);font-size:13.5px;line-height:1.7;color:var(--mut)}
.help-faq .help-a a{color:var(--violet-700);font-weight:600}
.help-faq .help-a a:hover{text-decoration:underline}
.help-links{margin-top:30px;border:1px solid var(--line);border-radius:14px;background:#fff;padding:20px 22px;box-shadow:var(--shadow)}
.help-links-row{display:flex;flex-wrap:wrap;gap:9px}
.help-links-row a{display:inline-flex;align-items:center;gap:7px;font-size:13px;font-weight:600;color:var(--violet-700);background:var(--lav-1);border:1px solid #ede9fe;border-radius:999px;padding:9px 15px;transition:background var(--t),transform var(--t)}
.help-links-row a:hover{background:var(--lav-2);transform:translateY(-1px)}
.help-links-row .ic{width:16px;height:16px}
@media(max-width:640px){.help-page{padding-top:18px;padding-bottom:44px}.help-card{padding:15px}.help-faq details{padding:0 14px}.help-actions .btn-solid,.help-actions .btn-line{width:100%}}
</style>

<main class="shop-wrap help-page">
    <section class="help-hero" aria-labelledby="aide-titre">
        <span class="help-eyebrow"><svg class="ic"><use href="#i-headset"/></svg> Aide &amp; support</span>
        <h1 id="aide-titre">Comment pouvons-nous vous aider ?</h1>
        <p>Commande, paiement Mobile Money, livraison, compte ou espace vendeur : trouvez la réponse ci-dessous ou contactez notre équipe.</p>
        <div class="help-actions">
            <a class="btn-solid" href="tel:+22382019583"><svg class="ic"><use href="#i-phone"/></svg> Appeler le support</a>
            <a class="btn-line" href="mailto:contact@mandenbaoubab.com?subject=Demande%20d%27aide"><svg class="ic"><use href="#i-reply"/></svg> Écrire un email</a>
        </div>
    </section>

    <section class="help-grid" aria-label="Coordonnées du support">
        <div class="help-card">
            <span class="help-ci"><svg class="ic"><use href="#i-phone"/></svg></span>
            <div>
                <b>Téléphone</b>
                <a href="tel:+22382019583">+223 82 01 95 83</a>
                <a href="tel:+22378057895">+223 78 05 78 95</a>
            </div>
        </div>
        <div class="help-card">
            <span class="help-ci"><svg class="ic"><use href="#i-reply"/></svg></span>
            <div>
                <b>Email</b>
                <a href="mailto:contact@mandenbaoubab.com">contact@mandenbaoubab.com</a>
                <span>Nous répondons dès que possible.</span>
            </div>
        </div>
        <div class="help-card">
            <span class="help-ci"><svg class="ic"><use href="#i-headset"/></svg></span>
            <div>
                <b>Horaires</b>
                <span>Lundi – Samedi · 9h – 18h</span>
                <span>Support téléphonique et email</span>
            </div>
        </div>
    </section>

    <section class="help-faq" aria-labelledby="faq-titre">
        <h2 id="faq-titre"><svg class="ic"><use href="#i-b2-info"/></svg> Questions fréquentes</h2>

        <details>
            <summary>Comment passer une commande ?</summary>
            <div class="help-a">
                Parcourez la boutique, ajoutez vos articles au panier puis cliquez sur « Commander ».
                Choisissez votre adresse de livraison et votre moyen de paiement, puis validez.
                Vous recevrez une confirmation et pourrez suivre la commande depuis votre compte.
            </div>
        </details>

        <details>
            <summary>Quels moyens de paiement acceptez-vous ?</summary>
            <div class="help-a">
                Les moyens disponibles sont affichés au moment de la commande : Orange Money, Moov Money,
                Wave et, selon les articles, le paiement à la livraison. Les paiements Mobile Money sont
                vérifiés avant la préparation de la commande.
            </div>
        </details>

        <details>
            <summary>Comment envoyer la preuve de paiement ?</summary>
            <div class="help-a">
                Après votre paiement Mobile Money, ouvrez la commande concernée et cliquez sur
                « J'ai payé · envoyer la preuve ». Ajoutez une capture d'écran ou une photo du reçu,
                avec l'identifiant de transaction si vous l'avez. La commande passe en vérification
                dès l'envoi et vous êtes notifié dès sa validation.
            </div>
        </details>

        <details>
            <summary>Comment suivre ma commande ?</summary>
            <div class="help-a">
                Connectez-vous, ouvrez <a href="{{ route('dashboard') }}">Mon compte</a> puis
                « Mes commandes ». Vous y trouverez le statut et le lien « Suivre cette commande ».
                Les changements importants (paiement validé, expédition, livraison) vous sont aussi
                signalés par notification.
            </div>
        </details>

        <details>
            <summary>Quels sont les délais et frais de livraison ?</summary>
            <div class="help-a">
                À Bamako, la livraison intervient généralement sous 24 à 48 h. Pour les autres villes,
                comptez quelques jours ouvrés selon la destination. La livraison est offerte dès
                {{ config('shop.free_shipping_min_items') }} articles et
                {{ number_format(config('shop.free_shipping_min_total'), 0, ',', ' ') }} FCFA d'achat ;
                sinon, les frais sont indiqués avant la validation de la commande.
            </div>
        </details>

        <details>
            <summary>Puis-je annuler ou modifier ma commande ?</summary>
            <div class="help-a">
                Contactez le support au plus vite avec votre numéro de commande. Tant que la commande
                n'est pas expédiée, une annulation reste possible ; après expédition, nous vous aidons
                à trouver la meilleure solution.
            </div>
        </details>

        <details>
            <summary>Comment retourner un article ou demander un remboursement ?</summary>
            <div class="help-a">
                Les demandes de retour ou de remboursement sont étudiées au cas par cas, conformément
                aux <a href="{{ route('conditions') }}">conditions d'utilisation</a>. Écrivez-nous avec
                votre numéro de commande, le motif et, si possible, une photo de l'article.
            </div>
        </details>

        <details>
            <summary>Comment devenir vendeur ?</summary>
            <div class="help-a">
                Depuis la page d'accueil, ouvrez la fenêtre de connexion puis cliquez sur
                « Créer un compte vendeur ». Renseignez vos informations : votre compte est activé
                après validation par l'équipe. Vous accédez ensuite à votre espace pour gérer vos
                produits et vos commandes.
            </div>
        </details>

        <details>
            <summary>J'ai oublié mon mot de passe ou je n'arrive pas à me connecter.</summary>
            <div class="help-a">
                Utilisez le lien « Mot de passe oublié ? » dans la fenêtre de connexion pour recevoir
                un lien de réinitialisation. Pensez à vérifier vos courriers indésirables. Si le problème
                persiste, écrivez-nous depuis l'adresse email de votre compte.
            </div>
        </details>

        <details>
            <summary>Comment modifier mes informations personnelles ?</summary>
            <div class="help-a">
                Depuis <a href="{{ route('dashboard') }}">Mon compte</a>, vous pouvez mettre à jour votre
                nom, votre téléphone, votre adresse et votre mot de passe. Ces informations servent
                uniquement à traiter vos commandes et vos livraisons.
            </div>
        </details>
    </section>

    <section class="help-links" aria-labelledby="liens-titre">
        <h2 id="liens-titre"><svg class="ic"><use href="#i-grid"/></svg> Liens utiles</h2>
        <div class="help-links-row">
            <a href="{{ route('dashboard') }}"><svg class="ic"><use href="#i-truck"/></svg> Suivre ma commande</a>
            <a href="{{ route('panier') }}"><svg class="ic"><use href="#i-bag"/></svg> Mon panier</a>
            <a href="{{ route('favoris') }}"><svg class="ic"><use href="#i-heart"/></svg> Mes favoris</a>
            <a href="{{ route('conditions') }}"><svg class="ic"><use href="#i-b2-check"/></svg> Conditions d'utilisation</a>
            <a href="{{ route('confidentialite') }}"><svg class="ic"><use href="#i-b2-lock"/></svg> Confidentialité</a>
            <a href="mailto:contact@mandenbaoubab.com"><svg class="ic"><use href="#i-reply"/></svg> Nous contacter</a>
        </div>
    </section>
</main>

@include('partials.footer')
@endsection

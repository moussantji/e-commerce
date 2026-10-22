<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
@php
$siteName = config('app.name', 'MandenBaoubab');

// Les sections Blade sont déjà échappées par Laravel : on les décode avant
// de composer les métadonnées, puis Blade les échappe une seule fois.
$decodeSection = static fn (string $value): string => trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

$pageTitle = trim(preg_replace('/\s+/u', ' ', strip_tags($decodeSection($__env->yieldContent('title')))));
$pageDescription = trim(preg_replace('/\s+/u', ' ', strip_tags($decodeSection($__env->yieldContent('meta_description')))));

$seoTitle = $pageTitle !== '' ? $pageTitle . ' | ' . $siteName : $siteName . ' — Boutique en ligne au Mali';
$seoDescription = $pageDescription !== '' ? $pageDescription : $siteName . ' — boutique en ligne au Mali : offres flash, livraison rapide à Bamako et dans tout le pays, paiement Mobile Money ou à la livraison.';
$seoDescription = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', $seoDescription)), 160, '…');

$seoCanonical = $decodeSection($__env->yieldContent('canonical')) ?: url()->current();
$seoImage = $decodeSection($__env->yieldContent('og_image')) ?: asset('img/shop/hero-bg.jpg');
$seoImageAlt = trim(preg_replace('/\s+/u', ' ', strip_tags($decodeSection($__env->yieldContent('og_image_alt'))))) ?: $siteName;
$seoType = $decodeSection($__env->yieldContent('og_type')) ?: 'website';

$privateRoutes = ['panier', 'checkout*', 'commande*', 'paiement.*', 'dashboard', 'profile*', 'favoris', 'notifications*', 'login', 'register', 'password.*', 'verification.*', 'admin.*', 'vendeur.*'];
$seoRobots = $decodeSection($__env->yieldContent('robots')) ?: (request()->routeIs($privateRoutes) ? 'noindex, nofollow' : 'index, follow, max-image-preview:large');
$structuredData = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            '@id' => url('/') . '#organization',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => asset('assets/img/favicons/favicon.png'),
            'telephone' => '+22382019583',
            'email' => 'contact@mandenbaoubab.com',
            'areaServed' => ['@type' => 'Country', 'name' => 'Mali'],
            'contactPoint' => [
                [
                    '@type' => 'ContactPoint',
                    'telephone' => '+22382019583',
                    'contactType' => 'customer service',
                    'availableLanguage' => 'French',
                ],
                [
                    '@type' => 'ContactPoint',
                    'telephone' => '+22378057895',
                    'contactType' => 'customer service',
                    'availableLanguage' => 'French',
                ],
            ],
        ],
        [
            '@type' => 'Store',
            '@id' => url('/') . '#store',
            'name' => $siteName,
            'url' => url('/'),
            'image' => asset('img/shop/hero-bg.jpg'),
            'telephone' => '+22382019583',
            'email' => 'contact@mandenbaoubab.com',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Bamako',
                'addressCountry' => 'ML',
            ],
            'areaServed' => ['@type' => 'Country', 'name' => 'Mali'],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    'opens' => '09:00',
                    'closes' => '18:00',
                ],
            ],
            'priceRange' => 'FCFA',
            'currenciesAccepted' => 'XOF',
            'paymentAccepted' => 'Orange Money, Moov Money, Wave, espèces',
        ],
        [
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'url' => url('/'),
            'name' => $siteName,
            'inLanguage' => 'fr-ML',
            'publisher' => ['@id' => url('/') . '#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('products') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ],
];
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoCanonical }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:alt" content="{{ $seoImageAlt }}">
<meta property="og:locale" content="fr_ML">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
<meta name="twitter:image:alt" content="{{ $seoImageAlt }}">
<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<meta name="theme-color" content="#4c1d95">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@include('partials.boutique-style')
@stack('head')
</head>
<body>
@include('partials.boutique-sprite')

<div id="progress" aria-hidden="true"></div>
<div id="navprogress" aria-hidden="true"></div>

@yield('content')

@if(session('success') || session('error'))
<div id="toastNotification" class="{{ session('error') ? 'err' : 'ok' }}">{{ session('error') ? '⚠️' : '✅' }} {{ session('error') ?? session('success') }}</div>
@endif

@livewireScripts
@include('partials.spa-nav')
@include('partials.search-modal')
@include('partials.login-modal')
@include('partials.cookies')

<script>
/* JS nouveau uniquement — vanilla, aucun ancien script */
(function(){
function hideLoader(){var l=document.getElementById('pageLoader');if(l)l.classList.add('fade-out');}
window.addEventListener('load',function(){setTimeout(hideLoader,300);});
setTimeout(hideLoader,2500);
var toast=document.getElementById('toastNotification');
if(toast){setTimeout(function(){toast.style.opacity='0';setTimeout(function(){toast.remove();},400);},8000);}
// reveal + cascade cartes + progression + nav
function cascadeCards(){document.querySelectorAll('.grid .card').forEach(function(c,i){if(!c.style.transitionDelay)c.style.transitionDelay=(Math.min(i,12)*70)+'ms';});}
cascadeCards();
/* Carte entière cliquable : toute la zone de la carte mène au produit.
   Les liens et boutons internes (favori, « Voir le produit »...) gardent
   leur comportement : on ne déclenche la navigation que hors de ces éléments. */
document.addEventListener('click',function(e){
var t=e.target;
if(!t||!t.closest)return;
var card=t.closest('[data-card-href]');
if(!card)return;
if(t.closest('a,button,input,select,textarea,label,summary'))return;
if(window.getSelection&&String(window.getSelection()).length)return;
var url=card.getAttribute('data-card-href');
if(!url)return;
if(e.ctrlKey||e.metaKey||e.shiftKey){window.open(url,'_blank','noopener');return;}
if(window.Livewire&&typeof Livewire.navigate==='function'){Livewire.navigate(url);}
else{window.location.href=url;}
});
var io=null;
function observeRv(){
var els=document.querySelectorAll('.rv:not(.in):not([data-rv-obs])');
if(!('IntersectionObserver' in window)){els.forEach(function(el){el.classList.add('in');});return;}
if(!io){io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:.12,rootMargin:'0px 0px -40px 0px'});}
els.forEach(function(el){el.setAttribute('data-rv-obs','1');io.observe(el);});
}
observeRv();
setTimeout(function(){document.querySelectorAll('.rv:not(.in)').forEach(function(el){el.classList.add('in');});},4000);
// Après chaque mise à jour Livewire (filtres, pagination...), les nouvelles cartes
// .rv doivent redevenir visibles : on ré-observe + filet de sécurité immédiat.
function revealAfterLivewire(){cascadeCards();observeRv();setTimeout(function(){document.querySelectorAll('.rv:not(.in)').forEach(function(el){var r=el.getBoundingClientRect();if(r.top<window.innerHeight+200)el.classList.add('in');});},50);}
window.revealAfterLivewire=revealAfterLivewire;
document.addEventListener('livewire:init',function(){if(window.Livewire&&Livewire.hook){try{Livewire.hook('morph.updated',function(){revealAfterLivewire();});}catch(e){}}});
/* Ré-initialise les contenus dynamiques après navigation SPA (wire:navigate). */
window.initDynamic=function(){
/* La coquille arrive fraîche via SPA : le loader plein écran serait resté visible. */
var pl=document.getElementById('pageLoader');if(pl)pl.classList.add('fade-out');
document.body.classList.toggle('has-buybar',!!document.getElementById('buybar'));
if(document.getElementById('pdp')){window._pdImg=0;if(window.refreshPDP){try{window.refreshPDP();}catch(e){}}}
if(window.initHomeCountdown){try{window.initHomeCountdown();}catch(e){}}
if(window.checkLoginModal){try{window.checkLoginModal();}catch(e){}}
};
var barre=document.getElementById('progress'),planifie=false;
function maj(){var y=window.scrollY||document.documentElement.scrollTop;var h=document.documentElement.scrollHeight-window.innerHeight;var p=h>0?Math.min(1,y/h):0;if(barre)barre.style.transform='scaleX('+p+')';document.documentElement.classList.toggle('scrolled',y>8);planifie=false;}
window.addEventListener('scroll',function(){if(!planifie){planifie=true;requestAnimationFrame(maj);}},{passive:true});maj();
@include('partials.search-script')
})();
/* Modale connexion / compte du template : ouverte depuis l'icône compte */
(function(){
var modal=document.getElementById('loginModal');
if(!modal)return;
var box=modal.querySelector('.box');
function ouvrir(){
modal.classList.add('on');
modal.setAttribute('aria-hidden','false');
var premier=modal.querySelector('input[name="email"]');
window.setTimeout(function(){if(premier)premier.focus();},60);
}
function fermer(){modal.classList.remove('on');modal.setAttribute('aria-hidden','true');}
window.closeLoginModal=fermer;
window.checkLoginModal=function(){try{if(new URLSearchParams(window.location.search).has('connexion'))ouvrir();}catch(e){}};
window.checkLoginModal();
document.querySelectorAll('[data-account]').forEach(function(a){
a.addEventListener('click',function(e){e.preventDefault();ouvrir();});
});

modal.addEventListener('click',function(e){
if(e.target===modal||(e.target.closest&&e.target.closest('[data-close-modal]')))fermer();
});
document.addEventListener('keydown',function(e){
if(e.key==='Escape'&&modal.classList.contains('on'))fermer();
});
/* Modale demande vendeur : ouverte depuis la modale login (délégué : survit au DOM morphé). */
document.addEventListener('click', function(e) {
    if (!e.target.closest) return;
    var openBtn = e.target.closest('[data-open-vendeur]');
    if (openBtn) {
        e.preventDefault();
        var lm = document.getElementById('loginModal');
        var vm = document.getElementById('vendeurModal');
        if (lm) {
            lm.classList.remove('on');
            lm.setAttribute('aria-hidden', 'true');
        }
        if (vm) {
            vm.classList.add('on');
            vm.setAttribute('aria-hidden', 'false');
            var first = vm.querySelector('input[name="name"]');
            if (first) window.setTimeout(function() {
                first.focus();
            }, 60);
        }
        return;
    }
    var closeBtn = e.target.closest('[data-close-vendeur]');
    var vm2 = document.getElementById('vendeurModal');
    if (!vm2) return;
    if (e.target === vm2 || closeBtn) {
        vm2.classList.remove('on');
        vm2.setAttribute('aria-hidden', 'true');
    }
});
document.addEventListener('keydown', function(e) {
    var vm3 = document.getElementById('vendeurModal');
    if (e.key === 'Escape' && vm3 && vm3.classList.contains('on')) {
        vm3.classList.remove('on');
        vm3.setAttribute('aria-hidden', 'true');
    }
});
var vf=document.getElementById('vendeurModalForm');
if(vf){
vf.addEventListener('submit',function(e){
var ok=true,firstBad=null;
vf.querySelectorAll('input[required]').forEach(function(i){
var bad=!i.value||!i.value.trim()||(i.type==='email'&&!/^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i.test(i.value.trim()))||(i.name==='password'&&i.value.length<8);
i.closest('.field').classList.toggle('bad',bad);
if(bad){ok=false;if(!firstBad)firstBad=i;}
});
var p1=vf.querySelector('input[name="password"]'),p2=vf.querySelector('input[name="password_confirmation"]');
if(p1&&p2&&p1.value!==p2.value){p2.closest('.field').classList.add('bad');ok=false;if(!firstBad)firstBad=p2;}
var zone=document.getElementById('vmErr'),txt=document.getElementById('vmErrTxt');
if(!ok){
e.preventDefault();
if(zone&&txt){zone.style.display='block';txt.textContent='Vérifiez les champs en rouge (email valide, 8 caractères min, mots de passe identiques).';}
if(firstBad)firstBad.focus();
}
});
}
/* Compte vendeur : création directe (sans code). */
var form=document.getElementById('loginModalForm');
if(form){
form.addEventListener('submit',function(e){
var mail=document.getElementById('lmMail'),mdp=document.getElementById('lmMdp');
var zone=document.getElementById('lmErr'),txt=document.getElementById('lmErrTxt');
var okMail=/^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i.test((mail.value||'').trim());
var okMdp=(mdp.value||'').length>0;
mail.closest('.field').classList.toggle('bad',!okMail);
mdp.closest('.field').classList.toggle('bad',!okMdp);
if(!okMail||!okMdp){
e.preventDefault();
zone.style.display='block';
txt.textContent=!okMail?'Adresse email invalide.':'Mot de passe requis.';
}
});
}
})();
</script>
@stack('scripts')
<script src="{{ asset('assets/js/json-image-upload.js') }}?v=1"></script>
</body>
</html>

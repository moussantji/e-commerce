<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', 'Administration') — Boutique</title>
<meta name="description" content="Administration de la boutique en ligne.">
<meta name="theme-color" content="#4c1d95">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@include('partials.boutique-style')
@stack('styles')
</head>
<body>
@include('partials.boutique-sprite')

<div class="page-loader" id="pageLoader"><div class="loader-circle"></div></div>

<div class="shop-topbar">
<div class="shop-wrap">
<div class="top-left"><svg class="ic ic-sm"><use href="#i-bolt"/></svg><b>Administration</b></div>
<div class="top-push"><a href="{{ route('home') }}">Voir la boutique</a><a href="{{ route('admin.profile') }}">Mon profil</a></div>
</div>
</div>
<header class="shop-header">
<div class="shop-wrap header-row">
<a class="brand" href="{{ route('admin.dashboard') }}" aria-label="Administration"><span class="mark"><svg class="ic"><use href="#i-store"/></svg></span><span class="brand-name">Boutique · Admin</span></a>
<div class="shop-actions" style="margin-left:auto">
<a href="{{ route('home') }}" aria-label="Voir la boutique" title="Voir la boutique"><svg class="ic"><use href="#i-home"/></svg></a>
<a href="{{ route('admin.profile') }}" aria-label="Mon profil" title="Mon profil"><svg class="ic"><use href="#i-user"/></svg></a>
</div>
</div>
</header>
@include('admin.partials.nav-boutique')
<div id="navprogress" aria-hidden="true"></div>

@yield('content')

@include('partials.footer')

@if(session('success') || session('error'))
<div id="toastNotification" class="{{ session('error') ? 'err' : 'ok' }}">{{ session('error') ? '⚠️' : '✅' }} {{ session('error') ?? session('success') }}</div>
@endif

@livewireScripts
@include('partials.spa-nav')
<script>
/* Coquille admin boutique : loader + toast + reveal (comme l'accueil). */
(function(){
function hideLoader(){var l=document.getElementById('pageLoader');if(l)l.classList.add('fade-out');}
window.addEventListener('load',function(){setTimeout(hideLoader,300);});
setTimeout(hideLoader,2500);
var toast=document.getElementById('toastNotification');
if(toast){setTimeout(function(){toast.style.opacity='0';setTimeout(function(){toast.remove();},400);},8000);}
function cascadeCards(){document.querySelectorAll('.grid .card').forEach(function(c,i){if(!c.style.transitionDelay)c.style.transitionDelay=(Math.min(i,12)*70)+'ms';});}
cascadeCards();
var io=null;
function observeRv(){
var els=document.querySelectorAll('.rv:not(.in):not([data-rv-obs])');
if(!('IntersectionObserver' in window)){els.forEach(function(el){el.classList.add('in');});return;}
if(!io){io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});},{threshold:.12,rootMargin:'0px 0px -40px 0px'});}
els.forEach(function(el){el.setAttribute('data-rv-obs','1');io.observe(el);});
}
observeRv();
setTimeout(function(){document.querySelectorAll('.rv:not(.in)').forEach(function(el){el.classList.add('in');});},4000);
function revealAfterLivewire(){cascadeCards();observeRv();setTimeout(function(){document.querySelectorAll('.rv:not(.in)').forEach(function(el){var r=el.getBoundingClientRect();if(r.top<window.innerHeight+200)el.classList.add('in');});},50);}
window.revealAfterLivewire=revealAfterLivewire;
document.addEventListener('livewire:init',function(){if(window.Livewire&&Livewire.hook){try{Livewire.hook('morph.updated',function(){revealAfterLivewire();});}catch(e){}}});
/* Ré-initialise les contenus dynamiques après navigation SPA (wire:navigate). */
window.initDynamic=function(){
if(window.__rebuildSearchIndex){try{window.__rebuildSearchIndex();}catch(e){}}
if(document.getElementById('pdp')){window._pdImg=0;if(window.refreshPDP){try{window.refreshPDP();}catch(e){}}}
if(window.initHomeCountdown){try{window.initHomeCountdown();}catch(e){}}
if(window.checkLoginModal){try{window.checkLoginModal();}catch(e){}}
};
var barre=document.getElementById('progress'),planifie=false;
function maj(){var y=window.scrollY||document.documentElement.scrollTop;var h=document.documentElement.scrollHeight-window.innerHeight;var p=h>0?Math.min(1,y/h):0;if(barre)barre.style.transform='scaleX('+p+')';document.documentElement.classList.toggle('scrolled',y>8);planifie=false;}
window.addEventListener('scroll',function(){if(!planifie){planifie=true;requestAnimationFrame(maj);}},{passive:true});maj();
})();
</script>
@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', 'Boutique')</title>
<meta name="description" content="Boutique en ligne — offres flash, livraison offerte, paiement Mobile Money.">
<meta name="theme-color" content="#4c1d95">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@include('partials.boutique-style')
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
if(window.__rebuildSearchIndex){try{window.__rebuildSearchIndex();}catch(e){}}
document.body.classList.toggle('has-buybar',!!document.getElementById('buybar'));
if(document.getElementById('pdp')){window._pdImg=0;if(window.refreshPDP){try{window.refreshPDP();}catch(e){}}}
if(window.initHomeCountdown){try{window.initHomeCountdown();}catch(e){}}
if(window.checkLoginModal){try{window.checkLoginModal();}catch(e){}}
};
var barre=document.getElementById('progress'),planifie=false;
function maj(){var y=window.scrollY||document.documentElement.scrollTop;var h=document.documentElement.scrollHeight-window.innerHeight;var p=h>0?Math.min(1,y/h):0;if(barre)barre.style.transform='scaleX('+p+')';document.documentElement.classList.toggle('scrolled',y>8);planifie=false;}
window.addEventListener('scroll',function(){if(!planifie){planifie=true;requestAnimationFrame(maj);}},{passive:true});maj();
/* Recherche — identique au HTML : index lu dans les vraies cartes .grid .card */
(function(){
var sm=document.getElementById('sm');
if(!sm||!sm.querySelector)return;
var pan=sm.querySelector('.sm-pan');
var champ=document.getElementById('sm-q');
var corps=sm.querySelector('.sm-body');
var bloc=document.getElementById('sm-default');
var res=document.getElementById('sm-results');
var zoneO=document.getElementById('sm-offres');
var form=document.getElementById('shopSearchForm');
if(!form)form=document.querySelector('.shop-search');
var haut=form?form.querySelector('input'):document.getElementById('shopSearchInput');
var reduit=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
var garde=false,dernier=null;
var produits=[];
function norm(s){return String(s).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');}
function ech(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function pos(nom,cible){var n=0;for(var i=0;i<nom.length;i++){if(n===cible)return i;n+=norm(nom.charAt(i)).length;}return nom.length;}
function surligne(nom,q){var plat=norm(nom),i=q?plat.indexOf(q):-1;if(i<0)return ech(nom);var a=pos(nom,i),b=pos(nom,i+q.length);return ech(nom.slice(0,a))+'<mark>'+ech(nom.slice(a,b))+'</mark>'+ech(nom.slice(b));}
function buildIndex(force){
var sig='';
var list=[];
Array.prototype.forEach.call(document.querySelectorAll('.grid .card'),function(c){
if(sm&&sm.contains(c))return;
var th=c.querySelector('.thumb'),nm=c.querySelector('.name');
var pr=c.querySelector('.price'),av=c.querySelector('.was');
var of=c.querySelector('.off');
if(!nm||!pr)return;
var nom=nm.textContent.trim(),prix=pr.textContent.trim();
var avant=av?av.textContent.trim():'',remise=of?of.textContent.trim():'';
sig+=nom+'|'+prix+'|'+avant+'|'+remise+';';
list.push({el:c,nom:nom,prix:prix,avant:avant,remise:remise,bg:(th&&th.getAttribute('style'))||'',cle:norm(nom)});
});
list.forEach(function(p,i){p.id=i;});
if(!force&&sig===buildIndex._sig)return;
buildIndex._sig=sig;
produits=list;
var plus=produits.filter(function(p){return p.remise;}).sort(function(a,b){return parseInt(b.remise.replace(/\D/g,''),10)-parseInt(a.remise.replace(/\D/g,''),10);}).slice(0,3);
if(zoneO){var html=plus.map(function(p,i){return ligne(p,'',0);}).join('');if(zoneO._html!==html){zoneO._html=html;zoneO.innerHTML=html;}}
if(!sm.hidden&&champ&&champ.value)filtrer(champ.value,true);
else if(champ&&!champ.value&&!sm.hidden)filtrer('',true);
}
buildIndex._sig=null;
window.__rebuildSearchIndex=function(){buildIndex(true);};
function ligne(p,q,delai){
return '<button class="row" type="button" data-i="'+p.id+'"'+(delai?' style="animation-delay:'+delai+'ms"':'')+'>'+
'<span class="th"'+(p.bg?' style="'+ech(p.bg)+'"':'')+'></span>'+
'<span class="tx"><span class="nm">'+surligne(p.nom,q)+'</span>'+
'<span class="mt">'+(p.remise?'<span class="bg">'+ech(p.remise)+'</span>':'')+(p.avant?'<s>'+ech(p.avant)+'</s>':'')+'</span></span>'+
'<span class="pr">'+ech(p.prix)+'</span>'+
'<svg class="ic" aria-hidden="true"><use href="#i-chevron"/></svg></button>';
}
function filtrer(v,fromRebuild){
var q=norm(String(v).trim());
if(!q){if(bloc.hidden===false&&res.hidden===true&&!res.innerHTML)return;bloc.hidden=false;res.hidden=true;res.innerHTML='';res._html='';if(corps&&!fromRebuild)corps.scrollTop=0;return;}
bloc.hidden=true;res.hidden=false;
var t=produits.filter(function(p){return p.cle.indexOf(q)>=0;});
var html;
if(!t.length){html='<div class="sm-empty"><svg class="ic" aria-hidden="true"><use href="#i-search"/></svg><b>Aucun produit ne correspond à « '+ech(String(v).trim())+' »</b><span>Essayez « smartphone », « machine à laver » ou « ventilateur ».</span></div>';}
else{html='<div class="sm-sec"><div class="sm-h">Produits</div><div class="sm-list">'+t.map(function(p,i){return ligne(p,q,i*45);}).join('')+'</div></div>';}
if(res._html===html+q)return;
res._html=html+q;res.innerHTML=html;
if(corps&&!fromRebuild)corps.scrollTop=0;
}
function versProduit(i){
var p=produits[parseInt(i,10)];
if(!p)return;
fermer();
window.setTimeout(function(){
if(p.el.scrollIntoView){p.el.scrollIntoView({behavior:reduit?'auto':'smooth',block:'center'});}
p.el.classList.add('flash');
window.setTimeout(function(){p.el.classList.remove('flash');},1500);
},200);
}
function ouvrir(){
if(!sm.hidden)return;
dernier=document.activeElement;
if(haut&&haut.value&&champ&&!champ.value)champ.value=haut.value;
buildIndex(true);
filtrer(champ?champ.value:'');
sm.hidden=false;
document.documentElement.classList.add('sm-open');
void sm.offsetWidth;
sm.classList.add('on');
window.setTimeout(function(){if(champ)champ.focus();},40);
}
function fermer(){
if(sm.hidden)return;
sm.classList.remove('on');
document.documentElement.classList.remove('sm-open');
window.setTimeout(function(){sm.hidden=true;},340);
if(dernier&&dernier.focus&&dernier!==champ)dernier.focus();
garde=true;
window.setTimeout(function(){garde=false;},420);
}
buildIndex(true);
/* L'index n'est reconstruit qu'à l'ouverture : aucun observateur Livewire/Mutation
   ne touche à la modale, donc plus aucun re-rendu en arrière-plan. */
if(form){
form.addEventListener('mousedown',function(e){e.preventDefault();});
form.addEventListener('click',function(e){e.preventDefault();ouvrir();});
form.addEventListener('submit',function(e){e.preventDefault();});
}
if(haut){
haut.addEventListener('focus',function(){if(!garde)ouvrir();});
haut.addEventListener('keydown',function(e){if(e.key==='Enter'||e.key==='ArrowDown'){e.preventDefault();ouvrir();}});
}
sm.addEventListener('click',function(e){
var t=e.target;
if(t.closest&&t.closest('[data-sm-close]')){e.preventDefault();fermer();return;}
var r=t.closest?t.closest('.row'):null;
if(r){e.preventDefault();versProduit(r.getAttribute('data-i'));return;}
var p=t.closest?t.closest('.pop'):null;
if(p&&champ){e.preventDefault();champ.value=p.getAttribute('data-q');if(haut)haut.value=champ.value;champ.focus();filtrer(champ.value);}
});
if(champ){
champ.addEventListener('input',function(){if(haut)haut.value=champ.value;filtrer(champ.value);});
champ.addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();var r=res.querySelector('.row');if(r)versProduit(r.getAttribute('data-i'));}});
}
document.addEventListener('keydown',function(e){
if(e.key==='Escape'){fermer();return;}
var t=(document.activeElement&&document.activeElement.tagName)||'';
var saisie=t==='INPUT'||t==='TEXTAREA';
if((e.key==='/'&&!saisie)||((e.metaKey||e.ctrlKey)&&String(e.key).toLowerCase()==='k')){e.preventDefault();ouvrir();}
});
if(pan){
pan.addEventListener('keydown',function(e){
if(e.key!=='Tab')return;
var f=pan.querySelectorAll('button, input, a[href]');
if(!f.length)return;
var premier=f[0],dernierEl=f[f.length-1];
if(e.shiftKey&&document.activeElement===premier){e.preventDefault();dernierEl.focus();}
else if(!e.shiftKey&&document.activeElement===dernierEl){e.preventDefault();premier.focus();}
});
}
})();
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
/* Modale demande vendeur : ouverte depuis la modale login. */
(function(){
var vm=document.getElementById('vendeurModal');
if(!vm)return;
function vmOuvrir(){if(modal)modal.classList.remove('on');vm.classList.add('on');vm.setAttribute('aria-hidden','false');}
function vmFermer(){vm.classList.remove('on');vm.setAttribute('aria-hidden','true');}
document.querySelectorAll('[data-open-vendeur]').forEach(function(b){
b.addEventListener('click',function(e){e.preventDefault();vmOuvrir();});
});
vm.addEventListener('click',function(e){
if(e.target===vm||(e.target.closest&&e.target.closest('[data-close-vendeur]')))vmFermer();
});
document.addEventListener('keydown',function(e){
if(e.key==='Escape'&&vm.classList.contains('on'))vmFermer();
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
})();
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
</body>
</html>

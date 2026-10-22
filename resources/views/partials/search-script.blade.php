/* Recherche globale : les résultats viennent de l'API produits, donc la recherche
   fonctionne aussi depuis les pages qui n'affichent aucune fiche produit. */
(function(){
var sm=document.getElementById('sm');
var touches=window.__shopSearchKeys;
if(!touches){
touches=window.__shopSearchKeys={handler:null};
document.addEventListener('keydown',function(e){if(touches.handler)touches.handler(e);});
}
if(!sm||!sm.querySelector){touches.handler=null;return;}
var pan=sm.querySelector('.sm-pan');
var champ=document.getElementById('sm-q');
var corps=sm.querySelector('.sm-body');
var bloc=document.getElementById('sm-default');
var res=document.getElementById('sm-results');
var zoneO=document.getElementById('sm-offres');
var offreWrap=document.getElementById('sm-offres-wrap');
var form=document.getElementById('shopSearchForm')||document.querySelector('.shop-search');
var haut=form?form.querySelector('input'):document.getElementById('shopSearchInput');
var garde=false,dernier=null,minuteur=null,sequence=0,controleur=null,offresChargees=false;
var api=@json(url('/api/products'));
var produitsUrl=@json(route('products'));
var modeleProduit=@json(route('produits.show', ['slug' => '__slug__', 'id' => '__id__']));
var cache={};

function norm(s){return String(s).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');}
function ech(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');}
function pos(nom,cible){var n=0;for(var i=0;i<nom.length;i++){if(n===cible)return i;n+=norm(nom.charAt(i)).length;}return nom.length;}
function surligne(nom,q){var plat=norm(nom),i=q?plat.indexOf(norm(q)):-1;if(i<0)return ech(nom);var a=pos(nom,i),b=pos(nom,i+norm(q).length);return ech(nom.slice(0,a))+'<mark>'+ech(nom.slice(a,b))+'</mark>'+ech(nom.slice(b));}
function fcfa(v){var n=Math.round(Number(v)||0);return String(n).replace(/\B(?=(\d{3})+(?!\d))/g,' ')+' FCFA';}
function urlProduit(p){return modeleProduit.replace('__slug__',encodeURIComponent(p.slug||'')).replace('__id__',encodeURIComponent(String(p.id||'')));}
function urlResultats(q){if(!q)return produitsUrl;return produitsUrl+(produitsUrl.indexOf('?')>=0?'&':'?')+'q='+encodeURIComponent(q);}

function ligne(p,q,delai){
var prixNormal=Number(p.price)||0;
var prixVente=(p.sale_price!==null&&p.sale_price!==undefined&&Number(p.sale_price)<prixNormal)?Number(p.sale_price):prixNormal;
var promo=prixVente<prixNormal;
var remise=(promo&&Number(p.discount_percent)>0)?'<span class="bg">-'+parseInt(p.discount_percent,10)+'%</span>':'';
var avant=promo?'<s>'+fcfa(prixNormal)+'</s>':'';
var meta=(remise||avant)?'<span class="mt">'+remise+avant+'</span>':'';
var image=p.image?'<span class="th" style="background-image:url(\''+ech(p.image)+'\')"></span>':'<span class="th"></span>';
return '<button class="row" type="button" data-url="'+ech(urlProduit(p))+'"'+(delai?' style="animation-delay:'+delai+'ms"':'')+'>'+image+
'<span class="tx"><span class="nm">'+surligne(p.name||'',q)+'</span>'+meta+'</span>'+
'<span class="pr">'+fcfa(prixVente)+'</span>'+
'<svg class="ic" aria-hidden="true"><use href="#i-chevron"/></svg></button>';
}

function afficherDefaut(){
sequence++;
if(controleur){controleur.abort();controleur=null;}
if(bloc)bloc.hidden=false;
if(res){res.hidden=true;res.innerHTML='';}
if(corps)corps.scrollTop=0;
}

function afficherChargement(){
if(bloc)bloc.hidden=true;
if(res){res.hidden=false;res.innerHTML='<div class="sm-empty"><b>Recherche en cours…</b></div>';}
if(corps)corps.scrollTop=0;
}

function afficherErreur(q){
if(bloc)bloc.hidden=true;
if(res){res.hidden=false;res.innerHTML='<div class="sm-empty"><b>Recherche momentanément indisponible.</b><span>Réessayez dans un instant ou ouvrez la liste des produits.</span></div><div class="sm-sec"><button class="more-btn" type="button" data-url="'+ech(urlResultats(q))+'">Voir tous les produits</button></div>';}
}

function afficherResultats(items,q){
if(!res)return;
if(!items.length){
res.innerHTML='<div class="sm-empty"><svg class="ic" aria-hidden="true"><use href="#i-search"/></svg><b>Aucun produit ne correspond à « '+ech(q)+' »</b><span>Essayez un autre mot-clé ou parcourez tous les produits.</span></div><div class="sm-sec"><button class="more-btn" type="button" data-url="'+ech(urlResultats(q))+'">Voir tous les produits</button></div>';
}else{
res.innerHTML='<div class="sm-sec"><div class="sm-h">Produits</div><div class="sm-list">'+items.map(function(p,i){return ligne(p,q,i*45);}).join('')+'</div></div><div class="sm-sec"><button class="more-btn" type="button" data-url="'+ech(urlResultats(q))+'">Voir tous les résultats</button></div>';
}
if(corps)corps.scrollTop=0;
}

function rechercher(q){
var terme=String(q||'').trim();
if(!terme){afficherDefaut();return;}
var cle=norm(terme);
var seq=++sequence;
if(controleur){controleur.abort();controleur=null;}
if(cache[cle]){afficherResultats(cache[cle],terme);return;}
controleur=window.AbortController?new AbortController():null;
afficherChargement();
var opts={headers:{'Accept':'application/json'},credentials:'same-origin'};
if(controleur)opts.signal=controleur.signal;
fetch(api+'?search='+encodeURIComponent(terme)+'&per_page=6&sort=popular',opts)
.then(function(r){if(!r.ok)throw new Error('HTTP '+r.status);return r.json();})
.then(function(data){
if(seq!==sequence)return;
var items=(data&&data.data)?data.data:[];
cache[cle]=items;
afficherResultats(items,terme);
})
.catch(function(err){
if(err&&err.name==='AbortError')return;
if(seq!==sequence)return;
afficherErreur(terme);
});
}

function planifier(q){clearTimeout(minuteur);minuteur=setTimeout(function(){rechercher(q);},240);}

function chargerOffres(){
if(offresChargees)return;
offresChargees=true;
if(zoneO)zoneO.innerHTML='<div class="sm-empty"><b>Chargement…</b></div>';
fetch(api+'?per_page=3&sort=popular',{headers:{'Accept':'application/json'},credentials:'same-origin'})
.then(function(r){if(!r.ok)throw new Error('HTTP '+r.status);return r.json();})
.then(function(data){
var items=(data&&data.data)?data.data:[];
if(offreWrap)offreWrap.hidden=!items.length;
if(zoneO)zoneO.innerHTML=items.map(function(p,i){return ligne(p,'',i*55);}).join('');
})
.catch(function(){if(offreWrap)offreWrap.hidden=true;if(zoneO)zoneO.innerHTML='';});
}

function ouvrirUrl(url){
if(!url)return;
fermer();
window.setTimeout(function(){window.location.href=url;},140);
}

function ouvrir(){
if(!sm.hidden)return;
dernier=document.activeElement;
if(haut&&haut.value&&champ&&!champ.value)champ.value=haut.value;
sm.hidden=false;
document.documentElement.classList.add('sm-open');
void sm.offsetWidth;
sm.classList.add('on');
chargerOffres();
if(champ&&champ.value.trim()){rechercher(champ.value);}
else{afficherDefaut();}
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

if(form){
form.addEventListener('mousedown',function(e){e.preventDefault();});
form.addEventListener('click',function(e){e.preventDefault();ouvrir();});
form.addEventListener('submit',function(e){e.preventDefault();ouvrir();});
}
if(haut){
haut.addEventListener('focus',function(){if(!garde)ouvrir();});
haut.addEventListener('keydown',function(e){if(e.key==='Enter'||e.key==='ArrowDown'){e.preventDefault();ouvrir();}});
}
sm.addEventListener('click',function(e){
var t=e.target;
if(t.closest&&t.closest('[data-sm-close]')){e.preventDefault();fermer();return;}
var r=t.closest?t.closest('.row'):null;
if(r&&r.getAttribute('data-url')){e.preventDefault();ouvrirUrl(r.getAttribute('data-url'));return;}
var m=t.closest?t.closest('.more-btn'):null;
if(m&&m.getAttribute('data-url')){e.preventDefault();ouvrirUrl(m.getAttribute('data-url'));return;}
var p=t.closest?t.closest('.pop'):null;
if(p&&champ){e.preventDefault();champ.value=p.getAttribute('data-q')||'';if(haut)haut.value=champ.value;champ.focus();rechercher(champ.value);}
});
if(champ){
champ.addEventListener('input',function(){
if(haut)haut.value=champ.value;
var q=champ.value.trim();
if(!q){clearTimeout(minuteur);afficherDefaut();}
else{planifier(q);}
});
champ.addEventListener('keydown',function(e){
if(e.key!=='Enter')return;
e.preventDefault();
var r=res?res.querySelector('.row'):null;
if(r&&r.getAttribute('data-url')){ouvrirUrl(r.getAttribute('data-url'));}
else{ouvrirUrl(urlResultats(champ.value.trim()));}
});
}
touches.handler=function(e){
if(e.key==='Escape'){fermer();return;}
var t=(document.activeElement&&document.activeElement.tagName)||'';
var saisie=t==='INPUT'||t==='TEXTAREA';
if((e.key==='/'&&!saisie)||((e.metaKey||e.ctrlKey)&&String(e.key).toLowerCase()==='k')){e.preventDefault();ouvrir();}
};
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

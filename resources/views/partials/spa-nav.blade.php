{{-- Navigation SPA (wire:navigate) : liens internes sans rechargement de page.
    - Marque les liens éligibles AVANT le boot Livewire (binding à l'init).
    - Re-marque après chaque morph (filtres, pagination Livewire...).
    - Exclusions : externes, fichiers, ancres, OAuth/sessions, anti-spam. --}}
<script>
(function(){
function eligible(a){
if(!a||a.hasAttribute('wire:navigate')||a.hasAttribute('data-no-navigate'))return false;
if(a.target==='_blank'||a.hasAttribute('download')||a.hasAttribute('data-account'))return false;
var href=a.getAttribute('href');
if(!href||href.charAt(0)==='#')return false;
if(href.indexOf('mailto:')===0||href.indexOf('tel:')===0||href.indexOf('javascript:')===0)return false;
var url;
try{url=new URL(href,window.location.origin);}catch(e){return false;}
if(url.origin!==window.location.origin)return false;
if(url.pathname.indexOf('/auth/')===0)return false;
if(url.pathname.indexOf('/storage/')===0)return false;
if(url.pathname.indexOf('/images/')===0)return false;
if(/\.(pdf|png|jpe?g|webp|svg|ico|zip|mp4)(\?|#|$)/i.test(url.pathname))return false;
return true;
}
function tagAll(){
if(!document.querySelectorAll)return;
var list=document.querySelectorAll('a[href]');
for(var i=0;i<list.length;i++){if(eligible(list[i]))list[i].setAttribute('wire:navigate','');}
}
tagAll();
window.__tagSpaLinks=tagAll;
function navBar(on){
var b=document.getElementById('navprogress');
if(!b)return;
if(on){b.classList.remove('done');void b.offsetWidth;b.classList.add('on');}
else{b.classList.remove('on');b.classList.add('done');window.setTimeout(function(){b.classList.remove('done');},600);}
}
document.addEventListener('livewire:init', function(){
if(window.Livewire&&Livewire.hook){
Livewire.hook('morph.updated', function(){
tagAll();
if(window.revealAfterLivewire){try{window.revealAfterLivewire();}catch(e){}}
});
}
});
if(window.Livewire||true){
document.addEventListener('livewire:navigate',function(){
navBar(true);
if(window.closeLoginModal){try{window.closeLoginModal();}catch(e){}}
});
document.addEventListener('livewire:navigated',function(){
navBar(false);
if(window.initDynamic){try{window.initDynamic();}catch(e){}}
});
}
})();
</script>

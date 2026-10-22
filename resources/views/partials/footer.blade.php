<footer class="shop-footer" id="aide">
<div class="shop-wrap">
<div class="f-cols">
<div>
<div class="brandline"><span class="mark"><svg class="ic"><use href="#i-store"/></svg></span><b>{{ config('app.name') }}</b></div>
<p>Produits livrés partout au Mali.<br>Bamako · Ségou · Sikasso · Mopti</p>
</div>
<div><h3>Nos offres</h3><ul><li><a href="{{ route('products') }}">Nouveautés</a></li><li><a href="{{ route('products') }}#promos">Promotions</a></li><li><a href="{{ route('client.brands.index') }}">Marques</a></li></ul></div>
<div><h3>Commandes</h3><ul><li><a href="{{ route('dashboard') }}">Suivre ma commande</a></li><li><a href="{{ route('panier') }}">Livraison</a></li><li><a href="{{ route('dashboard') }}">Retours</a></li></ul></div>
<div><h3>Paiement</h3><ul><li>Orange Money</li><li>Moov Money</li><li>Wave</li></ul></div>
<div><h3>Contact</h3><ul><li><a href="tel:+22378057895">+223 78 05 78 95</a></li><li><a href="tel:+22382019583">+223 82 01 95 83</a></li><li><a href="mailto:contact@mandenbaoubab.com">contact@mandenbaoubab.com</a></li><li>Lun – Sam · 9h – 18h</li></ul></div>
</div>
<div class="f-bot"><span>© {{ date('Y') }} {{ config('app.name') }} — Tous droits réservés · Prix en FCFA</span><span><a href="{{ route('conditions') }}">Conditions</a> · <a href="{{ route('confidentialite') }}">Confidentialité</a> · <a href="{{ route('aide') }}">Aide</a></span></div>
</div>
</footer>

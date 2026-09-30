{{-- Bannière cookies boutique (consentement stocké en local). --}}
<div id="cookieBar"
    style="display:none;position:fixed;left:12px;right:12px;bottom:12px;z-index:110;background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow-lg);padding:14px 16px;align-items:center;gap:12px;flex-wrap:wrap">
    <svg class="ic" style="color:var(--violet-600);flex:none">
        <use href="#i-b2-info" />
    </svg>
    <p style="flex:1;min-width:200px;font-size:13px;color:#374151;margin:0">Nous utilisons des cookies pour le panier,
        la connexion et la mesure d'audience. <a class="lien"
            href="{{ route('confidentialite') }}">En savoir plus</a></p>
    <span style="display:flex;gap:8px;flex:none">
        <button class="btn-ghost-sm" type="button" data-cookie="non">Refuser</button>
        <button class="btn-solid" style="font-size:13px;padding:10px 22px" type="button"
            data-cookie="oui">Accepter</button>
    </span>
</div>
<script>
    (function() {
        var bar = document.getElementById('cookieBar');
        if (!bar) return;
        try {
            if (localStorage.getItem('bt_cookies')) return;
        } catch (e) {
            return;
        }
        bar.style.display = 'flex';
        bar.querySelectorAll('[data-cookie]').forEach(function(b) {
            b.addEventListener('click', function() {
                try {
                    localStorage.setItem('bt_cookies', b.getAttribute('data-cookie') +
                        '|' + new Date().toISOString());
                } catch (e) {}
                bar.remove();
            });
        });
    })();
</script>

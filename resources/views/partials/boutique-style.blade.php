<style>
/* ============ NOUVEAU DESIGN UNIQUEMENT (violet clair) — aucun ancien CSS ============ */
:root{--violet:var(--violet-600);--lav:var(--lav-1);--mut:var(--grey);--violet-900:#2e1065;--violet-800:#4c1d95;--violet-700:#6d28d9;--violet-600:#7c3aed;--violet-500:#8b5cf6;--violet-400:#a855f7;--lav-1:#f5f3ff;--lav-2:#ede9fe;--pink:#e11d48;--ink:#111827;--grey:#6b7280;--line:#e5e7eb;--white:#fff;--radius:14px;--shadow:0 1px 2px rgba(16,24,40,.05),0 2px 8px rgba(16,24,40,.05);--shadow-md:0 2px 4px rgba(16,24,40,.05),0 8px 20px rgba(46,16,101,.09);--shadow-lg:0 4px 10px rgba(16,24,40,.06),0 18px 44px rgba(76,29,149,.18);--ring:0 0 0 4px rgba(139,92,246,.18);--ease:cubic-bezier(.22,.61,.36,1);--ease-out:cubic-bezier(.16,1,.3,1);--t:.28s var(--ease);--t-slow:.5s var(--ease-out)}
*{margin:0;padding:0;box-sizing:border-box}
html{-webkit-text-size-adjust:100%;scroll-behavior:smooth}
body{font-family:'Poppins',system-ui,-apple-system,'Segoe UI',sans-serif;color:var(--ink);background:#fff;line-height:1.45;-webkit-font-smoothing:antialiased;overflow-x:hidden}
img{max-width:100%;display:block}
a{color:inherit;text-decoration:none}
button{font:inherit;cursor:pointer;border:0;background:none}
.ic{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex:none}
.ic-sm{width:15px;height:15px}
:focus-visible{outline:2px solid var(--violet-600);outline-offset:3px;border-radius:6px}
.shop-wrap,.wrap{max-width:1400px;margin:0 auto;padding:0 20px}
/* loader */
.page-loader{position:fixed;inset:0;background:#fff;display:flex;align-items:center;justify-content:center;z-index:99999;transition:opacity .4s,visibility .4s}
.page-loader.fade-out{opacity:0;visibility:hidden}
.loader-circle{width:58px;height:58px;border:3px solid var(--lav-2);border-top-color:var(--violet-600);border-radius:50%;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
/* reveal */
.rv{opacity:0;transform:translateY(20px);transition:opacity .6s var(--ease-out),transform .6s var(--ease-out)}
.rv.in{opacity:1;transform:none}
/* toast */
#toastNotification{position:fixed;top:20px;right:20px;max-width:340px;padding:12px 16px;border-radius:12px;box-shadow:var(--shadow-lg);z-index:10000;display:flex;align-items:center;gap:10px;font-size:14px;background:#fff;border:1px solid var(--line);color:var(--ink)}
#toastNotification.ok{background:#f0fdf4;border-color:#a7f3d0;color:#065f46}
#toastNotification.err{background:#fdf2f2;border-color:#fca5a5;color:#b91c1c}
/* topbar */
.shop-topbar{background:var(--violet-900);color:#e9d5ff;font-size:12.5px}
.shop-topbar .shop-wrap{display:flex;align-items:center;min-height:38px;flex-wrap:wrap}
.top-left{display:flex;align-items:center;gap:9px;padding:8px 0}
.top-left b{font-weight:600}
.shop-topbar .sep{width:1px;height:14px;background:rgba(255,255,255,.22);margin:0 14px}
.top-push{margin-left:auto;display:flex;align-items:center;gap:14px;padding:6px 0}
.top-push a{opacity:.9}.top-push a:hover{opacity:1;text-decoration:underline}
/* header */
.shop-header{color:#fff;position:relative;overflow:hidden;background:linear-gradient(90deg,var(--violet-700) 0%,var(--violet-600) 42%,var(--violet-400) 100%)}
.header-row{display:flex;align-items:center;gap:22px;padding-top:16px;padding-bottom:16px;position:relative;z-index:1}
.brand{display:flex;align-items:center;gap:10px;color:#fff}
.brand .mark{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.28)}
.brand-name{font-weight:800;font-size:19px}
.shop-search{flex:1;display:flex;align-items:center;gap:10px;background:#fff;border-radius:999px;padding:13px 22px;color:var(--grey);min-width:0;box-shadow:0 4px 14px rgba(46,16,101,.18)}
.shop-search .ic{color:var(--violet-600)}
.shop-search input{flex:1;border:0;outline:0;font:inherit;font-size:14.5px;color:var(--ink);background:transparent;min-width:0}
.shop-actions{display:flex;align-items:center;gap:22px}
.shop-actions a,.shop-actions button{color:#fff;position:relative;display:grid;place-items:center;font-size:21px}
.shop-actions .ic{width:23px;height:23px}
.cart-dot{position:absolute;top:-8px;right:-10px;background:var(--pink);color:#fff;font-size:10px;font-weight:700;min-width:18px;height:18px;border-radius:999px;display:grid;place-items:center;padding:0 5px;box-shadow:0 0 0 2px var(--violet-600)}
.shop-search-mobile{display:none;padding:0 20px 12px;position:relative;z-index:1}
/* nav */
.shop-nav{border-bottom:1px solid var(--line);background:#fff;position:sticky;top:0;z-index:20}
.shop-nav .nav-row{display:flex;align-items:center;justify-content:center;gap:38px;min-height:54px;overflow-x:auto;scrollbar-width:none}
.shop-nav .nav-row::-webkit-scrollbar{display:none}
.shop-nav a{font-size:14.5px;font-weight:500;color:#374151;white-space:nowrap;padding:6px 0}
.shop-nav a:hover{color:var(--violet-600)}
.shop-nav a.hot{color:var(--pink);font-weight:600}
/* hero */
.hero{padding:22px 0 6px}
.hero-grid{display:grid;grid-template-columns:2.15fr 1fr;gap:18px}
.banner{position:relative;border-radius:18px;overflow:hidden;padding:38px 40px;color:#fff;display:flex;flex-direction:column;justify-content:center;min-height:288px;box-shadow:var(--shadow-md);background:linear-gradient(105deg,var(--violet-800) 0%,rgba(109,40,217,.88) 34%,rgba(168,85,247,.55) 68%,rgba(168,85,247,.22) 100%),var(--violet-800) url("{{ asset('img/shop/hero-bg.jpg') }}") center/cover no-repeat}
.pill{display:inline-block;background:#fff;color:var(--violet-700);font-weight:700;font-size:13px;padding:7px 17px;border-radius:999px;align-self:flex-start}
.banner h1{font-size:clamp(30px,4.4vw,54px);line-height:1.03;font-weight:800;letter-spacing:-1px;margin:16px 0 10px;color:#fff}
.banner .sub{font-size:16px;color:#ede9fe;margin-bottom:24px}
.btn-buy{display:inline-flex;align-items:center;gap:9px;background:#fff;color:var(--violet-800);font-weight:700;font-size:14.5px;padding:14px 30px;border-radius:999px;align-self:flex-start}
.btn-buy:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(0,0,0,.24)}
.promos{display:grid;grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;gap:18px}
.promo{border-radius:16px;padding:22px;display:flex;flex-direction:column;justify-content:center;box-shadow:var(--shadow);min-height:135px}
.promo h2{font-size:24px;font-weight:800}
.promo p{font-size:13.5px;margin-top:6px}
.promo.light{color:var(--violet-900);background:linear-gradient(100deg,rgba(242,238,254,.97) 0%,rgba(242,238,254,.86) 38%,rgba(242,238,254,.42) 68%,rgba(242,238,254,.06) 100%),var(--lav-2) url("{{ asset('img/shop/soldes-bg.jpg') }}") right center/cover no-repeat}
.promo.light p{color:#6d28d9}
.promo.solid{color:#fff;background:linear-gradient(115deg,rgba(76,29,149,.93) 0%,rgba(109,40,217,.72) 55%,rgba(168,85,247,.5) 100%),var(--violet-800) url("{{ asset('img/shop/nouveautes-bg.jpg') }}") right center/cover no-repeat}
.promo.img{padding:0;background:var(--lav-1);border:1px solid var(--line);align-items:center;overflow:hidden}
.promo.img .tile{width:100%;height:100%;min-height:135px;display:grid;place-items:center;color:var(--violet-500);background:var(--lav-2)}
.promo.img .tile .ic{width:56px;height:56px;stroke-width:1.2}
.promo.spot{position:relative;padding:0;border:1px solid var(--line);overflow:hidden;display:block;min-height:135px}
.promo.spot .tile{display:block;width:100%;min-height:165px;background:var(--lav-2) center/cover no-repeat;transition:transform var(--t-slow)}
.promo.spot:hover .tile{transform:scale(1.06)}
.spot-tag{position:absolute;top:10px;left:10px;background:var(--pink);color:#fff;font-size:11px;font-weight:700;padding:4px 10px;border-radius:999px;z-index:2}
.spot-tag.new{background:var(--violet-600)}
.spot-info{position:absolute;left:0;right:0;bottom:0;padding:28px 14px 12px;background:linear-gradient(transparent,rgba(17,24,39,.8));z-index:2}
.spot-nm{display:block;font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.spot-pr{display:block;font-size:14px;font-weight:800;color:#fff}
/* trust */
.trust{background:var(--lav-2);border-top:1px solid #ddd6fe;border-bottom:1px solid #ddd6fe;margin-top:22px}
.trust-row{display:flex;align-items:stretch;min-height:64px;overflow-x:auto}
.trust .item{display:flex;align-items:center;gap:12px;padding:12px 30px;flex:1;white-space:nowrap}
.trust .item:first-child{padding-left:0}
.trust .item+.item{border-left:1px solid #ddd6fe}
.trust .ic{color:var(--violet-600);width:22px;height:22px}
.trust b{display:block;font-size:13.5px;color:var(--violet-900)}
.trust span{font-size:12px;color:#7c3aed}
/* sections + cards */
.sec{padding:30px 0 8px}
#promos{scroll-margin-top:70px}
.sec-head{display:flex;align-items:center;gap:18px;margin-bottom:18px}
.sec-head h2{font-size:22px;font-weight:700}
.count{display:flex;gap:7px;align-items:center}
.count b{background:var(--violet-800);color:#fff;font-size:14.5px;min-width:42px;padding:8px 0;border-radius:9px;display:grid;place-items:center;font-variant-numeric:tabular-nums}
.count i{color:var(--violet-800);font-style:normal;font-weight:700}
.sec-head .more{margin-left:auto;font-size:14px;font-weight:600;color:var(--violet-700);display:inline-flex;align-items:center;gap:4px}
.sec-head .more:hover{color:var(--violet-900)}
.grid{display:grid;grid-template-columns:repeat(6,1fr);gap:18px}
.card{background:#fff;border:1px solid var(--line);border-radius:var(--radius);overflow:hidden;display:flex;flex-direction:column;box-shadow:var(--shadow);transition:transform .28s var(--ease),box-shadow .28s var(--ease)}
.card:hover{transform:translateY(-7px);box-shadow:var(--shadow-lg)}
.thumb{position:relative;width:100%;aspect-ratio:1/.92;display:grid;place-items:center;background:var(--lav-1) center/cover no-repeat;color:#a5b4fc;overflow:hidden}
.thumb .ic{width:52px;height:52px;stroke-width:1.2}
.fav{position:absolute;top:9px;right:9px;width:31px;height:31px;border-radius:50%;background:rgba(255,255,255,.94);display:grid;place-items:center;color:var(--grey);z-index:2}
.fav:hover{color:var(--pink)}.fav.on{color:var(--pink)}.fav.on .ic{fill:currentColor}
.fav .ic{width:16px;height:16px}
.off{position:absolute;top:9px;left:9px;background:var(--pink);color:#fff;font-size:11px;font-weight:700;padding:4px 9px;border-radius:999px;z-index:2}
.card .body{padding:13px;display:flex;flex-direction:column;gap:5px;flex:1}
.card .name{font-size:13px;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.card .stars{font-size:11px;color:#f59e0b}.card .stars .off2{color:#e5e7eb}.card .stars span{color:var(--grey);margin-left:4px}
.card .was{font-size:11.5px;color:#9ca3af;text-decoration:line-through}
.card .price{font-size:17px;font-weight:800;color:var(--pink)}
.card .add{margin-top:auto;background:var(--violet-600);color:#fff;font-size:12.5px;font-weight:600;padding:10px;border-radius:9px;text-align:center;display:block}
.card .add:hover{background:var(--violet-700)}
.empty{color:var(--grey);padding:20px 0}
/* cats */
.cats{display:flex;flex-wrap:wrap;gap:12px;padding:26px 0 10px}
.cat{border:1px solid var(--line);border-radius:999px;padding:10px 22px;font-size:13.5px;color:#374151;background:#fff}
.cat:hover{border-color:var(--violet-500);color:var(--violet-700);background:var(--lav-1)}
.cat.hot{color:var(--pink);border-color:#fecdd3}
/* footer */
.shop-footer{background:var(--violet-900);color:#c4b5fd;margin-top:38px;padding:44px 0 26px;font-size:13px}
.shop-footer .f-cols{display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr 1fr;gap:26px}
.shop-footer h3{color:#fff;font-size:14px;margin-bottom:14px}
.shop-footer ul{list-style:none}.shop-footer li{margin-bottom:9px}
.shop-footer a:hover{color:#fff;padding-left:4px}
.brandline{display:flex;align-items:center;gap:11px;margin-bottom:14px}
.brandline .mark{width:38px;height:38px;border-radius:11px;background:rgba(255,255,255,.12);display:grid;place-items:center;color:#fff}
.brandline b{color:#fff;font-size:17px}
.f-bot{border-top:1px solid rgba(255,255,255,.13);margin-top:30px;padding-top:20px;display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;font-size:12px}
/* ============ Animations accueil — identiques au HTML ============ */
#progress{position:fixed;top:0;left:0;height:2px;width:100%;z-index:99;background:linear-gradient(90deg,var(--violet-700),var(--violet-400),var(--pink));transform:scaleX(0);transform-origin:0 50%;transition:transform .1s linear}
.shop-topbar .ic-sm{animation:drive 4.5s var(--ease) infinite}
@keyframes drive{0%,70%,100%{transform:translateX(0)}78%{transform:translateX(3px)}86%{transform:translateX(0)}}
.shop-header::before{content:"";position:absolute;inset:-40% -10%;pointer-events:none;background:radial-gradient(420px 200px at 12% 50%,rgba(255,255,255,.16),transparent 60%),radial-gradient(380px 190px at 78% 50%,rgba(255,255,255,.13),transparent 62%);animation:aura 14s var(--ease) infinite alternate}
@keyframes aura{from{transform:translate3d(-3%,0,0) scale(1)}to{transform:translate3d(3%,0,0) scale(1.06)}}
.brand .mark{transition:transform var(--t),background var(--t)}
.brand:hover .mark{transform:rotate(-6deg) scale(1.06);background:rgba(255,255,255,.24)}
.shop-search{transition:box-shadow var(--t),transform var(--t)}
.shop-search:focus-within{box-shadow:var(--ring),0 6px 22px rgba(46,16,101,.24);transform:translateY(-1px)}
.shop-actions a{transition:transform var(--t),opacity var(--t)}
.shop-actions a:hover{transform:translateY(-2px)}
.cart-dot{animation:pop .6s var(--ease-out) .5s backwards,beat 2.6s var(--ease) 1.2s infinite}
@keyframes pop{from{transform:scale(0);opacity:0}to{transform:scale(1);opacity:1}}
@keyframes beat{0%,88%,100%{transform:scale(1)}92%{transform:scale(1.22)}96%{transform:scale(1)}}
.shop-nav{transition:box-shadow var(--t)}
html.scrolled .shop-nav{box-shadow:0 6px 20px rgba(46,16,101,.07)}
.shop-nav a{position:relative;transition:color var(--t)}
.shop-nav a::after{content:"";position:absolute;left:0;right:0;bottom:-1px;height:2px;border-radius:2px;background:currentColor;transform:scaleX(0);transform-origin:50% 50%;transition:transform var(--t-slow)}
.shop-nav a:hover::after{transform:scaleX(1)}
.banner::before{content:"";position:absolute;inset:0;border-radius:inherit;background:url("{{ asset('img/shop/hero-bg.jpg') }}") right center/cover no-repeat;animation:kenburns 22s var(--ease) infinite alternate}
.banner::after{content:"";position:absolute;right:-90px;top:-90px;width:340px;height:340px;border-radius:50%;background:rgba(255,255,255,.08);animation:orb 12s var(--ease) infinite alternate}
@keyframes kenburns{from{transform:scale(1) translateX(0)}to{transform:scale(1.07) translateX(-1.5%)}}
@keyframes orb{from{transform:translate3d(0,0,0) scale(1);opacity:.8}to{transform:translate3d(-26px,18px,0) scale(1.16);opacity:1}}
.banner>*{position:relative;z-index:1}
.btn-buy{position:relative;overflow:hidden;transition:transform var(--t),box-shadow var(--t)}
.btn-buy:hover .ic{transform:translateX(3px)}
.btn-buy::after{content:"";position:absolute;top:0;bottom:0;width:45%;left:-60%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.85),transparent);transform:skewX(-18deg);pointer-events:none}
.btn-buy:hover::after{animation:shine .85s var(--ease)}
@keyframes shine{to{left:130%}}
.rise{animation:rise .75s var(--ease-out) backwards}
.promo{transition:transform var(--t),box-shadow var(--t)}
.promo:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
.promo.img .tile{transition:transform var(--t-slow)}
.promo.img:hover .tile{transform:scale(1.06)}
.trust .item .ic{transition:transform var(--t)}
.trust .item:hover .ic{transform:translateY(-3px) scale(1.1)}
.sec-head h2 span{display:inline-block;animation:flicker 3.4s var(--ease) infinite}
@keyframes flicker{0%,100%{transform:scale(1) rotate(0)}45%{transform:scale(1.14) rotate(-6deg)}60%{transform:scale(1) rotate(0)}}
.count b{transition:transform .18s var(--ease),background .18s var(--ease)}
.count b:not(.idle){animation:flip .38s var(--ease)}
@keyframes flip{0%{transform:translateY(0)}45%{transform:translateY(-5px) scale(1.06);background:var(--violet-600)}100%{transform:translateY(0)}}
.count i{animation:blink 1s var(--ease) infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.35}}
.sec-head .more{transition:color var(--t),gap var(--t)}
.sec-head .more:hover{gap:9px}
.card{will-change:transform}
.card::after{content:"";position:absolute;inset:0;border-radius:inherit;pointer-events:none;background:linear-gradient(180deg,rgba(139,92,246,.07),transparent 42%);opacity:0;transition:opacity var(--t)}
.card:hover::after{opacity:1}
.card:hover{border-color:#ddd6fe}
.card{position:relative}
.thumb{transition:transform var(--t-slow)}
.card:hover .thumb{transform:scale(1.05)}
.thumb[style]{background-size:cover;background-position:center}
.fav{transition:color var(--t),transform var(--t),background var(--t)}
.fav:hover{transform:scale(1.14)}
.fav.on{animation:bump .45s var(--ease-out)}
@keyframes bump{0%{transform:scale(1)}35%{transform:scale(1.32)}60%{transform:scale(.92)}100%{transform:scale(1)}}
.off{animation:glow 2.8s var(--ease) infinite}
@keyframes glow{0%,72%,100%{box-shadow:0 0 0 0 rgba(225,29,72,0)}80%{box-shadow:0 0 0 6px rgba(225,29,72,.16)}}
.card .name{transition:color var(--t)}
.card:hover .name{color:var(--violet-800)}
.card .price{transition:transform var(--t)}
.card:hover .price{transform:translateX(2px)}
.card .add{position:relative;overflow:hidden;transition:background var(--t),box-shadow var(--t),transform var(--t)}
.card .add:hover{box-shadow:0 8px 18px rgba(109,40,217,.3)}
.card .add::after{content:"";position:absolute;top:0;bottom:0;width:50%;left:-70%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.5),transparent);transform:skewX(-18deg);pointer-events:none}
.card .add:hover::after{animation:shine .8s var(--ease)}
.cat{transition:border-color var(--t),color var(--t),background var(--t),transform var(--t),box-shadow var(--t)}
.cat:hover{transform:translateY(-3px);box-shadow:0 8px 18px rgba(109,40,217,.13)}
.cat:active{transform:translateY(-1px) scale(.98)}
.cat.hot:hover{background:#fff1f2;border-color:var(--pink);box-shadow:0 8px 18px rgba(225,29,72,.14)}
.shop-footer a{transition:color var(--t),padding-left var(--t);display:inline-block}
.brandline .mark{transition:transform var(--t),background var(--t)}
.brandline:hover .mark{transform:rotate(-6deg);background:rgba(255,255,255,.2)}
/* ============ Recherche — modale identique au HTML (aucun ancien style) ============ */
.sm{position:fixed;inset:0;z-index:120;display:flex;justify-content:center;align-items:flex-start}
.sm[hidden]{display:none}
html.sm-open,html.sm-open body{overflow:hidden}
.sm-veil{position:absolute;inset:0;background:rgba(46,16,101,.42);-webkit-backdrop-filter:blur(7px) saturate(1.1);backdrop-filter:blur(7px) saturate(1.1);opacity:0;transition:opacity .32s var(--ease)}
.sm.on .sm-veil{opacity:1}
.sm-pan{position:relative;width:min(760px,calc(100% - 40px));margin-top:9vh;max-height:78vh;display:flex;flex-direction:column;overflow:hidden;background:#fff;border-radius:22px;box-shadow:var(--shadow-lg),0 0 0 1px var(--line);opacity:0;transform:translateY(26px) scale(.97);transition:opacity .34s var(--ease-out),transform .46s var(--ease-out)}
.sm.on .sm-pan{opacity:1;transform:none}
.sm-pan::before{content:"";position:absolute;top:0;left:0;right:0;height:3px;z-index:3;background:linear-gradient(90deg,var(--violet-800),var(--violet-500) 55%,var(--pink))}
.sm-bar{flex:none;display:flex;align-items:center;gap:12px;background:#fff;padding:16px 16px 14px 20px;border-bottom:1px solid var(--line)}
.sm-bar>.ic{width:22px;height:22px;color:var(--violet-600)}
.sm-bar input{flex:1;min-width:0;border:0;outline:0;background:transparent;font:inherit;font-size:16.5px;color:var(--ink)}
.sm-bar input::placeholder{color:#9ca3af}
.sm-x{flex:none;width:38px;height:38px;border-radius:12px;display:grid;place-items:center;color:var(--grey);transition:background var(--t),color var(--t),transform var(--t)}
.sm-x .ic{width:17px;height:17px;color:currentColor}
.sm-x:hover{background:var(--lav-1);color:var(--violet-700);transform:rotate(90deg)}
.sm-body{flex:1 1 auto;min-height:0;padding:18px 20px 8px;overflow:auto;overscroll-behavior:contain;scrollbar-width:thin;scrollbar-color:var(--lav-2) transparent}
.sm-body::-webkit-scrollbar{width:10px}
.sm-body::-webkit-scrollbar-thumb{background:var(--lav-2);border:3px solid #fff;border-radius:999px}
.sm-h{font-size:11px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:var(--grey);margin-bottom:12px}
.sm-sec+.sm-sec{margin-top:24px}
.chips{display:flex;flex-wrap:wrap;gap:9px}
.pop{border:1px solid var(--line);background:#fff;border-radius:999px;padding:9px 16px;font-size:13.5px;font-weight:500;color:#374151;transition:border-color var(--t),color var(--t),background var(--t),transform var(--t),box-shadow var(--t)}
.pop:hover{border-color:var(--violet-500);color:var(--violet-700);background:var(--lav-1);transform:translateY(-2px);box-shadow:0 8px 18px rgba(109,40,217,.13)}
.pop:active{transform:translateY(0) scale(.97)}
.sm-list{display:flex;flex-direction:column;gap:4px}
.row{display:flex;align-items:center;gap:13px;width:100%;text-align:left;color:inherit;padding:9px 11px;border-radius:14px;transition:background var(--t),transform var(--t)}
.row:hover{background:var(--lav-1);transform:translateX(3px)}
.row .th{flex:none;width:46px;height:46px;border-radius:12px;background:var(--lav-1) center/cover no-repeat;box-shadow:inset 0 0 0 1px var(--line)}
.row .tx{flex:1;min-width:0}
.row .nm{display:block;font-size:14px;font-weight:500;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.row .mt{display:block;font-size:11.5px;color:var(--grey);margin-top:2px}
.row .mt s{color:#9ca3af}
.row .bg{display:inline-block;vertical-align:1px;background:var(--pink);color:#fff;font-size:10.5px;font-weight:700;padding:2px 7px;border-radius:999px;margin-right:6px}
.row .pr{flex:none;font-size:14.5px;font-weight:800;color:var(--pink);letter-spacing:-.2px}
.row>.ic{flex:none;width:16px;height:16px;color:var(--violet-500);opacity:.65}
.row:hover>.ic{transform:translateX(3px);opacity:1}
.row mark{background:var(--lav-2);color:var(--violet-800);border-radius:5px;padding:0 3px}
.sm-empty{padding:26px 8px 34px;text-align:center;color:var(--grey);font-size:14px}
.sm-empty .ic{width:34px;height:34px;color:var(--violet-400);stroke-width:1.4;margin:0 auto 12px}
.sm-empty b{color:var(--ink);font-weight:600}
.sm-empty span{display:block;margin-top:7px;font-size:12.5px}
.sm-foot{flex:none;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;padding:11px 20px;border-top:1px solid var(--line);background:var(--lav-1);font-size:12px;color:var(--grey)}
.sm-foot .k{display:inline-flex;align-items:center;gap:6px}
.sm-foot i{font-style:normal;background:#fff;border:1px solid var(--line);border-radius:6px;padding:2px 7px;font-size:11px;font-weight:600;color:#374151;box-shadow:var(--shadow)}
.sm.on .sm-bar{animation:rise .45s var(--ease-out) both}
.sm.on .sm-sec{animation:rise .5s var(--ease-out) both}
.sm.on .sm-sec:nth-of-type(2){animation-delay:.07s}
.sm.on .sm-list .row{animation:rise .4s var(--ease-out) both}
.card.flash{box-shadow:var(--ring),var(--shadow-lg);border-color:var(--violet-500)}
.more-btn{display:block;margin:18px auto 6px;background:#fff;border:1px solid var(--line);border-radius:999px;padding:11px 26px;font-weight:600;color:var(--violet-700)}
.more-btn:hover{border-color:var(--violet-500);background:var(--lav-1)}
@keyframes rise{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
/* Détail produit — reprise exacte du template boutique (shop-pages.css §4) */
.pdp{display:grid;grid-template-columns:1.05fr 1fr;gap:34px;padding:22px 0 10px;align-items:start}
.pdp-gal{position:sticky;top:78px}
.g-main{position:relative;border:1px solid var(--line);border-radius:var(--radius);overflow:hidden;aspect-ratio:1/1;background:var(--lav-1) center/cover no-repeat;box-shadow:var(--shadow-md)}
.g-main .off{top:14px;left:14px;font-size:12.5px;padding:6px 12px}
.g-main .fav{top:14px;right:14px;width:40px;height:40px}
.g-main .fav .ic{width:20px;height:20px}
.g-nav{position:absolute;top:50%;transform:translateY(-50%);width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,.94);display:grid;place-items:center;box-shadow:var(--shadow-md);color:var(--violet-800);z-index:2}
.g-nav:hover{background:#fff;transform:translateY(-50%) scale(1.06)}
.g-nav.prev{left:12px}.g-nav.next{right:12px}
.g-nav.prev .ic{transform:rotate(180deg)}
.g-thumbs{display:flex;gap:10px;margin-top:12px;flex-wrap:wrap}
.g-th{width:78px;height:78px;border-radius:12px;border:1px solid var(--line);overflow:hidden;background:var(--lav-1) center/cover no-repeat;position:relative}
.g-th:hover{transform:translateY(-2px)}
.g-th.on{border-color:var(--violet-500);box-shadow:var(--ring)}
.g-th span{position:absolute;left:0;right:0;bottom:0;font-size:9.5px;text-align:center;padding:3px 2px;background:rgba(17,24,39,.72);color:#fff;font-weight:600}
.pdp-info .tagline{display:flex;align-items:center;gap:10px;font-size:12.5px;color:var(--violet-700);font-weight:600}
.pdp-info h1{font-size:clamp(24px,2.8vw,36px);font-weight:800;letter-spacing:-.7px;line-height:1.1;margin:10px 0 10px}
.rating-row{display:flex;align-items:center;gap:14px;flex-wrap:wrap;font-size:13px;color:var(--grey)}
.rating-row .stars{display:flex;gap:2px}
.rating-row .stars svg{width:15px;height:15px;stroke:var(--pink);fill:var(--pink);stroke-width:1}
.rating-row .stars svg.empty{stroke:#d1d5db;fill:#e5e7eb}
.rating-row b{color:var(--ink)}
.rating-row .sep2{width:1px;height:14px;background:var(--line)}
.rating-row .stock{margin:0}
.pricebox{display:flex;align-items:baseline;gap:12px;flex-wrap:wrap;margin:18px 0 4px}
.pricebox .now{font-size:34px;font-weight:800;color:var(--pink);letter-spacing:-.8px}
.pricebox .was{font-size:16px;color:#9ca3af;text-decoration:line-through}
.pricebox .save{background:#fff1f2;color:var(--pink);border:1px solid #fecdd3;font-size:12px;font-weight:700;padding:5px 12px;border-radius:999px}
.flash-line{display:flex;align-items:center;gap:10px;margin:6px 0 2px;font-size:13px;color:var(--violet-800)}
.flash-line .count b{font-size:13px;min-width:34px;padding:6px 0}
.flash-line .count i{font-size:13px}
.lead{font-size:14.5px;color:#374151;line-height:1.7;margin:14px 0 6px}
.opt{margin-top:20px}
.opt>label{display:block;font-size:12.5px;font-weight:600;color:var(--violet-900);margin-bottom:10px}
.opt>label b{font-weight:700;color:var(--violet-700)}
.pills{display:flex;gap:9px;flex-wrap:wrap}
.swatches{display:flex;gap:10px;flex-wrap:wrap}
.swatch{width:40px;height:40px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 0 1px var(--line),0 2px 6px rgba(16,24,40,.12);position:relative}
.swatch:hover{transform:scale(1.08)}
.swatch.on{box-shadow:0 0 0 2px var(--violet-500),0 4px 12px rgba(109,40,217,.28)}
.qty-row{display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-top:22px}
.qty-row .hint{font-size:12.5px;color:var(--grey)}
.pdp-actions{display:flex;gap:12px;margin-top:18px;flex-wrap:wrap}
.assurances{display:grid;gap:10px;margin-top:22px;padding:16px;border:1px solid var(--line);border-radius:var(--radius);background:#fff;box-shadow:var(--shadow)}
.assurances div{display:flex;gap:11px;align-items:flex-start;font-size:13px;color:#374151}
.assurances .ic{color:var(--violet-600);margin-top:1px}
.assurances b{display:block;color:var(--ink);font-size:13.5px}
.tabs{margin:26px 0 10px;background:#fff;border:1px solid var(--line);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow)}
.tabs-head{display:flex;gap:4px;border-bottom:1px solid var(--line);padding:6px 8px 0;flex-wrap:wrap}
.tab-btn{font-size:14px;font-weight:600;color:var(--grey);padding:13px 18px;border-radius:10px 10px 0 0;position:relative}
.tab-btn:hover{color:var(--violet-700);background:var(--lav-1)}
.tab-btn.on{color:var(--violet-800)}
.tab-btn.on::after{content:"";position:absolute;left:14px;right:14px;bottom:-1px;height:2px;border-radius:2px;background:var(--violet-600)}
.tabs-body{padding:20px 22px}
.tabs-body p{font-size:14px;color:#374151;line-height:1.8}
.tabs-body p + p{margin-top:12px}
.review{display:flex;gap:14px;padding:16px 0;border-bottom:1px solid var(--line)}
.review:last-child{border-bottom:0}
.review .av{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;font-weight:700;font-size:13px;color:#fff;background:linear-gradient(135deg,var(--violet-600),var(--violet-400));flex:none}
.review b{font-size:13.5px;color:var(--ink)}
.review small{font-size:12px;color:var(--grey)}
.review p{font-size:13.5px;color:#374151;margin-top:6px;line-height:1.7}
.review .stars{display:flex;gap:2px;margin-left:auto}
.review .stars svg{width:13px;height:13px;stroke:var(--pink);fill:var(--pink);stroke-width:1}
.review .stars svg.empty{stroke:#d1d5db;fill:#e5e7eb}
/* Neutralise le panneau .empty sur les étoiles (sinon padding/bordure dashed) */
svg.empty{padding:0;margin:0;border:0;background:transparent;border-radius:0;text-align:left}
.card .rate svg.empty{stroke:#d1d5db;fill:#e5e7eb}
.review .rphotos{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
.review .rphotos img{width:64px;height:64px;object-fit:cover;border-radius:10px;border:1px solid var(--line);box-shadow:var(--shadow);transition:transform var(--t)}
.review .rphotos img:hover{transform:scale(1.08)}
.review .rrep{margin-top:10px;background:var(--lav-1);border:1px solid #ddd6fe;border-radius:10px;padding:10px 14px;font-size:12.5px;color:#4c1d95}
.review .rrep b{display:block;font-size:12px;color:var(--violet-800);margin-bottom:2px}
/* Carte avis premium — dégradé, étoiles glow, dropzone */
.avis-card{margin-top:20px;border:1px solid #ddd6fe;border-radius:16px;background:linear-gradient(180deg,var(--lav-1),#fff 55%);box-shadow:var(--shadow-md);overflow:hidden}
.avis-card-head{display:flex;align-items:center;gap:10px;padding:13px 18px;background:linear-gradient(135deg,var(--violet-700),var(--violet-500));color:#fff}
.avis-card-head .ic{width:19px;height:19px}
.avis-card-head b{font-size:14.5px;font-weight:700}
.avis-card-head span{font-size:12px;opacity:.85;margin-left:auto}
.avis-card-body{padding:18px}
.avis-ok{display:flex;align-items:center;gap:9px;font-size:13px;font-weight:600;color:#047857;background:#d1fae5;border:1px solid #6ee7b7;border-radius:12px;padding:11px 14px;margin-bottom:14px}
.rate-pick{display:flex;gap:6px;align-items:center}
.rate-pick button{background:#fff;border:1px solid var(--line);border-radius:12px;width:46px;height:46px;display:grid;place-items:center;cursor:pointer;transition:transform var(--t),border-color var(--t),box-shadow var(--t)}
.rate-pick button:hover{transform:scale(1.12) rotate(-6deg);border-color:var(--pink)}
.rate-pick button.on{border-color:var(--pink);box-shadow:0 0 0 4px rgba(225,29,72,.14),0 6px 16px rgba(225,29,72,.25)}
.rate-pick button.on svg{filter:drop-shadow(0 3px 5px rgba(225,29,72,.5))}
.rate-pick svg{transition:transform var(--t)}
.dropzone{display:flex;align-items:center;gap:12px;border:1.5px dashed #c4b5fd;background:rgba(255,255,255,.7);border-radius:12px;padding:14px 16px;cursor:pointer;transition:border-color var(--t),background var(--t),transform var(--t);font-size:13px;color:var(--violet-800)}
.dropzone:hover{border-color:var(--violet-500);background:#fff;transform:translateY(-1px)}
.aprev{position:relative;flex:none}
.aprev img{width:68px;height:68px;object-fit:cover;border-radius:12px;border:1px solid var(--line);box-shadow:var(--shadow);display:block}
.aprev button{position:absolute;top:-8px;right:-8px;width:24px;height:24px;border-radius:50%;background:var(--pink);color:#fff;font-size:13px;font-weight:700;display:grid;place-items:center;box-shadow:0 4px 10px rgba(225,29,72,.4);transition:transform var(--t)}
.aprev button:hover{transform:scale(1.15)}
.avis-err{font-size:12px;color:var(--pink);margin-top:6px;display:block}
.sec.mini{padding:22px 0 4px}
.buybar{display:none}
.thumb .ic{width:58px;height:58px;stroke-width:1.2}
.card .rate svg{width:13px;height:13px;stroke:var(--pink);fill:var(--pink);stroke-width:1}
@media(max-width:900px){.pdp{grid-template-columns:1fr;gap:22px}.pdp-gal{position:static}.buybar{display:flex;position:fixed;left:0;right:0;bottom:0;z-index:40;align-items:center;gap:10px;padding:11px 16px calc(11px + env(safe-area-inset-bottom));background:#fff;border-top:1px solid var(--line);box-shadow:0 -8px 24px rgba(46,16,101,.12)}.buybar .now{font-size:19px;font-weight:800;color:var(--pink)}.buybar .btn-solid{flex:1;padding:13px 18px;font-size:14px}body.has-buybar{padding-bottom:74px}.tabs-body{padding:16px}}
.spec{width:100%;border-collapse:collapse;font-size:13.5px}
.spec tr{border-bottom:1px solid var(--line)}
.spec tr:last-child{border-bottom:0}
.spec td{padding:12px 8px}
.spec td:first-child{color:var(--grey);width:42%;font-weight:500}
.spec td:last-child{color:var(--ink)}
.crumb{padding:16px 0 2px}
.crumb .wrap{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--mut);flex-wrap:wrap}.crumb a:hover{color:var(--violet-700)}.crumb .ic{width:14px;height:14px;opacity:.55}.crumb .here{color:var(--violet-800);font-weight:600}
.phead{padding:14px 0 6px}.phead h1{font-size:clamp(24px,3vw,34px);font-weight:800;letter-spacing:-.6px;color:var(--ink)}.phead p{color:var(--mut);font-size:14px;margin-top:6px}
.toolbar{background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px 16px;margin:18px 0;box-shadow:var(--shadow);display:flex;align-items:center;gap:14px;flex-wrap:wrap}
.toolbar .grow{flex:1;min-width:180px}.toolbar label{font-size:12.5px;color:var(--mut);display:flex;align-items:center;gap:7px;cursor:pointer}
.toolbar input[type=checkbox]{width:16px;height:16px;accent-color:var(--violet-600);cursor:pointer}
.ctrl{border:1px solid var(--line);border-radius:999px;padding:10px 16px;font:inherit;font-size:13.5px;color:var(--ink);background:#fff}
select.ctrl{cursor:pointer;padding-right:34px;appearance:none;background-image:linear-gradient(45deg,transparent 50%,var(--violet-600) 50%),linear-gradient(135deg,var(--violet-600) 50%,transparent 50%);background-position:calc(100% - 19px) 18px,calc(100% - 14px) 18px;background-size:5px 5px,5px 5px;background-repeat:no-repeat}
.ctrl:focus{outline:0;border-color:var(--violet-500);box-shadow:0 0 0 4px rgba(139,92,246,.18)}
.chips-row{display:flex;gap:10px;flex-wrap:wrap}
.grid.plist{grid-template-columns:repeat(4,1fr)}
@media(max-width:1200px){.grid.plist{grid-template-columns:repeat(3,1fr)}}
@media(max-width:900px){.grid.plist{grid-template-columns:repeat(2,1fr);gap:14px}}
.card .rate{display:flex;align-items:center;gap:6px;font-size:11.5px;color:var(--mut)}
.card .stock{font-size:11.5px;font-weight:600}.card .stock.ok{color:#059669}.card .stock.low{color:#b45309}.card .stock.out{color:var(--pink)}
.card .prices{display:flex;align-items:baseline;gap:8px}.card.sold img{opacity:.55}
.panel{background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow);padding:18px 20px}
.panel h2{font-size:16.5px;font-weight:700;display:flex;align-items:center;gap:9px;margin-bottom:14px}
.panel h2 .ic{color:var(--violet-600)}.panel + .panel{margin-top:16px}
.cline{display:grid;grid-template-columns:104px 1fr auto;gap:16px;padding:16px 0;border-bottom:1px solid var(--line);align-items:center}
.cline:last-child{border-bottom:0}.cline .th{width:104px;height:104px;border-radius:12px;border:1px solid var(--line);background:var(--lav-2) center/cover no-repeat;display:block}
.cline h3{font-size:14.5px;font-weight:600}.cline h3 a:hover{color:var(--violet-700)}
.cline .opts{font-size:12.5px;color:var(--mut);margin-top:5px}.cline .unit{font-size:12.5px;color:var(--mut);margin-top:6px}
.cline .rm{display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600;color:var(--pink);margin-top:10px;background:none;border:0;cursor:pointer;padding:0}
.cline .rm:hover{text-decoration:underline}.cline .lt{font-size:16px;font-weight:800;color:var(--pink);text-align:right;white-space:nowrap}
.stepper{display:flex;align-items:center;border:1px solid var(--line);border-radius:999px;background:#fff;overflow:hidden}
.stepper button{width:42px;height:46px;display:grid;place-items:center;color:var(--violet-800)}
.stepper button:hover{background:var(--lav-1)}
.stepper button .ic{width:15px;height:15px}
.stepper input{width:54px;height:46px;text-align:center;border:0;font:inherit;font-weight:700;color:var(--ink)}
.stepper input:focus{outline:0;background:var(--lav-1)}
.cline .stepper{margin-top:10px}
.cline .stepper button{width:36px;height:38px}
.cline .stepper input{width:46px;height:38px;font-size:14px}
.summary{position:sticky;top:78px}
.srow{display:flex;justify-content:space-between;gap:10px;font-size:14px;color:#374151;padding:8px 0}.srow b{color:var(--ink)}.srow.ok b{color:#059669}
.stotal{display:flex;justify-content:space-between;align-items:baseline;gap:10px;border-top:1px solid var(--line);margin-top:10px;padding-top:14px}
.stotal span{font-size:13px;font-weight:600;color:var(--mut);text-transform:uppercase;letter-spacing:.6px}.stotal b{font-size:24px;font-weight:800;color:var(--pink)}
.bar{height:8px;border-radius:999px;background:var(--lav-2);overflow:hidden;margin:10px 0 8px}
.bar i{display:block;height:100%;border-radius:999px;background:linear-gradient(90deg,var(--violet-600),var(--violet-400))}
.promo-row{display:flex;gap:9px;margin-top:14px}.promo-row .ctrl{flex:1;border-radius:12px;min-width:0}
.promo-msg{font-size:12.5px;margin-top:9px;display:none}.promo-msg.ok{color:#059669;display:block}.promo-msg.ko{color:var(--pink);display:block}
.empty{text-align:center;padding:56px 20px;background:#fff;border:1px dashed var(--line);border-radius:14px;margin:20px 0}
.empty .ic{width:46px;height:46px;color:var(--violet-400);margin:0 auto 14px}.empty h3{font-size:20px;font-weight:700}.empty p{color:var(--mut);font-size:14px;margin-top:8px}
.btn-solid{display:inline-flex;align-items:center;justify-content:center;gap:9px;background:var(--violet-600);color:#fff;font-weight:700;font-size:15px;padding:15px 30px;border-radius:999px;border:0;cursor:pointer;text-decoration:none;position:relative;overflow:hidden}
.btn-solid:hover{background:var(--violet-700);transform:translateY(-2px);box-shadow:0 12px 28px rgba(109,40,217,.32)}
.btn-solid::after{content:"";position:absolute;top:0;bottom:0;width:45%;left:-60%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.45),transparent);transform:skewX(-18deg);pointer-events:none}
.btn-solid:hover::after{animation:shine .85s var(--ease)}
.btn-line{display:inline-flex;align-items:center;justify-content:center;gap:9px;background:#fff;color:var(--violet-800);border:1px solid var(--line);font-weight:600;font-size:14.5px;padding:15px 26px;border-radius:999px;cursor:pointer;text-decoration:none}
.btn-line:hover{border-color:var(--violet-500);color:var(--violet-700);transform:translateY(-2px)}
.btn-line.on{color:var(--pink);border-color:#fecdd3;background:#fff1f2}
.btn-line.on .ic{fill:currentColor}
.btn-ghost-sm{display:inline-flex;align-items:center;gap:7px;font-size:13px;font-weight:600;color:var(--violet-700);border:1px solid var(--line);background:#fff;border-radius:999px;padding:9px 16px;cursor:pointer;text-decoration:none}
.btn-ghost-sm:hover{border-color:var(--violet-500);transform:translateY(-1px)}
.steps{display:flex;gap:8px;background:var(--lav-2);border:1px solid var(--line);border-radius:999px;padding:5px;margin-bottom:18px}
.steps div{flex:1;text-align:center;font-size:12.5px;font-weight:600;color:var(--mut);padding:9px 6px;border-radius:999px}
.steps div.on{background:linear-gradient(135deg,var(--violet-600),var(--violet-400));color:#fff;box-shadow:0 6px 16px rgba(109,40,217,.28)}
.field{margin-bottom:13px}.field label{display:block;font-size:12.5px;font-weight:600;color:var(--violet-800);margin-bottom:7px}
.field .ctrl{width:100%;border-radius:12px}.field.bad .ctrl{border-color:var(--pink)}.field .err{font-size:12px;color:var(--pink);margin-top:6px;display:none}.field.bad .err{display:block}
.pay-pills{display:flex;gap:10px;flex-wrap:wrap}
.opt-pill{border:1px solid var(--line);background:#fff;border-radius:11px;padding:10px 15px;font-size:13px;color:#374151;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:8px}
.opt-pill:hover{border-color:var(--violet-500);color:var(--violet-700);transform:translateY(-1px)}
.opt-pill.on{border-color:var(--violet-600);background:var(--lav-1);color:var(--violet-800);font-weight:600;box-shadow:var(--ring)}
.opt-pill small{color:var(--pink);font-weight:700;margin-left:4px}
.pay-pills .opt-pill{display:flex;align-items:center;gap:8px}
.recap .mini{display:grid;grid-template-columns:56px 1fr auto;gap:12px;align-items:center;padding:10px 0;border-bottom:1px solid var(--line)}
.recap .mini:last-child{border-bottom:0}.recap .mini .th{width:56px;height:56px;border-radius:10px;border:1px solid var(--line);background:var(--lav-2) center/cover no-repeat}
.recap .mini b{font-size:13px;display:block}.recap .mini small{font-size:11.5px;color:var(--mut)}.recap .mini span{font-size:13px;font-weight:700;color:var(--pink)}
.done{text-align:center;padding:34px 20px}.done .badge-ok{width:76px;height:76px;border-radius:50%;margin:0 auto 16px;display:grid;place-items:center;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.4);color:#059669}.done .badge-ok .ic{width:36px;height:36px}
.dash-head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin:18px 0}
.dash-who{display:flex;align-items:center;gap:14px}
.av-lg{width:64px;height:64px;border-radius:50%;display:grid;place-items:center;color:#fff;font-weight:800;font-size:22px;background:linear-gradient(135deg,var(--violet-700),var(--violet-400));flex:none}
.dash-head h1{font-size:clamp(21px,2.4vw,28px);font-weight:800;letter-spacing:-.5px}.dash-head p{font-size:13px;color:var(--mut);margin-top:3px}
.dash-narrow{max-width:520px;margin:22px auto 10px}
.kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin:16px 0 20px}
.kpi{background:#fff;border:1px solid var(--line);border-radius:14px;padding:16px 18px;box-shadow:var(--shadow);display:flex;flex-direction:column;gap:4px}
.kpi:hover{transform:translateY(-3px)}.kpi .kpi-l{font-size:12.5px;font-weight:600;color:var(--mut)}.kpi b{font-size:24px;font-weight:800;color:var(--violet-800);letter-spacing:-.6px}.kpi .kpi-n{font-size:11.5px;color:#9ca3af}
@media(max-width:900px){.kpis{grid-template-columns:1fr 1fr}.summary{position:static}}
@media(max-width:520px){.kpis{grid-template-columns:1fr}.cline{grid-template-columns:72px 1fr}.cline .th{width:72px;height:72px}.cline .lt{grid-column:2;text-align:left}}
.dash-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:18px;align-items:start;margin-bottom:18px}
@media(max-width:900px){.dash-grid{grid-template-columns:1fr}}
.h3-mini{font-size:14px;font-weight:700;color:var(--violet-800);margin-bottom:8px}
.st{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:5px 11px;border-radius:999px;white-space:nowrap}
.st::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.st.conf{background:#ede9fe;color:#6d28d9}.st.prep{background:#fef3c7;color:#b45309}.st.exp{background:#e0f2fe;color:#0369a1}.st.ok{background:#d1fae5;color:#047857}.st.ko{background:#ffe4e6;color:var(--pink)}
.ocmd{border:1px solid var(--line);border-radius:12px;padding:13px 15px;margin-bottom:12px;background:#fff}
.ocmd:hover{border-color:#ddd6fe}.ocmd-top{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}.ocmd-top b{font-size:13.5px}
.ocmd-lignes{font-size:12.5px;color:var(--mut);margin-top:7px;line-height:1.6}
.ocmd-foot{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:9px;padding-top:9px;border-top:1px dashed var(--line);font-size:12.5px;color:#374151}
.ocmd-foot b{color:var(--pink);font-size:14px}
.muted-sm{font-size:12.5px;color:var(--mut)}
.badge-nb{background:var(--lav-2);color:var(--violet-800);font-size:12px;font-weight:700;border-radius:999px;padding:3px 10px;margin-left:6px}
.dligne{display:grid;grid-template-columns:56px 1fr auto;gap:12px;align-items:center;padding:10px 0;border-bottom:1px solid var(--line)}
.dligne:last-child{border-bottom:0}.dligne .th{width:56px;height:56px;border-radius:10px;border:1px solid var(--line);background:var(--lav-2) center/cover no-repeat;display:block}
.dligne b{font-size:13px;display:block}.dligne small{font-size:11.5px;color:var(--mut)}
.lien{color:var(--violet-700);font-weight:700;text-decoration:underline;font-size:12.5px;background:none;border:0;cursor:pointer;padding:0}
.liste-check{list-style:none;display:grid;gap:9px}.liste-check li{font-size:13px;color:#374151;padding-left:22px;position:relative;line-height:1.6}
.liste-check li::before{content:"✓";position:absolute;left:0;color:var(--violet-600);font-weight:800}
.suivi-form{display:flex;gap:10px;flex-wrap:wrap}.suivi-form .ctrl{flex:1;min-width:220px;border-radius:12px}
.timeline{display:flex;align-items:flex-start;gap:0;margin:20px 0 6px;overflow-x:auto;padding-bottom:6px}
.tl-step{display:flex;align-items:center;gap:0;min-width:0;flex:1;position:relative}
.tl-dot{width:36px;height:36px;border-radius:50%;display:grid;place-items:center;font-size:13px;font-weight:700;flex:none;background:#fff;border:2px solid var(--line);color:var(--mut);z-index:1}
.tl-step.fait .tl-dot{background:var(--violet-600);border-color:var(--violet-600);color:#fff}
.tl-step.actuel .tl-dot{background:#fff;border-color:var(--violet-600);color:var(--violet-700)}
.tl-step .tl-dot .ic{width:18px;height:18px}
.tl-txt{display:flex;flex-direction:column;margin-left:10px;min-width:0}
.tl-txt b{font-size:12.5px;font-weight:700;color:var(--violet-800);white-space:nowrap}
.tl-txt small{font-size:11px;color:var(--mut);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:170px}
.tl-step.a-venir .tl-txt b{color:#9ca3af;font-weight:600}
.tl-bar{position:absolute;left:36px;right:0;top:18px;height:2px;background:var(--line)}
.tl-step.fait .tl-bar{background:var(--violet-600)}
@media(max-width:640px){.timeline{flex-direction:column;gap:2px;overflow:visible}.tl-step{width:100%;align-items:flex-start}.tl-bar{left:17px;right:auto;top:36px;bottom:0;width:2px;height:auto}.tl-txt{margin-bottom:14px}}
.pager{display:flex;justify-content:center;gap:8px;margin:26px 0 8px;flex-wrap:wrap}
.pager button{min-width:40px;height:40px;border-radius:11px;border:1px solid var(--line);background:#fff;color:#374151;font-weight:600;font-size:13.5px;cursor:pointer}
.pager button:hover{border-color:var(--violet-500);color:var(--violet-700)}
.pager button.on{background:linear-gradient(135deg,var(--violet-600),var(--violet-400));color:#fff;border-color:transparent}
.pager button[disabled]{opacity:.45;cursor:not-allowed}
.tagline-band{background:var(--lav-2);border:1px solid #ddd6fe;border-radius:12px;padding:12px 15px;font-size:13px;color:var(--violet-800);display:flex;gap:10px;align-items:center;margin:16px 0}
.tagline-band .ic{color:var(--violet-600)}
.siblings{display:flex;align-items:center;justify-content:space-between;gap:10px;background:#fff;border:1px solid var(--line);border-radius:12px;padding:9px 13px;margin-bottom:16px;box-shadow:var(--shadow)}
.sib{display:flex;flex-direction:column;min-width:0;max-width:40%}
.sib:hover{color:var(--violet-700);transform:translateX(-2px)}
.sib.next{align-items:flex-end;text-align:right}
.sib.next:hover{transform:translateX(2px)}
.sib .sib-k{font-size:12.5px;font-weight:700;color:var(--violet-700)}
.sib .sib-n{font-size:11.5px;color:var(--grey);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%}
.sib-c{font-size:12px;font-weight:700;color:var(--grey);white-space:nowrap}
@media(max-width:560px){.sib{max-width:44%}.sib .sib-n{font-size:10.5px}.sib-c{display:none}}
.sib{display:flex;flex-direction:column;min-width:0;max-width:40%}.sib:hover{color:var(--violet-700)}.sib.next{align-items:flex-end;text-align:right}
.sib .sib-k{font-size:12.5px;font-weight:700;color:var(--violet-700)}.sib .sib-n{font-size:11.5px;color:var(--mut);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%}
.sib-c{font-size:12px;font-weight:700;color:var(--mut);white-space:nowrap}
@media(max-width:560px){.sib{max-width:44%}.sib .sib-n{font-size:10.5px}.sib-c{display:none}}
.cart-grid{display:grid;grid-template-columns:1.55fr .95fr;gap:26px;align-items:start;margin:20px 0 10px}
@media(max-width:900px){.cart-grid{grid-template-columns:1fr}}
.hactions a.acc{color:#fff;position:relative;display:flex;align-items:center;opacity:.95}
.hactions a.acc:hover{opacity:1;transform:translateY(-2px)}
@media(max-width:640px){.shop-nav .nav-row{gap:16px;min-height:48px;padding:0 16px}.shop-nav a{font-size:13px}}
/* responsive */
@media(max-width:1180px){.grid{grid-template-columns:repeat(3,1fr)}.shop-footer .f-cols{grid-template-columns:1fr 1fr 1fr}}
@media(max-width:900px){.hero-grid{grid-template-columns:1fr}.banner{min-height:0;padding:30px 26px}.header-row{flex-wrap:wrap;gap:14px}.shop-search.desktop{display:none}.shop-search-mobile{display:flex}.shop-actions{margin-left:auto}.shop-nav .nav-row{justify-content:flex-start;gap:24px;padding:0 20px}.hide-m{display:none}.brand-name{display:none}}
@media(max-width:640px){.grid{grid-template-columns:repeat(2,1fr);gap:12px}.sec-head{flex-wrap:wrap;gap:12px}.promos{grid-template-columns:1fr}.promo.img{display:none}.banner{background:linear-gradient(160deg,rgba(76,29,149,.95) 0%,rgba(109,40,217,.86) 60%,rgba(168,85,247,.7) 100%),var(--violet-800) url("{{ asset('img/shop/hero-bg.jpg') }}") center/cover no-repeat}.promo.light{background:linear-gradient(120deg,rgba(242,238,254,.97) 0%,rgba(242,238,254,.9) 55%,rgba(242,238,254,.75) 100%),var(--lav-2) url("{{ asset('img/shop/soldes-bg.jpg') }}") right center/cover no-repeat}.promo.solid{background:linear-gradient(120deg,rgba(76,29,149,.95) 0%,rgba(109,40,217,.85) 55%,rgba(168,85,247,.72) 100%),var(--violet-800) url("{{ asset('img/shop/nouveautes-bg.jpg') }}") right center/cover no-repeat}.shop-footer .f-cols{grid-template-columns:1fr 1fr}.sm-pan{width:100%;margin-top:0;height:100%;max-height:100%;border-radius:0}}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.001ms!important;transition-duration:.001ms!important}.rv{opacity:1;transform:none}}
/* ===== Favoris ultra-premium (override) ===== */
.card{position:relative}
.card .thumb{position:relative}
.fav{position:absolute!important;top:10px!important;right:10px!important;width:38px!important;height:38px!important;border-radius:14px!important;background:rgba(255,255,255,.82)!important;-webkit-backdrop-filter:blur(10px) saturate(1.3);backdrop-filter:blur(10px) saturate(1.3);display:grid!important;place-items:center;color:#9ca3af!important;z-index:3!important;border:1px solid rgba(255,255,255,.9)!important;box-shadow:0 6px 18px rgba(16,24,40,.14),inset 0 1px 0 rgba(255,255,255,.9)!important;transition:transform .25s cubic-bezier(.22,.61,.36,1),color .2s,background .2s,box-shadow .25s,border-color .2s!important;cursor:pointer}
.fav::before{content:"";position:absolute;inset:-2px;border-radius:16px;padding:2px;background:linear-gradient(135deg,#7c3aed,#e11d48);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:0;transition:opacity .25s;pointer-events:none}
.fav:hover{transform:translateY(-2px) scale(1.08)!important;color:#e11d48!important;background:#fff!important;box-shadow:0 12px 26px rgba(225,29,72,.25)!important}
.fav:hover::before{opacity:.55}
.fav:active{transform:scale(.92)!important}
.fav .ic{width:18px!important;height:18px!important;stroke-width:2!important;transition:transform .25s,fill .2s}
.fav:hover .ic{transform:scale(1.15)}
.fav.on{color:#fff!important;background:linear-gradient(135deg,#e11d48,#f43f5e)!important;border-color:transparent!important;box-shadow:0 10px 24px rgba(225,29,72,.45),inset 0 1px 0 rgba(255,255,255,.35)!important;animation:bump .45s cubic-bezier(.16,1,.3,1)}
.fav.on::before{opacity:0}
.fav.on .ic{fill:#fff!important;stroke:#fff!important;filter:drop-shadow(0 2px 4px rgba(0,0,0,.25))}
.fav .tip{position:absolute;top:calc(100% + 8px);right:0;background:#111827;color:#fff;font-size:11px;font-weight:600;padding:6px 10px;border-radius:9px;white-space:nowrap;opacity:0;pointer-events:none;transform:translateY(-4px);transition:.2s;box-shadow:0 8px 20px rgba(0,0,0,.25)}
.fav .tip::before{content:"";position:absolute;bottom:100%;right:14px;border:5px solid transparent;border-bottom-color:#111827}
.fav:hover .tip,.fav:focus-visible .tip{opacity:1;transform:none}
.g-main .fav{top:14px!important;right:14px!important;width:44px!important;height:44px!important;border-radius:15px!important}
.g-main .fav .ic{width:21px!important;height:21px!important}
.fav[wire\:loading]{opacity:.6;pointer-events:none}
/* ===== Caractéristiques premium sur PDP ===== */
.spec-chips{display:flex;flex-wrap:wrap;gap:9px;margin-top:4px}
.spec-chip{display:inline-flex;align-items:center;gap:8px;background:#fff;border:1px solid var(--line);border-radius:14px;padding:9px 13px;font-size:12.5px;box-shadow:var(--shadow);transition:.2s;cursor:default}
.spec-chip:hover{border-color:#c4b5fd;transform:translateY(-2px);box-shadow:0 10px 22px rgba(109,40,217,.14)}
.spec-chip .k{color:var(--grey);font-weight:500}
.spec-chip .v{color:var(--ink);font-weight:700}
.spec-chip .e{width:30px;height:30px;border-radius:10px;display:grid;place-items:center;background:linear-gradient(135deg,#ede9fe,#f5f3ff);color:#6d28d9;flex:none}
.spec-chip .e .ic{width:16px;height:16px}
.spec-more{margin-top:12px;display:inline-flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:var(--violet-700)}
.spec-more:hover{color:var(--violet-900);gap:10px}
.pills .pill-opt{border:1.5px solid var(--line);background:#fff;border-radius:999px;padding:9px 17px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;transition:.2s}
.pills .pill-opt:hover{border-color:#a78bfa;color:#6d28d9;transform:translateY(-1px)}
.pills .pill-opt.on{border-color:#7c3aed;background:#f5f3ff;color:#4c1d95;box-shadow:0 0 0 4px rgba(139,92,246,.13)}
/* ===== Switch "Se souvenir" (template compte) ===== */
.switch{display:inline-flex;align-items:center;gap:9px;font-size:13px;color:#374151;cursor:pointer;user-select:none}
.switch input{width:18px;height:18px;accent-color:var(--violet-600);cursor:pointer;margin:0}
/* ===== Modale connexion / compte (template) ===== */
.modal{position:fixed;inset:0;z-index:120;display:none;align-items:center;justify-content:center;padding:20px;background:rgba(17,24,39,.55);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px)}
.modal.on{display:flex}
.modal .box{background:#fff;border-radius:18px;padding:26px;width:100%;max-width:440px;box-shadow:var(--shadow-lg);animation:rise .3s var(--ease-out) both;max-height:88vh;overflow:auto}
.modal .box h2{font-size:21px;font-weight:800;letter-spacing:-.4px}
.modal .box p.sub{font-size:13.5px;color:var(--grey);margin-top:6px}
.modal .box .foot{display:flex;gap:10px;margin-top:18px}
.modal .demo{margin-top:16px;border:1px dashed #ddd6fe;background:var(--lav-1);border-radius:12px;padding:13px;font-size:12.5px;color:#4c1d95}
.modal .demo code{background:#fff;border-radius:6px;padding:2px 7px;font-size:12.5px;color:var(--violet-800)}
.modal .box .field .err{display:none;font-size:12px;color:var(--pink);margin-top:6px}
.modal .box .field.bad .ctrl{border-color:var(--pink)}
.modal .box .field.bad .err{display:block}
/* ===== Bouton WhatsApp cartes (uniquement listings) ===== */
.card-cta{display:flex;gap:8px;margin-top:auto}
.card-cta .add{flex:1}
.wa-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;background:#22c55e;color:#fff;font-size:12.5px;font-weight:700;padding:10px 12px;border-radius:9px;flex:none;transition:.2s}
.wa-btn:hover{background:#16a34a;transform:translateY(-1px);box-shadow:0 8px 18px rgba(34,197,94,.35)}
.wa-btn .ic{width:16px;height:16px;fill:currentColor;stroke:none}
/* ===== Bouton WhatsApp fiche produit (même base que Favoris .btn-line) ===== */
.btn-line .ic-wa{fill:#22c55e;stroke:none}
.btn-line:hover .ic-wa{fill:#16a34a;stroke:none}
/* ===== Options sélectionnables fiche produit ===== */
.opt-pills{display:flex;gap:9px;flex-wrap:wrap;margin-top:2px}
.opt-pill{border:1.5px solid var(--line);background:#fff;border-radius:11px;padding:10px 15px;font-size:13px;color:#374151;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:.2s}
.opt-pill:hover{border-color:var(--violet-500);color:var(--violet-700);transform:translateY(-1px)}
.opt-pill.on{border-color:var(--violet-600);background:var(--lav-1);color:var(--violet-800);font-weight:700;box-shadow:0 0 0 4px rgba(139,92,246,.14)}
.opt-pill small{color:var(--grey);font-weight:500}
.opt-pill.on small{color:var(--violet-700)}
.opt-grp{margin-top:18px}
.opt-grp>label{display:block;font-size:12.5px;font-weight:600;color:var(--violet-900);margin-bottom:9px}
.opt-grp>label b{font-weight:700;color:var(--violet-700)}
.sel-recap{margin-top:14px;background:var(--lav-1);border:1px solid #ddd6fe;border-radius:12px;padding:11px 14px;font-size:12.5px;color:var(--violet-800);display:flex;gap:9px;align-items:center;flex-wrap:wrap}
.sel-recap .ic{color:var(--violet-600);flex:none}

/* ===== Admin boutique (tableaux, stocks, top ventes — template) ===== */
.table-scroll{overflow-x:auto}
.tbl{width:100%;border-collapse:collapse;font-size:13px;min-width:620px}
.tbl th{text-align:left;font-size:11.5px;text-transform:uppercase;letter-spacing:.7px;color:var(--grey);font-weight:700;padding:9px 10px;border-bottom:1px solid var(--line);white-space:nowrap}
.tbl td{padding:11px 10px;border-bottom:1px solid var(--line);vertical-align:middle}
.tbl tr:last-child td{border-bottom:0}
.tbl tr:hover td{background:var(--lav-1)}
.ctrl-sm{padding:7px 10px;font-size:12.5px;border-radius:9px;width:auto}
.stock-ctl{display:flex;align-items:center;gap:7px}
.mini-btn{width:26px;height:26px;border-radius:8px;border:1px solid var(--line);background:#fff;color:var(--violet-700);font-weight:800;line-height:1;display:grid;place-items:center;transition:var(--t)}
.mini-btn:hover{background:var(--lav-1);border-color:var(--violet-500)}
.stk{min-width:34px;text-align:center;font-weight:800;border-radius:8px;padding:3px 7px;font-size:12.5px}
.stk.ok{background:#d1fae5;color:#047857}
.stk.low{background:#fef3c7;color:#b45309}
.stk.out{background:#ffe4e6;color:var(--pink)}
.top-prod{display:flex;align-items:center;gap:11px;padding:9px 0;border-bottom:1px solid var(--line)}
.top-prod:last-of-type{border-bottom:0}
.top-prod .rang{width:24px;height:24px;border-radius:8px;background:var(--lav-2);color:var(--violet-800);font-weight:800;font-size:12px;display:grid;place-items:center;flex:none}
.top-prod .tp-info{flex:1;min-width:0}
.top-prod .tp-info b{font-size:12.5px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.top-prod .bar{margin-top:5px}
.top-prod .tp-nb{font-size:12.5px;font-weight:800;color:var(--violet-800)}
/* ===== Nav admin (même esprit que la nav boutique) ===== */
.admin-nav{border-bottom:1px solid var(--line);background:var(--violet-900);position:sticky;top:0;z-index:20}
.admin-nav .nav-row{display:flex;align-items:center;gap:6px;min-height:52px;overflow-x:auto;scrollbar-width:none}
.admin-nav .nav-row::-webkit-scrollbar{display:none}
.admin-nav a{font-size:13px;font-weight:600;color:#c4b5fd;white-space:nowrap;padding:8px 14px;border-radius:999px}
.admin-nav a:hover{color:#fff;background:rgba(255,255,255,.12)}
.admin-nav a.hot{background:#fff;color:var(--violet-800)}
/* ===== Texte riche (pages info admin) ===== */
.boutique-texte h4{font-size:15.5px;font-weight:700;margin:18px 0 8px;color:var(--violet-900)}
.boutique-texte h4:first-child{margin-top:0}
.boutique-texte p{font-size:14px;color:#374151;line-height:1.75;margin-bottom:10px}
.boutique-texte ul{margin:6px 0 12px 20px;font-size:14px;color:#374151;line-height:1.75}
.boutique-texte a{color:var(--violet-700);font-weight:600;text-decoration:underline}
/* ===== Correctifs responsive mobile (panier + modales) ===== */
@media(max-width:560px){
.steps{gap:6px;padding:4px}
.steps div{font-size:11px;padding:8px 4px;min-width:0}
.lux-foot{flex-direction:column}
.lux-foot>*{width:100%}
.lux-item{flex-wrap:wrap}
.lux-item .pr{margin-left:auto}
.stotal{flex-wrap:wrap;gap:6px}
.stotal b{font-size:21px}
.srow{flex-wrap:wrap;gap:6px}
.cline{gap:12px}
.pdp-actions .btn-solid{font-size:14px}
}
@media(max-width:380px){
.cline{grid-template-columns:64px 1fr;gap:10px}
.cline .th{width:64px;height:64px}
.cline .stepper button{width:32px;height:36px}
.cline .stepper input{width:40px;height:36px;font-size:13px}
.cline .lt{font-size:14px}
.lux-head h3{font-size:16px}
.lux-ico{width:44px;height:44px}
}
</style>

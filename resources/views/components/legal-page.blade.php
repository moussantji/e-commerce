@props([
    'title',
    'eyebrow' => 'Informations légales',
    'icon' => 'i-b2-info',
    'description' => null,
    'updated' => '22 octobre 2026',
    'updatedIso' => '2026-10-22',
    'sections' => [],
    'otherRoute' => null,
    'otherLabel' => null,
])

@php
    $plainText = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $slot)));
    $words = $plainText === '' ? [] : preg_split('/\s+/u', $plainText);
    $readingMinutes = max(1, (int) ceil(count($words) / 200));
@endphp

@include('section-begin')

<style>
.legal-page{padding:26px 20px 64px;max-width:1000px}
.legal-crumb{display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin:0 0 14px;padding:0 2px;font-size:12.5px;color:var(--mut)}
.legal-crumb a{color:var(--mut);font-weight:500;transition:color var(--t)}
.legal-crumb a:hover{color:var(--violet-700)}
.legal-crumb .ic{width:13px;height:13px;color:#b6a8d9;flex:none}
.legal-crumb span{color:var(--violet-800);font-weight:600}
.legal-hero{position:relative;overflow:hidden;padding:clamp(26px,4vw,38px) clamp(22px,4vw,38px);border:1px solid var(--line);border-radius:22px;background:radial-gradient(480px 210px at 100% 0%,var(--lav-2),transparent 72%),linear-gradient(180deg,#fff,#fcfbff);box-shadow:var(--shadow)}
.legal-eyebrow{display:inline-flex;align-items:center;gap:7px;padding:7px 13px;border:1px solid var(--line);border-radius:999px;background:#fff;color:var(--violet-700);font-size:11.5px;font-weight:700;letter-spacing:.5px;text-transform:uppercase}
.legal-eyebrow .ic{width:14px;height:14px}
.legal-hero h1{max-width:720px;margin:14px 0 10px;color:var(--violet-900);font-size:clamp(28px,4.2vw,42px);font-weight:800;line-height:1.1;letter-spacing:-.7px}
.legal-sub{max-width:640px;margin:0;color:var(--mut);font-size:14.5px;line-height:1.7}
.legal-meta{display:flex;align-items:center;flex-wrap:wrap;gap:8px 14px;margin-top:18px;color:var(--mut);font-size:12.5px}
.legal-meta span{display:inline-flex;align-items:center;gap:6px}
.legal-meta .ic{width:14px;height:14px;color:var(--violet-500);flex:none}
.legal-meta time{font-weight:600;color:#4b5563}
.legal-dot{width:3px;height:3px;border-radius:50%;background:#c4b5fd}
.legal-actions{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin-top:22px}
.legal-actions .btn-line{padding:10px 18px;font-size:13px}
.legal-crosslink{display:inline-flex;align-items:center;gap:5px;padding:9px 6px;border-radius:8px;color:var(--violet-700);font-size:12.5px;font-weight:600}
.legal-crosslink .ic{width:14px;height:14px;transition:transform var(--t)}
.legal-crosslink:hover{color:var(--violet-900)}
.legal-crosslink:hover .ic{transform:translateX(2px)}
.legal-layout{display:grid;grid-template-columns:260px minmax(0,1fr);gap:24px;align-items:start;margin-top:24px}
.legal-aside{position:sticky;top:72px}
.legal-toc{padding:17px 15px;border:1px solid var(--line);border-radius:16px;background:#fff;box-shadow:var(--shadow)}
.legal-toc-title{display:flex;align-items:center;gap:8px;margin:0 0 10px;padding:0 7px;color:var(--violet-700);font-size:11.5px;font-weight:800;letter-spacing:.07em;text-transform:uppercase}
.legal-toc-title .ic{width:14px;height:14px;flex:none}
.legal-toc ol{display:grid;gap:1px;margin:0;padding:0;list-style:none}
.legal-toc a{display:flex;align-items:baseline;gap:9px;padding:7px 8px;border-radius:8px;color:var(--mut);font-size:12.5px;line-height:1.4;transition:background var(--t),color var(--t)}
.legal-toc a:hover{background:var(--lav-1);color:var(--violet-700)}
.legal-toc-n{min-width:15px;color:#a78bfa;font-size:9.5px;font-weight:700;letter-spacing:.03em}
.legal-help{margin-top:12px;padding:16px;border:1px solid var(--lav-2);border-radius:16px;background:linear-gradient(180deg,var(--lav-1),#fff)}
.legal-help b{display:flex;align-items:center;gap:7px;margin-bottom:5px;color:var(--violet-900);font-size:12.5px}
.legal-help b .ic{width:14px;height:14px;color:var(--violet-600);flex:none}
.legal-help p{margin:0 0 11px;color:var(--mut);font-size:12px;line-height:1.6}
.legal-help a{display:flex;align-items:center;gap:7px;padding:5px 0;color:var(--violet-700);font-size:12px;font-weight:600;overflow-wrap:anywhere}
.legal-help a:hover{color:var(--violet-900)}
.legal-help a .ic{width:14px;height:14px;flex:none}
.legal-article{padding:clamp(22px,3.4vw,34px);border:1px solid var(--line);border-radius:18px;background:#fff;box-shadow:var(--shadow)}
.legal-section{scroll-margin-top:76px}
.legal-section+.legal-section{margin-top:28px;padding-top:28px;border-top:1px solid var(--lav-1)}
.legal-section h2{display:flex;align-items:center;gap:11px;margin:0 0 10px;color:var(--violet-900);font-size:clamp(18px,2vw,20px);font-weight:800;line-height:1.3;letter-spacing:-.25px}
.legal-num{display:inline-grid;place-items:center;flex:none;min-width:29px;height:24px;padding:0 6px;border:1px solid var(--lav-2);border-radius:7px;background:var(--lav-1);color:var(--violet-600);font-size:10px;font-weight:800;letter-spacing:.04em}
.legal-section p{margin:0;color:#374151;font-size:15px;line-height:1.8}
.legal-section p+p{margin-top:10px}
.legal-section a{color:var(--violet-700);font-weight:600;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:2px}
.legal-section a:hover{color:var(--violet-900)}
.legal-top{display:inline-flex;align-items:center;gap:6px;width:100%;margin-top:24px;padding-top:16px;border-top:1px solid var(--lav-1);color:var(--violet-700);font-size:12.5px;font-weight:600}
.legal-top:hover{color:var(--violet-900)}
.legal-top .ic{width:14px;height:14px;transform:rotate(-90deg)}
@media(max-width:900px){
.legal-page{padding-top:20px;padding-bottom:52px}
.legal-layout{grid-template-columns:1fr;gap:16px}
.legal-aside{position:static;width:100%;max-width:720px;margin:0 auto}
.legal-article{width:100%;max-width:720px;margin:0 auto}
.legal-toc{padding:14px 15px}
.legal-toc ol{grid-template-columns:repeat(2,minmax(0,1fr));gap:1px 8px}
.legal-help{display:none}
}
@media(max-width:600px){
.legal-page{padding:16px 14px 46px}
.legal-crumb{margin-bottom:12px}
.legal-hero{padding:24px 18px;border-radius:18px}
.legal-hero h1{font-size:27px;letter-spacing:-.5px}
.legal-sub{font-size:13.5px}
.legal-meta{gap:7px 10px;font-size:12px}
.legal-dot{display:none}
.legal-actions{align-items:stretch;flex-direction:column;gap:4px}
.legal-actions .btn-line{width:100%}
.legal-crosslink{justify-content:center}
.legal-article{padding:20px 17px;border-radius:16px}
.legal-section h2{gap:9px;font-size:18px}
.legal-section p{font-size:14.5px;line-height:1.75}
.legal-num{min-width:27px;font-size:9.5px}
}
@media(max-width:360px){
.legal-toc ol{grid-template-columns:1fr}
}
@media print{
.shop-topbar,.shop-header,.shop-nav,.shop-footer,.legal-crumb,.legal-actions,.legal-aside,#progress,#navprogress,.page-loader{display:none!important}
.legal-page{max-width:none;padding:0}
.legal-hero{padding:0 0 16px;border:0;border-bottom:1px solid #ddd;border-radius:0;background:none;box-shadow:none}
.legal-hero h1{font-size:26px}
.legal-layout{display:block;margin-top:18px}
.legal-article{padding:0;border:0;border-radius:0;box-shadow:none}
.legal-section{break-inside:avoid}
.legal-section+.legal-section{border-top:1px solid #e5e7eb}
.legal-top{display:none}
a{color:inherit!important;text-decoration:none!important}
}
</style>

<main class="shop-wrap legal-page">
    <nav class="legal-crumb" aria-label="Fil d'Ariane">
        <a href="{{ route('home') }}">Accueil</a>
        <svg class="ic" aria-hidden="true"><use href="#i-chevron"/></svg>
        <span aria-current="page">{{ $title }}</span>
    </nav>

    <header class="legal-hero">
        <span class="legal-eyebrow">
            <svg class="ic" aria-hidden="true"><use href="#{{ $icon }}"/></svg>
            {{ $eyebrow }}
        </span>
        <h1 id="legal-title">{{ $title }}</h1>
        @if ($description)
            <p class="legal-sub">{{ $description }}</p>
        @endif
        <div class="legal-meta">
            <span>
                <svg class="ic" aria-hidden="true"><use href="#i-b2-info"/></svg>
                Dernière mise à jour : <time datetime="{{ $updatedIso }}">{{ $updated }}</time>
            </span>
            <span class="legal-dot" aria-hidden="true"></span>
            <span>Lecture : {{ $readingMinutes }} min</span>
        </div>
        <div class="legal-actions">
            <a class="btn-line" href="{{ route('aide') }}">
                <svg class="ic" aria-hidden="true"><use href="#i-headset"/></svg>
                Aide &amp; support
            </a>
            @if ($otherRoute && $otherLabel)
                <a class="legal-crosslink" href="{{ route($otherRoute) }}">
                    {{ $otherLabel }}
                    <svg class="ic" aria-hidden="true"><use href="#i-chevron"/></svg>
                </a>
            @endif
        </div>
    </header>

    <div class="legal-layout">
        <aside class="legal-aside">
            <nav class="legal-toc" aria-label="Sommaire de la page">
                <h2 class="legal-toc-title"><svg class="ic" aria-hidden="true"><use href="#i-grid"/></svg> Sommaire</h2>
                <ol>
                    @foreach ($sections as $id => $label)
                        <li>
                            <a href="#{{ $id }}">
                                <span class="legal-toc-n" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span>{{ $label }}</span>
                            </a>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <div class="legal-help">
                <b><svg class="ic" aria-hidden="true"><use href="#i-headset"/></svg> Besoin d'aide ?</b>
                <p>Notre équipe vous répond du lundi au samedi, de 9h à 18h.</p>
                <a href="mailto:contact@mandenbaoubab.com">
                    <svg class="ic" aria-hidden="true"><use href="#i-reply"/></svg>
                    contact@mandenbaoubab.com
                </a>
                <a href="tel:+22382019583">
                    <svg class="ic" aria-hidden="true"><use href="#i-phone"/></svg>
                    +223 82 01 95 83
                </a>
            </div>
        </aside>

        <article class="legal-article">
            {{ $slot }}

            <a class="legal-top" href="#legal-title">
                <svg class="ic" aria-hidden="true"><use href="#i-chevron"/></svg>
                Revenir en haut
            </a>
        </article>
    </div>
</main>

@include('partials.footer')

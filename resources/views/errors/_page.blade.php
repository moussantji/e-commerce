{{-- Mise en page d'erreur boutique — variables : $code, $titre, $message, $icon, $actions(bool) --}}
@extends('base')

@section('title', $code . ' — ' . $titre)

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">{{ $code }}</span>
        </div>
    </nav>

    <section>
        <div class="wrap">
            <div class="dash-narrow" style="max-width:560px">
                <div class="empty" style="padding:64px 24px">
                    <div
                        style="font-size:64px;font-weight:800;letter-spacing:-2px;color:var(--violet-200, #ddd6fe);line-height:1">
                        {{ $code }}</div>
                    <svg class="ic" style="width:40px;height:40px;margin-top:14px">
                        <use href="#{{ $icon ?? 'i-b2-alert' }}" />
                    </svg>
                    <h3 style="margin-top:10px">{{ $titre }}</h3>
                    <p>{{ $message }}</p>
                    <div class="pdp-actions" style="justify-content:center;margin-top:20px">
                        <a class="btn-solid" href="{{ route('home') }}"><svg class="ic">
                                <use href="#i-home" />
                            </svg> Retour à l'accueil</a>
                        <a class="btn-line" href="{{ route('products') }}">Voir le catalogue</a>
                    </div>
                    @if (!empty($showLogin) && !auth()->check())
                        <p class="muted-sm" style="margin-top:14px">Besoin d'aide ? Appelez le <a class="lien"
                                href="tel:+22382019583">+223 82 01 95 83</a></p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection

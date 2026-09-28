@extends('base')

@section('title', 'Vérifier votre email')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Vérification email</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Vérifiez votre email</h1>
            <p>Un lien de vérification vient de vous être envoyé par email.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="dash-narrow">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-card" />
                        </svg> Confirmation requise</h2>
                    <p style="font-size:14px;color:#374151;line-height:1.7;margin:12px 0 4px">
                        Merci pour votre inscription ! Cliquez sur le lien reçu par email pour activer votre compte.
                        Si vous ne l'avez pas reçu, demandez un nouvel envoi ci-dessous.
                    </p>
                    @if (session('status') == 'verification-link-sent')
                        <div class="tagline-band" style="margin:14px 0 0">
                            <svg class="ic">
                                <use href="#i-b2-check" />
                            </svg>
                            <span>Un nouveau lien de vérification a été envoyé.</span>
                        </div>
                    @endif
                    <div class="pdp-actions" style="margin-top:18px">
                        <form method="POST" action="{{ route('verification.send') }}" style="flex:1;display:flex">
                            @csrf
                            <button class="btn-solid" style="flex:1" type="submit"><svg class="ic">
                                    <use href="#i-card" />
                                </svg> Renvoyer l'email</button>
                        </form>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin-top:12px;text-align:center">
                        @csrf
                        <button class="lien" type="submit"
                            style="background:none;border:0;cursor:pointer;font-size:13px">Se déconnecter</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection

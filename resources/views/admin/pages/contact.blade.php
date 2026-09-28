@extends('admin.base')

@section('title', 'Contact')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Contact</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Contact</h1>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel boutique-texte">
                <div class="dash-grid">
                    <div>
                        <h4>Informations de contact</h4>
                        <p>N'hésitez pas à nous contacter pour toute question ou demande d'information.</p>
                        <table class="spec">
                            <tbody>
                                <tr>
                                    <td>Adresse</td>
                                    <td>Bamako, Mali</td>
                                </tr>
                                <tr>
                                    <td>Téléphone</td>
                                    <td><a class="lien" href="tel:+22382019583">+223 82 01 95 83</a></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td><a class="lien" href="mailto:contact@boutique.ml">contact@boutique.ml</a></td>
                                </tr>
                                <tr>
                                    <td>Horaires</td>
                                    <td>Lun – Sam · 8h – 20h</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <h4>Envoyez-nous un message</h4>
                        <form onsubmit="return false;">
                            <div class="field">
                                <label for="name">Nom complet</label>
                                <input type="text" class="ctrl" id="name" required>
                            </div>
                            <div class="field">
                                <label for="email">Adresse email</label>
                                <input type="email" class="ctrl" id="email" required>
                            </div>
                            <div class="field">
                                <label for="subject">Sujet</label>
                                <input type="text" class="ctrl" id="subject" required>
                            </div>
                            <div class="field">
                                <label for="message">Message</label>
                                <textarea class="ctrl" id="message" rows="4" required style="border-radius:12px;resize:vertical"></textarea>
                            </div>
                            <button type="submit" class="btn-solid">Envoyer le message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('admin.base')

@section('title', 'Contact')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-4">Contactez-nous</h2>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Informations de contact</h4>
                                <p class="mb-4">N'hésitez pas à nous contacter pour toute question ou demande d'information.</p>
                                
                                <div class="mb-4">
                                    <h5>Adresse</h5>
                                    <p>123 Rue du Commerce<br>75001 Paris, France</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h5>Téléphone</h5>
                                    <p>+33 1 23 45 67 89</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h5>Email</h5>
                                    <p>contact@monsite.com</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h5>Heures d'ouverture</h5>
                                    <p>Lundi - Vendredi: 9h00 - 18h00<br>
                                    Samedi: 10h00 - 16h00<br>
                                    Dimanche: Fermé</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h4>Envoyez-nous un message</h4>
                                <form>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nom complet</label>
                                        <input type="text" class="form-control" id="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Adresse email</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Sujet</label>
                                        <input type="text" class="form-control" id="subject" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" rows="5" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Envoyer le message</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

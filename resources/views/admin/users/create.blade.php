@extends('admin.base')

@section('content')
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Nouvel utilisateur</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header">
            <h5 class="mb-0">Créer un utilisateur</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                {{-- Identité --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom"
                               class="form-control @error('prenom') is-invalid @enderror"
                               value="{{ old('prenom') }}">
                        @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="tel"
                               class="form-control @error('tel') is-invalid @enderror"
                               value="{{ old('tel') }}">
                        @error('tel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Naissance --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date de naissance</label>
                        <input type="date" name="date_naiss"
                               class="form-control @error('date_naiss') is-invalid @enderror"
                               value="{{ old('date_naiss') }}">
                        @error('date_naiss') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Lieu de naissance</label>
                        <input type="text" name="lieu_naiss"
                               class="form-control @error('lieu_naiss') is-invalid @enderror"
                               value="{{ old('lieu_naiss') }}">
                        @error('lieu_naiss') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Connexion --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Confirmation</label>
                        <input type="password" name="password_confirmation"
                               class="form-control" required>
                    </div>
                </div>

                {{-- Adresse (JSON) --}}
                <div class="border-top pt-3 mt-3">
                    <h6 class="mb-3">Adresse</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Adresse (ligne 1)</label>
                            <input type="text" name="adresse[line1]"
                                   class="form-control"
                                   value="{{ old('adresse.line1') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Adresse (ligne 2)</label>
                            <input type="text" name="adresse[line2]"
                                   class="form-control"
                                   value="{{ old('adresse.line2') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ville</label>
                            <input type="text" name="adresse[city]"
                                   class="form-control"
                                   value="{{ old('adresse.city') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Code postal</label>
                            <input type="text" name="adresse[postal_code]"
                                   class="form-control"
                                   value="{{ old('adresse.postal_code') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pays</label>
                            <input type="text" name="pays"
                                   class="form-control @error('pays') is-invalid @enderror"
                                   value="{{ old('pays') }}">
                            @error('pays') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Liens sociaux (JSON) --}}
                <div class="border-top pt-3 mt-3">
                    <h6 class="mb-3">Liens sociaux</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Facebook</label>
                            <input type="url" name="social_links[facebook]"
                                   class="form-control"
                                   value="{{ old('social_links.facebook') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="url" name="social_links[instagram]"
                                   class="form-control"
                                   value="{{ old('social_links.instagram') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">X (Twitter)</label>
                            <input type="url" name="social_links[twitter]"
                                   class="form-control"
                                   value="{{ old('social_links.twitter') }}">
                        </div>
                    </div>
                </div>

                {{-- Rôle & statut --}}
                <div class="border-top pt-3 mt-3">
                    <h6 class="mb-3">Rôle & statut</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Rôle</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror">
                                <option value="customer" {{ old('role','customer') === 'customer' ? 'selected' : '' }}>Client</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="inactive" {{ old('status','inactive') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Actif</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label d-block">Email vérifié</label>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" id="email_verified"
                                       name="email_verified" value="1" {{ old('email_verified') ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_verified">
                                    Marquer comme vérifié
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer l’utilisateur
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        Annuler
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

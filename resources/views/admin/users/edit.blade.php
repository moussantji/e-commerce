@extends('admin.base')

@section('content')
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Modifier l’utilisateur #{{ $user->id }}</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $user->name }} ({{ $user->email }})</h5>
            <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                {{ $user->status === 'active' ? 'Actif' : 'Inactif' }}
            </span>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Identité --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom"
                               class="form-control @error('prenom') is-invalid @enderror"
                               value="{{ old('prenom', $user->prenom) }}">
                        @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="tel"
                               class="form-control @error('tel') is-invalid @enderror"
                               value="{{ old('tel', $user->tel) }}">
                        @error('tel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Naissance --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date de naissance</label>
                        <input type="date" name="date_naiss"
                               class="form-control @error('date_naiss') is-invalid @enderror"
                               value="{{ old('date_naiss', optional($user->date_naiss)->format('Y-m-d')) }}">
                        @error('date_naiss') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Lieu de naissance</label>
                        <input type="text" name="lieu_naiss"
                               class="form-control @error('lieu_naiss') is-invalid @enderror"
                               value="{{ old('lieu_naiss', $user->lieu_naiss) }}">
                        @error('lieu_naiss') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Connexion --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Laisser vide pour ne pas changer">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Confirmation</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Laisser vide aussi">
                    </div>
                </div>

                @php
                    $adresse = is_array(old('adresse')) ? old('adresse') : ($user->adresse ?? []);
                    $social  = is_array(old('social_links')) ? old('social_links') : ($user->social_links ?? []);
                @endphp

                {{-- Adresse (JSON) --}}
                <div class="border-top pt-3 mt-3">
                    <h6 class="mb-3">Adresse</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Adresse (ligne 1)</label>
                            <input type="text" name="adresse[line1]"
                                   class="form-control"
                                   value="{{ $adresse['line1'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Adresse (ligne 2)</label>
                            <input type="text" name="adresse[line2]"
                                   class="form-control"
                                   value="{{ $adresse['line2'] ?? '' }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ville</label>
                            <input type="text" name="adresse[city]"
                                   class="form-control"
                                   value="{{ $adresse['city'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Code postal</label>
                            <input type="text" name="adresse[postal_code]"
                                   class="form-control"
                                   value="{{ $adresse['postal_code'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pays</label>
                            <input type="text" name="pays"
                                   class="form-control @error('pays') is-invalid @enderror"
                                   value="{{ old('pays', $user->pays) }}">
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
                                   value="{{ $social['facebook'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="url" name="social_links[instagram]"
                                   class="form-control"
                                   value="{{ $social['instagram'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">X (Twitter)</label>
                            <input type="url" name="social_links[twitter]"
                                   class="form-control"
                                   value="{{ $social['twitter'] ?? '' }}">
                        </div>
                    </div>
                </div>

                {{-- Rôle, statut, vérification, activité --}}
                <div class="border-top pt-3 mt-3">
                    <h6 class="mb-3">Rôle & statut</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Rôle</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror">
                                <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Client</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                <option value="active"   {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Actif</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label d-block">Email vérifié</label>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" id="email_verified"
                                       name="email_verified" value="1"
                                       {{ old('email_verified', $user->email_verified_at ? 1 : 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_verified">
                                    Marquer comme vérifié
                                </label>
                            </div>
                            @if($user->email_verified_at)
                                <small class="text-muted">
                                    Vérifié le {{ $user->email_verified_at->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dernière connexion</label>
                            <input type="text" class="form-control"
                                   value="{{ $user->last_login ? $user->last_login->format('d/m/Y H:i') : 'Jamais' }}"
                                   disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dernière activité</label>
                            <input type="text" class="form-control"
                                   value="{{ $user->last_activity ? $user->last_activity->format('d/m/Y H:i') : 'Inconnue' }}"
                                   disabled>
                        </div>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
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

@extends('layouts.app')
@section('title', 'Mon Profil')
@section('content')
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col">
                <h1 class="text-primary">Mon Profil</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white">
                        <h2 class="card-title h5 mb-0">Informations personnelles</h2>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-4 shadow-lg border-0">
                    <div class="card-header bg-warning text-white">
                        <h2 class="card-title h5 mb-0">Modifier mon mot de passe</h2>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="card mb-4 shadow-lg border-0">
                    <div class="card-header bg-danger text-white">
                        <h2 class="card-title h5 mb-0">Supprimer mon compte</h2>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

                <div class="card shadow-lg border-0">
                    <div class="card-header bg-info text-white">
                        <h2 class="card-title h5 mb-0">Historique de mes commandes</h2>
                    </div>
                    <div class="card-body">
                        @if(isset($commandes) && count($commandes) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark text-white">
                                    <tr>
                                        <th>N° Commande</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($commandes as $commande)
                                        <tr>
                                            <td>#{{ $commande->id }}</td>
                                            <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                            <span class="badge bg-{{ $commande->status_color }}">
                                                {{ $commande->status_label }}
                                            </span>
                                            </td>
                                            <td>{{ number_format($commande->total_price, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                <a href="{{ route('commandes.show', $commande->id) }}" class="btn btn-sm btn-info">
                                                    Voir Détails
                                                </a>
                                                @if($commande->status === 'prete')
                                                    <a href="{{ route('commandes.facture', $commande->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                                                        Télécharger Facture
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('commandes.index') }}" class="btn btn-outline-primary">Voir toutes mes commandes</a>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <p>Vous n'avez pas encore passé de commande.</p>
                                <a href="{{ route('produits.index') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-utensils"></i> Découvrir notre menu
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

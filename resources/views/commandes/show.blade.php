@extends('layouts.app')

@section('title', 'Commande #' . $commande->numero)

@section('content')
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="fw-bold">Commande #{{ $commande->numero }}</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux commandes
            </a>
            @if(Auth::user()->estGestionnaire() && $commande->statut !== 'payee' && $commande->statut !== 'annulee')
                <a href="{{ route('paiements.create', $commande) }}" class="btn btn-success">
                    <i class="fas fa-credit-card"></i> Enregistrer un paiement
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Détails de la commande</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th class="text-center">Prix unitaire</th>
                                <th class="text-center">Quantité</th>
                                <th class="text-end">Sous-total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($commande->details as $detail)
                                <tr>
                                    <td>{{ $detail->produit_nom }}</td>
                                    <td class="text-center">{{ number_format($detail->prix_unitaire) }} CFA</td>
                                    <td class="text-center">{{ $detail->quantite }}</td>
                                    <td class="text-end fw-bold">{{ number_format($detail->prix_unitaire * $detail->quantite) }} CFA</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr class="table-warning">
                                <td colspan="3" class="text-end fw-bold">Total :</td>
                                <td class="text-end fw-bold text-primary">{{ number_format($commande->montant_total) }} CFA</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Statut :</span>
                            <span class="badge bg-{{ $commande->statut === 'payee' ? 'primary' : ($commande->statut === 'annulee' ? 'danger' : 'warning') }}">
                                {{ ucfirst($commande->statut) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Date de la commande :</span>
                            <span>{{ $commande->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Client :</span>
                            <span>{{ $commande->client->prenom }} {{ $commande->client->nom }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Adresse de livraison :</strong>
                            <p class="mb-0">{{ $commande->adresse_livraison }}</p>
                        </li>
                        <li class="list-group-item">
                            <strong>Téléphone :</strong>
                            <p class="mb-0">{{ $commande->telephone }}</p>
                        </li>
                        @if($commande->instructions)
                            <li class="list-group-item">
                                <strong>Instructions :</strong>
                                <p class="mb-0">{{ $commande->instructions }}</p>
                            </li>
                        @endif
                        @if($commande->paiement)
                            <li class="list-group-item">
                                <strong>Paiement :</strong>
                                <p class="mb-0">Date : {{ $commande->paiement->created_at->format('d/m/Y H:i') }}</p>
                                <p class="mb-0">Montant : {{ number_format($commande->paiement->montant) }} CFA</p>
                                <p class="mb-0">Mode : {{ $commande->paiement->mode }}</p>
                            </li>
                        @endif
                    </ul>

                    @if(Auth::user()->estGestionnaire() && $commande->statut !== 'annulee')
                        <div class="mt-3">
                            <form action="{{ route('commandes.status.update', $commande) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <label for="statut" class="form-label">Changer le statut</label>
                                <select name="statut" id="statut" class="form-select mb-2">
                                    <option value="en_attente" {{ $commande->statut === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="en_preparation" {{ $commande->statut === 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                                    <option value="prete" {{ $commande->statut === 'prete' ? 'selected' : '' }}>Prête</option>
                                    @if($commande->paiement)
                                        <option value="payee" {{ $commande->statut === 'payee' ? 'selected' : '' }}>Payée</option>
                                    @endif
                                </select>
                                <button type="submit" class="btn btn-primary w-100">Mettre à jour</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

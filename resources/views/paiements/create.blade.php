@extends('layouts.app')

@section('title', 'Enregistrer un paiement')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg rounded">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <h1 class="h4 mb-0"><i class="bi bi-credit-card me-2"></i> Enregistrer un paiement - Commande #{{ $commande->id }}</h1>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="fw-bold"><i class="bi bi-person-circle me-2"></i> Informations client</h5>
                        <p><strong>Client :</strong> {{ $commande->client->name }}</p>
                        <p><strong>Email :</strong> {{ $commande->client->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bold"><i class="bi bi-receipt me-2"></i> Détails de la commande</h5>
                        <p><strong>Statut :</strong> <span class="badge bg-{{ $commande->status_color }}">{{ $commande->status_label }}</span></p>
                        <p><strong>Date :</strong> {{ $commande->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Total :</strong> <span class="fw-bold text-primary">{{ number_format($commande->montant_total) }} CFA</span></p>
                    </div>
                </div>

                <h5 class="mb-3 fw-bold"><i class="bi bi-cart-check me-2"></i> Produits commandés</h5>
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                    <tr>
                        <th>Produit</th>
                        <th class="text-center">Quantité</th>
                        <th class="text-end">Prix unitaire</th>
                        <th class="text-end">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($commande->details as $detail)
                        <tr>
                            <td>{{ $detail->produit->nom }}</td>
                            <td class="text-center">{{ $detail->quantite }}</td>
                            <td class="text-end">{{ number_format($detail->prix_unitaire) }} CFA</td>
                            <td class="text-end fw-bold">{{ number_format($detail->prix_unitaire * $detail->quantite) }} CFA</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="table-warning">
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end fw-bold text-primary">{{ number_format($commande->montant_total) }} CFA</th>
                    </tr>
                    </tfoot>
                </table>

                <hr class="my-4">

                <h5 class="mb-3 fw-bold"><i class="bi bi-cash-coin me-2"></i> Enregistrer un paiement</h5>
                <form action="{{ route('paiements.store', $commande->id) }}" method="POST">
                    @csrf

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="montant" class="form-label fw-bold">Montant</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control @error('montant') is-invalid @enderror" id="montant" name="montant" value="{{ old('montant', $commande->montant_total) }}" required>
                                <span class="input-group-text">CFA</span>
                                @error('montant')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="mode" class="form-label fw-bold">Méthode de paiement</label>
                            <select class="form-select @error('mode') is-invalid @enderror" id="mode" name="mode" required>
                                <option value="">Sélectionner une méthode</option>
                                <option value="carte" {{ old('mode') == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                                <option value="especes" {{ old('mode') == 'especes' ? 'selected' : '' }}>Espèces</option>
                                <option value="mobile" {{ old('mode') == 'mobile' ? 'selected' : '' }}>Paiement mobile</option>
                            </select>
                            @error('mode')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="commentaire" class="form-label fw-bold">Commentaire</label>
                        <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire" rows="3" placeholder="Ajouter une note..."></textarea>
                        @error('commentaire')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('commandes.show', $commande->id) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Annuler</a>
                        <button type="submit" class="btn btn-success"><i class="bi bi-save me-2"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

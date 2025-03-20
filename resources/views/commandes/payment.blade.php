@extends('layouts.app')

@section('title', 'Paiement de la Commande #' . $commande->id)

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h1>Paiement de la Commande #{{ $commande->id }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title h5 mb-0">Détails de la Commande</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Client:</div>
                        <div class="col-md-8">{{ $commande->user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Date de création:</div>
                        <div class="col-md-8">{{ $commande->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Statut:</div>
                        <div class="col-md-8">
                        <span class="badge {{ $commande->statut === 'prête' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($commande->statut) }}
                        </span>
                        </div>
                    </div>

                    <h3 class="h6 mt-4 mb-3">Articles commandés</h3>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th class="text-end">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($commande->ligneCommandes as $ligne)
                                <tr>
                                    <td>{{ $ligne->produit->nom }}</td>
                                    <td>{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} €</td>
                                    <td>{{ $ligne->quantite }}</td>
                                    <td class="text-end">{{ number_format($ligne->prix_unitaire * $ligne->quantite, 2, ',', ' ') }} €</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th class="text-end">{{ number_format($commande->total, 2, ',', ' ') }} €</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title h5 mb-0">Paiement</h2>
                </div>
                <div class="card-body">
                    @if($commande->est_payee)
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i> Cette commande a déjà été payée.
                            <p class="mt-2 mb-0">Date de paiement: {{ $commande->date_paiement->format('d/m/Y H:i') }}</p>
                        </div>
                    @else
                        <form action="{{ route('commandes.process-payment', $commande->id) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="montant" class="form-label">Montant</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('montant') is-invalid @enderror"
                                           id="montant" name="montant" value="{{ old('montant', $commande->total) }}" required readonly>
                                    <span class="input-group-text">€</span>
                                    @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="methode_paiement" class="form-label">Méthode de paiement</label>
                                <select class="form-select @error('methode_paiement') is-invalid @enderror" id="methode_paiement" name="methode_paiement" required>
                                    <option value="">Sélectionner une méthode</option>
                                    <option value="especes" {{ old('methode_paiement') == 'especes' ? 'selected' : '' }}>Espèces</option>
                                </select>
                                @error('methode_paiement')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                                <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire" rows="2">{{ old('commentaire') }}</textarea>
                                @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-cash me-2"></i> Confirmer le paiement
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

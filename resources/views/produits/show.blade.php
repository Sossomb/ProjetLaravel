@extends('layouts.app')

@section('title', 'Produit: ' . $produit->nom)

@section('content')
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>{{ $produit->nom }}</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux produits
            </a>

            @if(Auth::user()->estGestionnaire())
                <a href="{{ route('produits.edit', $produit) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>

                <!-- Bouton d'archivage -->
                <form action="{{ route('produits.destroy', $produit) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir archiver ce produit?')">
                        <i class="fas fa-archive"></i> Archiver
                    </button>
                </form>

                <!-- Bouton de suppression définitive -->
                <form action="{{ route('produits.delete', $produit->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('ATTENTION: Cette action est irréversible. Êtes-vous sûr de vouloir supprimer définitivement ce produit?')">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Détails du produit</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            @if($produit->image)
                                <img src="{{ asset('storage/' . $produit->image) }}" alt="{{ $produit->nom }}" class="img-fluid rounded mb-3">
                            @else
                                <div class="bg-light p-5 text-center rounded mb-3">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="mt-2">Pas d'image disponible</p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Catégorie:</strong> {{ $produit->categorie->nom ?? 'Non catégorisé' }}</p>
                            <p><strong>Prix:</strong> {{ number_format($produit->prix, 0, ',', ' ') }} CFA</p>
                            <p><strong>Disponibilité:</strong>
                                @if($produit->disponible)
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-danger">Non disponible</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Description</h5>
                        <p>{{ $produit->description ?: 'Aucune description disponible' }}</p>
                    </div>

                    @if(Auth::check() && $produit->disponible)
                        <form action="{{ route('panier.ajouter') }}" method="POST" class="mt-4">
                            @csrf
                            <input type="hidden" name="produit_id" value="{{ $produit->id }}">
                            <div class="input-group">
                                <input type="number" name="quantite" value="1" min="1" max="10" class="form-control" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-cart-plus"></i> Ajouter au panier
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Produits similaires</h5>
                </div>
                <div class="card-body">
                    <!-- Ici vous pourriez afficher des produits similaires -->
                    <p class="text-muted">Fonctionnalité à venir</p>
                </div>
            </div>
        </div>
    </div>
@endsection

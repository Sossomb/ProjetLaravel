@extends('layouts.app')

@section('title', 'Menu')

@section('content')
    <div class="container">
        <div class="mb-4 text-center">
            <h1>Notre Menu</h1>
        </div>

        @if(Auth::check() && Auth::user()->estGestionnaire())
            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('produits.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Nouveau Produit
                </a>
                <a href="{{ route('produits.archives') }}" class="btn btn-primary">
                    <i class="fas fa-archive"></i> Voir les produits archivés
                </a>
            </div>
        @endif

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('produits.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Nom du produit" value="{{ request('libelle') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control" id="prix_min" name="prix_min" placeholder="Prix min (CFA)" value="{{ request('prix_min') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control" id="prix_max" name="prix_max" placeholder="Prix max (CFA)" value="{{ request('prix_max') }}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="categorie_id" name="categorie_id">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}" {{ request('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                    {{ $categorie->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Produits en disposition horizontale -->
        <div class="row flex-nowrap overflow-auto">
            @forelse ($produits as $produit)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        @if($produit->image)
                            <img src="{{ asset('storage/' . $produit->image) }}" class="card-img-top" alt="{{ $produit->nom }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-hamburger fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $produit->nom }}</h5>
                            <p class="card-text text-muted small">{{ $produit->categorie->libelle }}</p>
                            <p class="card-text">{{ Str::limit($produit->description, 100) }}</p>
                            <p class="card-text fw-bold">{{ number_format($produit->prix) }} CFA</p>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('produits.show', $produit) }}" class="btn btn-outline-primary">Détails</a>

                                @auth
                                    @if($produit->estDisponible())
                                        <form action="{{ route('panier.ajouter') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="produit_id" value="{{ $produit->id }}">
                                            <input type="hidden" name="quantite" value="1">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-cart-plus"></i> Ajouter
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-secondary" disabled>Indisponible</button>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Aucun produit disponible.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

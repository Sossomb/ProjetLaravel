@extends('layouts.app')

@section('title', 'Catalogue de Burgers')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Nos Burgers</h1>

        <div class="row mb-4">
            <div class="col-md-8">
                <form action="{{ route('catalogue') }}" method="GET" class="d-flex">
                    <input type="text" name="recherche" class="form-control me-2" placeholder="Rechercher un burger..." value="{{ request('recherche') }}">
                    <button type="submit" class="btn btn-outline-primary">Rechercher</button>
                </form>
            </div>
            <div class="col-md-4">
                <select name="categorie" class="form-select" onchange="window.location.href='{{ route('catalogue') }}?categorie='+this.value">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $categorie)
                        <option value="{{ $categorie->id }}" {{ request('categorie') == $categorie->id ? 'selected' : '' }}>
                            {{ $categorie->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Filtres</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('catalogue') }}" method="GET">
                            <h6>Prix</h6>
                            <div class="mb-3">
                                <label for="prix_min" class="form-label">Min</label>
                                <input type="number" name="prix_min" id="prix_min" class="form-control" value="{{ request('prix_min') }}" min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label for="prix_max" class="form-label">Max</label>
                                <input type="number" name="prix_max" id="prix_max" class="form-control" value="{{ request('prix_max') }}" min="0" step="0.01">
                            </div>

                            <h6>Catégories</h6>
                            <div class="mb-3">
                                @foreach($categories as $categorie)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]"
                                               value="{{ $categorie->id }}" id="cat-{{ $categorie->id }}"
                                            {{ in_array($categorie->id, request('categories', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat-{{ $categorie->id }}">
                                            {{ $categorie->libelle }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="en_stock" id="en_stock" {{ request('en_stock') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="en_stock">
                                        En stock uniquement
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Appliquer les filtres</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row">
                    @forelse ($produits as $produit)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="{{ $produit->image ? asset('storage/'. $produit->image) : asset('images/placeholder.png') }}"
                                     class="card-img-top" alt="{{ $produit->nom }}" style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $produit->nom }}</h5>
                                    <p class="card-text text-muted small">{{ Str::limit($produit->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary">{{ number_format($produit->prix, 2, ',', ' ') }} €</span>
                                        <span class="badge {{ $produit->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $produit->stock > 0 ? 'En stock' : 'Rupture' }}
                                    </span>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('produits.show', $produit->id) }}" class="btn btn-outline-primary btn-sm">
                                            Détails
                                        </a>
                                        @if($produit->stock > 0)
                                            <form action="{{ route('panier.ajouter') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="produit_id" value="{{ $produit->id }}">
                                                <input type="hidden" name="quantite" value="1">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-cart-plus"></i> Ajouter
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                Indisponible
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                Aucun produit ne correspond à votre recherche.
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $produits->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    <div class="container py-5">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-4">Bienvenue chez ISI BURGER</h1>
                <p class="lead mb-4">Les meilleurs burgers artisanaux, préparés avec des ingrédients frais et locaux.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('catalogue') }}" class="btn btn-primary btn-lg">Voir notre menu</a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">S'inscrire</a>
                    @else
                        <a href="{{ route('panier.index') }}" class="btn btn-outline-primary btn-lg">Voir mon panier</a>
                    @endguest
                </div>
            </div>
            <div class="col-md-6">
                <img src="{{ asset('images/burger-hero.jpg') }}" alt="ISI Burger" class="img-fluid rounded-3 shadow">
            </div>
        </div>

        <div class="text-center my-5">
            <h2 class="fw-bold mb-4">Nos catégories populaires</h2>
        </div>

        <div class="row mb-5">
            @foreach($categories as $categorie)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <h3 class="card-title h4">{{ $categorie->libelle }}</h3>
                            <p class="card-text text-muted">{{ Str::limit($categorie->description, 100) }}</p>
                            <a href="{{ route('catalogue', ['categorie' => $categorie->id]) }}" class="btn btn-outline-primary mt-3">Découvrir</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center my-5">
            <h2 class="fw-bold mb-4">Nos burgers les plus populaires</h2>
        </div>

        <div class="row">
            @foreach($produits as $produit)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $produit->image ? asset('storage/'. $produit->image) : asset('images/placeholder.png') }}"
                             class="card-img-top" alt="{{ $produit->nom }}" style="height: 180px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $produit->nom }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($produit->description, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">{{ number_format($produit->prix, 2, ',', ' ') }} €</span>
                                <a href="{{ route('produits.show', $produit->id) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('catalogue') }}" class="btn btn-primary btn-lg">Voir tout notre menu</a>
        </div>
    </div>

    <div class="bg-light py-5 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="fw-bold mb-4">Comment ça marche ?</h2>
                    <div class="d-flex align-items-start mb-4">
                        <div class="bg-primary text-white rounded-circle p-3 me-3" style="width: 40px; height: 40px; text-align: center; line-height: 1;">1</div>
                        <div>
                            <h3 class="h5 mb-2">Parcourez notre menu</h3>
                            <p class="text-muted">Explorez notre sélection de délicieux burgers artisanaux.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-4">
                        <div class="bg-primary text-white rounded-circle p-3 me-3" style="width: 40px; height: 40px; text-align: center; line-height: 1;">2</div>
                        <div>
                            <h3 class="h5 mb-2">Composez votre commande</h3>
                            <p class="text-muted">Ajoutez vos burgers préférés à votre panier.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <div class="bg-primary text-white rounded-circle p-3 me-3" style="width: 40px; height: 40px; text-align: center; line-height: 1;">3</div>
                        <div>
                            <h3 class="h5 mb-2">Récupérez votre commande</h3>
                            <p class="text-muted">Venez chercher votre commande fraîchement préparée au restaurant.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="{{ asset('images/order-process.jpg') }}" alt="Comment commander" class="img-fluid rounded-3 shadow">
                </div>
            </div>
        </div>
    </div>
@endsection

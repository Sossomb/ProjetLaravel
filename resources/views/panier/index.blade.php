@extends('layouts.app')

@section('title', 'Mon Panier')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h1>Mon Panier</h1>
        </div>
    </div>

    @if(count($items) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                <tr>
                    <th>Produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @php $total = 0; @endphp
                @foreach($items as $item)
                    @php
                        $produit = $item['produit'];
                        $quantite = $item['quantite'];
                        $sousTotal = $produit->prix * $quantite;
                        $total += $sousTotal;
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($produit->image)
                                    <img src="{{ asset('storage/' . $produit->image) }}" class="img-thumbnail me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $produit->nom }}">
                                @else
                                    <div class="bg-light rounded me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-hamburger text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('produits.show', $produit) }}" class="text-decoration-none">{{ $produit->nom }}</a>
                                    <small class="text-muted d-block">
                                        {{ isset($produit->categorie) ? $produit->categorie->libelle : 'Aucune catégorie' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>{{ number_format($produit->prix) }} CFA</td>
                        <td>
                            <form action="{{ route('panier.update') }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="quantites[{{ $produit->id }}]" value="{{ $quantite }}">
                                <input type="number" name="quantites[{{ $produit->id }}]" class="form-control form-control-sm" style="width: 80px;" min="1" max="{{ $produit->stock }}" value="{{ $quantite }}" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td>{{ number_format($sousTotal) }} CFA</td>
                        <td>
                            <form action="{{ route('panier.supprimer', $produit->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer cet article du panier ?');">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="table-light">
                    <td colspan="3" class="text-end fw-bold">Total:</td>
                    <td class="fw-bold">{{ number_format($total, 2, '.', 0) }} CFA</td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <div>
                <form action="{{ route('panier.vider') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir vider votre panier?')">
                        <i class="fas fa-trash"></i> Vider le panier
                    </button>
                </form>
            </div>
            <a href="{{ route('commandes.checkout') }}" class="btn btn-success">
                <i class="fas fa-check"></i> Passer la commande
            </a>
        </div>
    @else
        <div class="alert alert-info">
            <p>Votre panier est vide.</p>
            <a href="{{ route('produits.index') }}" class="btn btn-primary mt-2">
                <i class="fas fa-utensils"></i> Parcourir le menu
            </a>
        </div>
    @endif
@endsection

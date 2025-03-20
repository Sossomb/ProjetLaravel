@extends('layouts.app')

@section('title', 'Finaliser la commande')

@section('content')
    <div class="container py-5">
        <h1 class="mb-4 text-center text-primary">Finaliser votre commande</h1>

        @if(session('error'))
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-cart-fill"></i> Récapitulatif de votre panier</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Sous-total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $total = 0; @endphp
                                @if(is_array($items) && count($items) > 0)
                                    @foreach($items as $id => $item)
                                        @if(isset($item['produit']) && isset($item['quantite']) && isset($item['sous_total']))
                                            @php
                                                $produit = $item['produit'];
                                                $quantite = $item['quantite'];
                                                $sousTotal = $item['sous_total'];
                                                $total += $sousTotal;
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $produit->nom }}</strong></td>
                                                <td>{{ $quantite }}</td>
                                                <td>{{ number_format($produit->prix) }} CFA</td>
                                                <td><strong>{{ number_format($sousTotal) }} CFA</strong></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-danger">Votre panier est vide</td>
                                    </tr>
                                @endif
                                </tbody>
                                <tfoot>
                                <tr class="table-info">
                                    <th colspan="3" class="text-end">Total</th>
                                    <th>{{ number_format($total) }} CFA</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        @if(!is_array($items) || count($items) == 0)
                            <div class="alert alert-warning mt-3 text-center">
                                <i class="bi bi-info-circle-fill"></i> Votre panier est vide.
                                <a href="{{ route('produits.index') }}" class="text-decoration-none">Retourner au catalogue</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-truck"></i> Adresse de livraison</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('commandes.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="adresse_livraison" class="form-label">Adresse de livraison</label>
                                <textarea name="adresse_livraison" id="adresse_livraison" rows="3" class="form-control @error('adresse_livraison') is-invalid @enderror" required>{{ old('adresse_livraison', auth()->user()->adresse) }}</textarea>
                                @error('adresse_livraison')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="text" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone', auth()->user()->telephone) }}" required>
                                @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (optionnel)</label>
                                <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2" {{ !is_array($items) || count($items) == 0 ? 'disabled' : '' }}>
                                <i class="bi bi-check-circle"></i> Confirmer la commande
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

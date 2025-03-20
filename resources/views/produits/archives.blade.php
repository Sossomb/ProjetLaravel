@extends('layouts.app')

@section('title', 'Produits archivés')

@section('content')
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Produits archivés</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux produits
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($produits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Catégorie</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($produits as $produit)
                            <tr>
                                <td>
                                    @if($produit->image)
                                        <img src="{{ asset('storage/' . $produit->image) }}" alt="{{ $produit->nom }}" style="height: 50px;">
                                    @else
                                        <i class="fas fa-image text-muted"></i>
                                    @endif
                                </td>
                                <td>{{ $produit->nom }}</td>
                                <td>{{ number_format($produit->prix, 0, ',', ' ') }} CFA</td>
                                <td>{{ $produit->categorie->nom ?? 'Non catégorisé' }}</td>
                                <td>
                                    <a href="{{ route('produits.show', $produit) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <form action="{{ route('produits.restore', $produit->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Êtes-vous sûr de vouloir restaurer ce produit?')">
                                            <i class="fas fa-trash-restore"></i> Restaurer
                                        </button>
                                    </form>

                                    <form action="{{ route('produits.delete', $produit->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('ATTENTION: Cette action est irréversible. Êtes-vous sûr de vouloir supprimer définitivement ce produit?')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $produits->links() }}
                </div>
            @else
                <p class="text-center">Aucun produit archivé pour le moment.</p>
            @endif
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Mes Commandes')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h1>Mes Commandes</h1>
        </div>
    </div>

    @if($commandes->count() > 0)
        <!-- Table avec défilement vertical -->
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($commandes as $commande)
                    <tr>
                        <td>{{ $commande->numero }}</td>
                        <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @switch($commande->statut)
                                @case('en_attente')
                                    <span class="badge bg-warning">En attente</span>
                                    @break
                                @case('en_preparation')
                                    <span class="badge bg-info">En préparation</span>
                                    @break
                                @case('prete')
                                    <span class="badge bg-success">Prête</span>
                                    @break
                                @case('payee')
                                    <span class="badge bg-primary">Payée</span>
                                    @break
                                @case('annulee')
                                    <span class="badge bg-danger">Annulée</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $commande->statut }}</span>
                            @endswitch
                        </td>
                        <td>{{ number_format($commande->montant_total) }} CFA</td>
                        <td>
                            <a href="{{ route('commandes.show', $commande) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Détails
                            </a>

                            @if($commande->statut === 'en_attente')
                                <form action="{{ route('commandes.cancel', $commande) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande?')">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">
            <p>Vous n'avez pas encore passé de commande.</p>
            <a href="{{ route('produits.index') }}" class="btn btn-primary mt-2">
                <i class="fas fa-utensils"></i> Parcourir le menu
            </a>
        </div>
    @endif
@endsection

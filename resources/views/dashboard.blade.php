@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h1>Tableau de bord</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Commandes du jour</h6>
                            <h2 class="display-4 mb-0">{{ $commandesJour }}</h2>
                        </div>
                        <i class="bi bi-cart4 fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('commandes.index', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-primary w-100">
                        Voir le détail
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Commandes validées</h6>
                            <h2 class="display-4 mb-0">{{ $commandesValidees }}</h2>
                        </div>
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('commandes.index', ['statut' => 'prete', 'date' => now()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-success w-100">
                        Voir le détail
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Commandes en attente</h6>
                            <h2 class="display-4 mb-0">{{ $commandesEnAttente }}</h2>
                        </div>
                        <i class="bi bi-hourglass-split fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('commandes.index', ['statut' => 'en_attente', 'date' => now()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-warning w-100">
                        Voir le détail
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Recettes du jour</h6>
                            <h2 class="display-4 mb-0">{{ number_format($recettesJour, 0) }} CFA</h2>
                        </div>
                        <i class="bi bi-cash-coin fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('commandes.index', ['est_payee' => 1, 'date' => now()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-info w-100">
                        Voir le détail
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="card-title h5 mb-0">Commandes par mois</h2>
                </div>
                <div class="card-body">
                    <canvas id="commandesParMois" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="card-title h5 mb-0">Produits par catégorie</h2>
                </div>
                <div class="card-body">
                    <canvas id="produitsParCategorie" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="card-title h5 mb-0">Dernières commandes</h2>
                    <a href="{{ route('commandes.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($dernieresCommandes as $commande)
                                <tr>
                                    <td>{{ $commande->id }}</td>
                                    <td>{{ $commande->client->name }}</td>
                                    <td>{{ $commande->date_commande->format('d/m/Y H:i') }}</td>
                                    <td>
                                    <span class="badge
                                        {{ $commande->statut === 'en_attente' ? 'bg-warning' : '' }}
                                        {{ $commande->statut === 'en_cours' ? 'bg-info' : '' }}
                                        {{ $commande->statut === 'en_preparation' ? 'bg-info' : '' }}
                                        {{ $commande->statut === 'prete' ? 'bg-success' : '' }}
                                        {{ $commande->statut === 'annulee' ? 'bg-danger' : '' }}">
                                        {{ $commande->status_label }}
                                    </span>
                                    </td>
                                    <td>{{ number_format($commande->montant_total) }} CFA</td>
                                    <td>
                                        <a href="{{ route('commandes.show', $commande->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">Aucune commande récente</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="card-title h5 mb-0">Produits en rupture</h2>
                    <a href="{{ route('produits.index', ['stock' => 'rupture']) }}" class="btn btn-sm btn-outline-danger">Gérer</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($produitsEnRupture ?? [] as $produit)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">{{ $produit->nom }}</span>
                                    <br>
                                    <small class="text-muted">{{ $produit->categorie->libelle }}</small>
                                </div>
                                <a href="{{ route('produits.edit', $produit->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Éditer
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-3">Tous les produits sont en stock</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Commandes par mois
                var commandesCtx = document.getElementById('commandesParMois').getContext('2d');
                var commandesChart = new Chart(commandesCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($commandesMensuellesLabels ?? []) !!},
                        datasets: [{
                            label: 'Nombre de commandes',
                            data: {!! json_encode($commandesMensuellesData ?? []) !!},
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                // Produits par catégorie
                var produitsCtx = document.getElementById('produitsParCategorie').getContext('2d');
                var produitsChart = new Chart(produitsCtx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($produitsParCategorieLabels ?? []) !!},
                        datasets: [{
                            data: {!! json_encode($produitsParCategorieData ?? []) !!},
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(153, 102, 255, 0.7)',
                                'rgba(255, 159, 64, 0.7)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection

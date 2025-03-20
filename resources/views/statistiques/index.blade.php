@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4 text-center text-primary">📊 Tableau de Bord Statistiques</h1>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body text-center">
                        <h5 class="card-title text-secondary">📦 Commandes en cours</h5>
                        <p class="card-text display-4 fw-bold text-warning">{{ $commandesEnCours }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body text-center">
                        <h5 class="card-title text-secondary">✅ Commandes validées</h5>
                        <p class="card-text display-4 fw-bold text-success">{{ $commandesValidees }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body text-center">
                        <h5 class="card-title text-secondary">💰 Recettes du jour</h5>
                        <p class="card-text display-4 fw-bold text-primary">{{ number_format($recettesJour, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <h5 class="card-title text-center text-info">📅 Commandes par mois ({{ Carbon\Carbon::now()->year }})</h5>
                        <canvas id="commandesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <h5 class="card-title text-center text-danger">📊 Produits par catégorie</h5>
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphique des commandes par mois
        const ctxCommandes = document.getElementById('commandesChart');
        new Chart(ctxCommandes, {
            type: 'bar',
            data: {
                labels: {!! json_encode($moisData) !!},
                datasets: [{
                    label: 'Nombre de commandes',
                    data: {!! json_encode($commandeData) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // Graphique des produits par catégorie
        const ctxCategories = document.getElementById('categoriesChart');
        new Chart(ctxCategories, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categorieLabels) !!},
                datasets: [{
                    data: {!! json_encode($produitCounts) !!},
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ],
                    borderWidth: 2
                }]
            }
        });
    </script>
@endsection

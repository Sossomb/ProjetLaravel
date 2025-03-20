@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Commande Prête</h1>

        <p>Bonjour,</p>

        <p>Votre commande est prête à être récupérée !</p>

        <h2>Informations client</h2>
        <p>
            Nom : {{ $commande->user->name ?? 'Client non identifié' }}<br>
            Email : {{ $commande->user->email ?? 'Email non disponible' }}<br>
            Téléphone : {{ $commande->telephone ?? 'Non spécifié' }}<br>
            Adresse de livraison : {{ $commande->adresse_livraison ?? 'Non spécifiée' }}
        </p>

        <h2>Détails de la commande</h2>
        <table border="1" cellpadding="5" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Sous-total</th>
            </tr>
            </thead>
            <tbody>
            @php
                $total_calcule = 0;
            @endphp
            @foreach($commande->lignes as $ligne)
                @php
                    $sous_total = $ligne->prix_unitaire * $ligne->quantite;
                    $total_calcule += $sous_total;
                @endphp
                <tr>
                    <td>{{ $ligne->produit->nom ?? 'Produit inconnu' }}</td>
                    <td>{{ $ligne->quantite }}</td>
                    <td>{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} CFA</td>
                    <td>{{ number_format($sous_total, 0, ',', ' ') }} CFA</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" align="right"><strong>Total</strong></td>
                <td><strong>{{ number_format($total_calcule, 0, ',', ' ') }} CFA</strong></td>
            </tr>
            </tfoot>
        </table>

        @if($commande->notes)
            <h2>Notes</h2>
            <p>{{ $commande->notes }}</p>
        @endif

        <p>Merci de venir récupérer votre commande dès que possible.</p>
    </div>
@endsection

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $commande->id }} - ISI BURGER</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .text-end {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">ISI BURGER</div>
        <div>Restauration rapide de qualité</div>
    </div>

    <div class="invoice-title">
        Facture #{{ $commande->id }}
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Client:</div>
            <div class="info-value">{{ $commande->user->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $commande->user->email }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de commande:</div>
            <div class="info-value">{{ $commande->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Statut:</div>
            <div class="info-value">{{ ucfirst($commande->statut) }}</div>
        </div>
        @if($commande->est_payee)
            <div class="info-row">
                <div class="info-label">Date de paiement:</div>
                <div class="info-value">{{ $commande->date_paiement->format('d/m/Y H:i') }}</div>
            </div>
        @endif
    </div>

    <table>
        <thead>
        <tr>
            <th>Produit</th>
            <th>Prix unitaire</th>
            <th>Quantité</th>
            <th class="text-end">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($commande->ligneCommandes as $ligne)
            <tr>
                <td>{{ $ligne->produit->nom }}</td>
                <td>{{ number_format($ligne->prix_unitaire) }} CFA</td>
                <td>{{ $ligne->quantite }}</td>
                <td class="text-end">{{ number_format($ligne->prix_unitaire * $ligne->quantite) }} CFA</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="total-row">
            <td colspan="3" class="text-end">Total</td>
            <td class="text-end">{{ number_format($commande->total) }} CFA</td>
        </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Merci de votre commande chez ISI BURGER!</p>
        <p>Si vous avez des questions, n'hésitez pas à nous contacter au 01 23 45 67 89</p>
        <p>ISI BURGER - Adresse, Code Postal, Ville - SIRET: 000 000 000 00000</p>
    </div>
</div>
</body>
</html>

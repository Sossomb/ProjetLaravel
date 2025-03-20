<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre facture ISI BURGER</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>ISI BURGER</h1>
    </div>

    <div class="content">
        <p>Bonjour {{ $commande->client->name }},</p>

        <p>Nous vous remercions pour votre commande #{{ $commande->numero }}.</p>

        <p>Veuillez trouver ci-joint votre facture.</p>

        <p>Montant total: {{ number_format($commande->montant_total, 0, ',', ' ') }} CFA</p>

        <p>Nous vous remercions de votre confiance et espérons vous revoir bientôt chez ISI BURGER!</p>
    </div>

    <div class="footer">
        <p>ISI BURGER - Adresse, Code Postal, Ville - Tél: 01 23 45 67 89</p>
    </div>
</div>
</body>
</html>

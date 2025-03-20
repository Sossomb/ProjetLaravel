<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre facture ISI BURGER</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .header h1 {
            color: #e63946;
            margin: 0;
            font-size: 32px;
            letter-spacing: 1px;
        }
        .content {
            margin-bottom: 30px;
            font-size: 16px;
        }
        .content p {
            margin-bottom: 15px;
        }
        .highlight {
            font-weight: bold;
            color: #e63946;
            font-size: 18px;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }
        .btn {
            display: inline-block;
            background-color: #e63946;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 15px;
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

        <p>Nous vous remercions pour votre commande <span class="highlight">#{{ $commande->numero }}</span>.</p>

        <p>Votre paiement a bien été confirmé et nous vous avons joint votre facture en pièce attachée à cet email.</p>

        <p>Montant total: <span class="highlight">{{ number_format($commande->montant_total, 0, ',', ' ') }} CFA</span></p>

        <p>Nous vous remercions de votre confiance et espérons vous revoir bientôt chez ISI BURGER!</p>

        <center><a href="{{ route('commandes.show', $commande->id) }}" class="btn">Voir ma commande</a></center>
    </div>

    <div class="footer">
        <p>ISI BURGER - Dakar, Sénégal - Tél: 77 229 83 46</p>
        <p>Ce message est automatique, merci de ne pas y répondre</p>
    </div>
</div>
</body>
</html>

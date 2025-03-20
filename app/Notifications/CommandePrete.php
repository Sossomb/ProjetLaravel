<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommandePrete extends Notification implements ShouldQueue
{
    use Queueable;

    protected $commande;
    protected $facturePdf;

    /**
     * Créer une nouvelle instance de notification.
     *
     * @param Commande $commande
     * @param string|null $facturePdf Chemin vers le PDF de la facture (peut être null)
     */
    public function __construct(Commande $commande, $facturePdf = null)
    {
        $this->commande = $commande;
        $this->facturePdf = $facturePdf;
    }

    /**
     * Définir les canaux de livraison de la notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Obtenir la représentation mail de la notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('Votre commande #' . $this->commande->numero . ' est prête!')
            ->line('Bonjour ' . ($notifiable->prenom ?? $notifiable->name ?? '') . ',')
            ->line('Nous sommes heureux de vous informer que votre commande est maintenant prête à être retirée ou livrée.')
            ->line('Détails de votre commande :')
            ->line('Numéro de commande : ' . $this->commande->numero);

        // Calculer le montant total ou utiliser la propriété 'total' ou 'montant_total'
        $montantTotal = $this->commande->total ?? $this->commande->montant_total ?? $this->calculerTotal();
        $mailMessage->line('Total : ' . number_format($montantTotal, 0, ',', ' ') . ' CFA');

        // Ajouter la facture en pièce jointe si disponible
        if ($this->facturePdf && file_exists($this->facturePdf)) {
            $mailMessage->attach($this->facturePdf, [
                'as' => 'facture-commande-' . $this->commande->numero . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mailMessage
            ->action('Voir ma commande', url('/commandes/' . $this->commande->id))
            ->line('Merci d\'avoir choisi notre service !');
    }

    /**
     * Obtenir la représentation du tableau de la notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'commande_id' => $this->commande->id,
            'numero_commande' => $this->commande->numero,
            'statut' => 'prête',
            'message' => 'Votre commande #' . $this->commande->numero . ' est prête à être retirée ou livrée.'
        ];
    }

    /**
     * Calculer le total de la commande si nécessaire
     *
     * @return float
     */
    private function calculerTotal()
    {
        $total = 0;

        if ($this->commande->lignes) {
            foreach ($this->commande->lignes as $ligne) {
                $total += $ligne->prix_unitaire * $ligne->quantite;
            }
        }

        return $total;
    }
}

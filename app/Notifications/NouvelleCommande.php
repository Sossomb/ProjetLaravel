<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleCommande extends Notification implements ShouldQueue
{
    use Queueable;

    protected $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle commande #' . $this->commande->numero)
            ->line('Une nouvelle commande a été passée.')
            ->line('Numéro de commande: ' . $this->commande->numero)
            ->line('Montant total: ' . number_format($this->commande->montant_total) . ' CFA')
            ->action('Voir les détails', route('commandes.show', $this->commande))
            ->line('Merci de traiter cette commande rapidement!');
    }

    public function toArray($notifiable)
    {
        return [
            'commande_id' => $this->commande->id,
            'numero' => $this->commande->numero,
            'montant' => $this->commande->montant_total,
        ];
    }

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

<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\PDF;

class FactureCommande extends Mailable
{
    use Queueable, SerializesModels;

    public $commande;
    protected $pdfContent = null;

    /**
     * Create a new message instance.
     *
     * @param  Commande  $commande
     * @param  string|null  $pdfContent
     * @return void
     */
    public function __construct(Commande $commande, $pdfContent = null)
    {
        $this->commande = $commande;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject('Votre facture ISI BURGER #' . $this->commande->numero)
            ->view('emails.facture');

        // Si le contenu PDF est déjà fourni
        if ($this->pdfContent) {
            $mail->attachData(
                $this->pdfContent,
                'facture-' . $this->commande->numero . '.pdf',
                ['mime' => 'application/pdf']
            );
        } else {
            // Sinon générer le PDF ici
            $pdf = PDF::loadView('factures.facture', ['commande' => $this->commande]);
            $mail->attachData(
                $pdf->output(),
                'facture-' . $this->commande->numero . '.pdf',
                ['mime' => 'application/pdf']
            );
        }

        return $mail;
    }
}

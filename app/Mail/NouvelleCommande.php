<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Commande;

class NouvelleCommande extends Mailable
{
use Queueable, SerializesModels;

public $commande;

public function __construct(Commande $commande)
{
$this->commande = $commande;
}

public function build()
{
return $this->subject('Confirmation de votre commande')
->view('emails.nouvelle_commande')
->with(['commande' => $this->commande]);
}
}

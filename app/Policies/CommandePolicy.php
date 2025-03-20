<?php

namespace App\Policies;

use App\Models\Commande;
use App\Models\User;

class CommandePolicy
{
    public function view(User $user, Commande $commande)
    {
        return $user->estGestionnaire() || $commande->client_id === $user->id;
    }

    public function update(User $user, Commande $commande)
    {
        return $user->estGestionnaire();
    }

    public function cancel(User $user, Commande $commande)
    {
        return $user->estGestionnaire() || ($commande->client_id === $user->id && $commande->statut === 'en_attente');
    }
}

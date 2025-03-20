<?php

namespace App\Policies;

use App\Models\Produit;
use App\Models\User;

class ProduitPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Produit $produit)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->estGestionnaire();
    }

    public function update(User $user, Produit $produit)
    {
        return $user->estGestionnaire();
    }

    public function delete(User $user, Produit $produit)
    {
        return $user->estGestionnaire();
    }

    public function restore(User $user, Produit $produit)
    {
        return $user->estGestionnaire();
    }

    public function viewArchived(User $user)
    {
        return $user->estGestionnaire();
    }
}

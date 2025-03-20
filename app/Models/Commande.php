<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'date_commande',
        'statut',
        'client_id',
        'montant_total',  // Garder pour compatibilité
        'adresse_livraison',
        'telephone',
        'instructions',
        'notes'          // Ajout du champ notes qui est utilisé dans la vue
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'montant_total' => 'float',
    ];

    protected $appends = ['status_label', 'status_color', 'total'];

    // Accessor pour obtenir le total (alias de montant_total pour compatibilité avec la vue)
    public function getTotalAttribute()
    {
        // Si montant_total est déjà défini et non nul, on le retourne
        if ($this->montant_total) {
            return $this->montant_total;
        }

        // Sinon, on calcule le total à partir des lignes de commande
        return $this->lignes->sum(function($ligne) {
            return $ligne->prix_unitaire * $ligne->quantite;
        });
    }

    // Méthode pour calculer et enregistrer le total
    public function calculerEtEnregistrerTotal()
    {
        $total = $this->lignes->sum(function($ligne) {
            return $ligne->prix_unitaire * $ligne->quantite;
        });

        $this->montant_total = $total;
        $this->save();

        return $total;
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Alias pour la relation client afin de maintenir la compatibilité avec les vues existantes
    public function user()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function detailCommandes()
    {
        return $this->hasMany(DetailCommande::class);
    }

    // Alias pour la relation detailCommandes
    public function details()
    {
        return $this->hasMany(DetailCommande::class);
    }

    // Alias pour la relation details pour compatibilité avec la vue qui utilise "lignes"
    public function lignes()
    {
        return $this->hasMany(DetailCommande::class);
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class);
    }

    public function estPayee()
    {
        return $this->statut === 'payee';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours de préparation',
            'en_preparation' => 'En préparation', // Pour gérer les deux formats possibles
            'prete' => 'Prête à être retirée',
            'livree' => 'Livrée',
            'annulee' => 'Annulée',
            'payee' => 'Payée'
        ];

        return $labels[$this->statut] ?? $this->statut;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'en_attente' => 'warning',
            'en_cours' => 'info',
            'en_preparation' => 'info', // Pour gérer les deux formats possibles
            'prete' => 'success',
            'livree' => 'primary',
            'annulee' => 'danger',
            'payee' => 'success'
        ];

        return $colors[$this->statut] ?? 'secondary';
    }

    public function getDatePaiementAttribute()
    {
        return $this->paiement ? $this->paiement->date_paiement : null;
    }

    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'detail_commandes', 'commande_id', 'produit_id')
            ->withPivot('quantite')
            ->withTimestamps();
    }
}

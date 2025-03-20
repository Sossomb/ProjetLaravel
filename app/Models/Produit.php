<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'prix', 'image', 'description', 'stock', 'categorie_id', 'archive'];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function detailCommandes()
    {
        return $this->hasMany(DetailCommande::class);
    }

    public function estDisponible()
    {
        return $this->stock > 0 && !$this->archive;
    }
}

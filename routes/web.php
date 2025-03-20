<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\ProfileController;

// Route principale (accueil)
Route::get('/', function () {
    return redirect()->route('produits.index');
})->name('home');

// Route pour le catalogue (assumant qu'il s'agit de la même page que produits.index)
Route::get('/catalogue', [ProduitController::class, 'index'])->name('catalogue');

// Routes d'authentification avec Breeze
Route::middleware('guest')->group(function () {
    // Ces routes sont automatiquement gérées par Breeze (login, register)
});

// Routes accessibles à tous les utilisateurs authentifiés
Route::middleware('auth')->group(function () {
    // Routes de profil fournies par Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ancien bloc panier - maintenant accessible à tous les utilisateurs authentifiés
    Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');
    Route::post('/panier/ajouter', [PanierController::class, 'ajouter'])->name('panier.ajouter');
    Route::delete('/panier/{produit}', [PanierController::class, 'supprimer'])->name('panier.supprimer');
    Route::put('/panier', [PanierController::class, 'mettreAJour'])->name('panier.update');
    Route::delete('/panier', [PanierController::class, 'vider'])->name('panier.vider');
    // Ajout des routes pour les factures
    Route::get('/commandes/{commande}/facture/download', [CommandeController::class, 'generateInvoice'])->name('commandes.facture.download');
    Route::post('/commandes/{commande}/facture/send', [CommandeController::class, 'sendInvoice'])->name('commandes.facture.send');

    // Ancien bloc commandes clients - maintenant accessible à tous les utilisateurs authentifiés
    Route::get('/commandes', [CommandeController::class, 'index'])->name('commandes.index');
    Route::get('/commandes/{commande}', [CommandeController::class, 'show'])->name('commandes.show');
    Route::post('/commandes', [CommandeController::class, 'store'])->name('commandes.store');
    Route::get('/checkout', [CommandeController::class, 'checkout'])->name('commandes.checkout');
    Route::get('/commandes/{commande}/facture', [CommandeController::class, 'facture'])->name('commandes.facture');


    // Nouvelle route pour commander à nouveau
    Route::post('/commandes/{commande}/repeat', [CommandeController::class, 'repeat'])->name('commandes.repeat');
});

// Ajout de la route pour le tableau de bord des gestionnaires
Route::middleware(['auth', 'gestionnaire'])->get('/dashboard', [StatistiqueController::class, 'dashboard'])->name('dashboard');

// Routes pour les produits - index (accessible à tous)
Route::get('/produits', [ProduitController::class, 'index'])->name('produits.index');

// Routes pour les produits (administration) - PLACÉES AVANT la route show
Route::middleware(['auth', 'gestionnaire'])->group(function () {
    Route::get('/produits/create', [ProduitController::class, 'create'])->name('produits.create');
    Route::post('/produits', [ProduitController::class, 'store'])->name('produits.store');
    Route::get('/produits/{produit}/edit', [ProduitController::class, 'edit'])->name('produits.edit');
    Route::put('/produits/{produit}', [ProduitController::class, 'update'])->name('produits.update');
    Route::delete('/produits/{produit}', [ProduitController::class, 'destroy'])->name('produits.destroy');
    Route::put('/produits/{id}/restore', [ProduitController::class, 'restore'])->name('produits.restore');
    // Ajout de la route pour la suppression définitive
    Route::delete('/produits/{id}/delete', [ProduitController::class, 'delete'])->name('produits.delete');
    // Route pour afficher les produits archivés
    Route::get('/produits/archives', [ProduitController::class, 'archives'])->name('produits.archives');
});

// Cette route est maintenant APRÈS toutes les routes statiques de produits
Route::get('/produits/{produit}', [ProduitController::class, 'show'])->where('produit', '[0-9]+')->name('produits.show');

// Routes pour les catégories (administration)
Route::middleware(['auth', 'gestionnaire'])->resource('categories', CategorieController::class);

// Routes pour les commandes (administration)
Route::middleware(['auth', 'gestionnaire'])->group(function () {
    Route::put('/commandes/{commande}/status', [CommandeController::class, 'updateStatus'])->name('commandes.status.update');
    Route::delete('/commandes/{commande}', [CommandeController::class, 'cancel'])->name('commandes.cancel');
});

// Routes pour les paiements (administration)
Route::middleware(['auth', 'gestionnaire'])->group(function () {
    Route::get('/commandes/{commande}/paiement', [PaiementController::class, 'create'])->name('paiements.create');
    Route::post('/commandes/{commande}/paiement', [PaiementController::class, 'store'])->name('paiements.store');
});

// Routes pour les statistiques (administration)
Route::middleware(['auth', 'gestionnaire'])->get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');

// Inclure les routes d'authentification de Breeze
require __DIR__.'/auth.php';

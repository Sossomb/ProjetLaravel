<?php

namespace App\Http\Controllers;

use App\Models\Panier;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PanierController extends Controller
{
    public function index()
    {
        // Vérification explicite de l'authentification
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder à votre panier.');
        }

        $user = Auth::user();
        $panierItems = $user->panier()->with('produit')->get();

        // Debug: Loggez les informations du panier
        Log::info('Panier de l\'utilisateur ' . $user->id . ': ' . $panierItems->count() . ' articles');

        $items = [];
        $total = 0;

        foreach ($panierItems as $item) {
            if ($item->produit) { // Vérifier que le produit existe toujours
                $sousTotal = $item->produit->prix * $item->quantite;
                $total += $sousTotal;

                $items[$item->produit->id] = [
                    'produit' => $item->produit,
                    'quantite' => $item->quantite,
                    'sous_total' => $sousTotal
                ];
            }
        }

        return view('panier.index', compact('items', 'total'));
    }

    public function ajouter(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
        ]);

        // Vérification explicite de l'authentification
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour ajouter des produits au panier.');
        }

        $produit = Produit::findOrFail($request->produit_id);

        if ($produit->stock < $request->quantite) {
            return back()->with('error', 'Stock insuffisant. Disponible: ' . $produit->stock);
        }

        $user = Auth::user();
        $panierItem = $user->panier()->where('produit_id', $produit->id)->first();

        // Debug: Loggez l'ajout au panier
        Log::info('Tentative d\'ajout au panier: User=' . $user->id . ', Produit=' . $produit->id . ', Quantité=' . $request->quantite);

        if ($panierItem) {
            $nouvelleQuantite = $panierItem->quantite + $request->quantite;

            if ($nouvelleQuantite > $produit->stock) {
                return back()->with('error', 'Stock insuffisant. Disponible: ' . $produit->stock);
            }

            $panierItem->update(['quantite' => $nouvelleQuantite]);
            Log::info('Panier mis à jour: Item=' . $panierItem->id . ', Nouvelle quantité=' . $nouvelleQuantite);
        } else {
            $item = $user->panier()->create([
                'produit_id' => $produit->id,
                'quantite' => $request->quantite,
            ]);
            Log::info('Nouvel item ajouté au panier: ' . $item->id);
        }

        // Redirection explicite vers la page du panier au lieu de retourner à la page précédente
        return redirect()->route('panier.index')->with('success', 'Produit ajouté au panier.');
    }

    public function supprimer($produitId)
    {
        $user = Auth::user();
        $user->panier()->where('produit_id', $produitId)->delete();

        return back()->with('success', 'Produit retiré du panier.');
    }

    public function mettreAJour(Request $request)
    {
        $request->validate([
            'quantites' => 'required|array',
            'quantites.*' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        foreach ($request->quantites as $produitId => $quantite) {
            $panierItem = $user->panier()->where('produit_id', $produitId)->first();

            if ($panierItem) {
                $produit = Produit::find($produitId);
                if (!$produit || $quantite > $produit->stock) {
                    return back()->with('error', 'Stock insuffisant pour ' . ($produit->nom ?? 'ce produit'));
                }

                $panierItem->update(['quantite' => $quantite]);
            }
        }

        return back()->with('success', 'Panier mis à jour.');
    }

    public function vider()
    {
        Auth::user()->panier()->delete();
        return back()->with('success', 'Panier vidé.');
    }
}

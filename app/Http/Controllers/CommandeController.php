<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\DetailCommande;
use App\Models\Produit;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Mail\NouvelleCommande;
use App\Mail\CommandePrete;
use App\Mail\FactureCommande;
use Barryvdh\DomPDF\Facade\PDF;

class CommandeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::user()->estGestionnaire()) {
            // Si l'utilisateur est un gestionnaire, il voit toutes les commandes
            $commandes = Commande::with('client')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            // Si c'est un client, il ne voit que ses commandes
            $commandes = Commande::where('client_id', Auth::id())->orderBy('created_at', 'desc')->paginate(10);
        }
        return view('commandes.index', compact('commandes'));
    }

    public function checkout()
    {
        // Récupérer les éléments du panier depuis la base de données
        $panierItems = auth()->user()->panier()
            ->with('produit')  // Eager loading de la relation produit
            ->get();

        // Vérifier si le panier contient des produits
        if ($panierItems->isEmpty()) {
            return redirect()->route('panier.index')->with('warning', 'Votre panier est vide.');
        }

        // Formater les données pour la vue
        $items = [];
        foreach ($panierItems as $item) {
            $items[$item->id] = [
                'produit' => $item->produit,
                'quantite' => $item->quantite,
                'sous_total' => $item->produit->prix * $item->quantite
            ];
        }

        return view('commandes.checkout', compact('items'));
    }

    public function store(Request $request)
    {
        try {
            // Valider les données du formulaire
            $validated = $request->validate([
                'adresse_livraison' => 'required|string|max:255',
                'telephone' => 'required|string|max:20',
                'instructions' => 'nullable|string',
            ]);

            // Débuter une transaction pour s'assurer que toutes les opérations se font ensemble
            DB::beginTransaction();

            // Récupérer les éléments du panier
            $panierItems = auth()->user()->panier()->with('produit')->get();

            // Vérifier si le panier est vide
            if ($panierItems->isEmpty()) {
                return redirect()->back()->with('error', 'Votre panier est vide.');
            }

            // Vérifier la disponibilité des produits
            foreach ($panierItems as $item) {
                $produit = Produit::find($item->produit_id);
                if ($produit->stock < $item->quantite) {
                    return redirect()->back()->with('error', 'Le produit "' . $produit->nom . '" n\'est pas disponible en quantité suffisante.');
                }
            }

            // Calculer le montant total
            $montantTotal = 0;
            foreach ($panierItems as $item) {
                $montantTotal += $item->produit->prix * $item->quantite;
            }

            // Créer la commande
            $commande = new Commande();
            $commande->client_id = auth()->id();
            $commande->montant_total = $montantTotal;
            $commande->statut = 'en_attente';
            $commande->adresse_livraison = $validated['adresse_livraison'];
            $commande->telephone = $validated['telephone'];
            $commande->instructions = $validated['instructions'] ?? null;
            $commande->numero = 'CMD-' . Str::upper(Str::random(8));
            $commande->date_commande = now();
            $commande->save();

            // Ajouter les produits à la commande
            foreach ($panierItems as $item) {
                $detailCommande = new DetailCommande();
                $detailCommande->commande_id = $commande->id;
                $detailCommande->produit_id = $item->produit_id;
                $detailCommande->quantite = $item->quantite;
                $detailCommande->prix_unitaire = $item->produit->prix;
                $detailCommande->sous_total = $item->produit->prix * $item->quantite;
                $detailCommande->save();

                // Mettre à jour le stock du produit
                $produit = Produit::find($item->produit_id);
                $produit->stock -= $item->quantite;
                $produit->save();
            }

            // Vider le panier
            auth()->user()->panier()->delete();

            // Envoyer l'email de confirmation
            Mail::to(auth()->user()->email)->send(new NouvelleCommande($commande));

            // Valider la transaction
            DB::commit();

            return redirect()->route('commandes.show', $commande->id)
                ->with('success', 'Votre commande a été créée avec succès! Un email de confirmation vous a été envoyé.');
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();

            // Rediriger avec un message d'erreur
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de votre commande: ' . $e->getMessage());
        }
    }

    public function show(Commande $commande)
    {
        // Vérifier que l'utilisateur actuel est le propriétaire de la commande
        // ou qu'il a les droits d'administration
        if (Auth::id() !== $commande->client_id && !Auth::user()->estGestionnaire()) {
            abort(403, 'Accès non autorisé');
        }

        // Charger les détails de la commande avec les produits
        $commande->load(['details.produit']);  // Chargement des produits via la relation details

        return view('commandes.show', compact('commande'));
    }

    public function updateStatus(Request $request, Commande $commande)
    {
        // Vérifier que l'utilisateur a les droits d'administration
        if (!Auth::user()->estGestionnaire()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'statut' => 'required|in:en_attente,en_cours,en_preparation,prete,livree,payee,annulee',
        ]);

        $ancienStatut = $commande->statut;
        $nouveauStatut = $request->statut;

        $commande->update([
            'statut' => $nouveauStatut,
        ]);

        // Charger les informations du client
        $commande->load('client');

        // Envoyer une notification par email au client selon le statut
        if ($nouveauStatut === 'prete') {
            // Utilisez une classe Mailable au lieu d'une classe Notification
            Mail::to($commande->client->email)->send(new CommandePrete($commande));
        } else {
            // Pour les autres statuts, vous pouvez utiliser NouvelleCommande
            Mail::to($commande->client->email)->send(new NouvelleCommande($commande, true, $nouveauStatut));
        }

        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Le statut de la commande a été mis à jour avec succès et le client a été notifié.');
    }

    public function repeat(Commande $commande)
    {
        // Débuter une transaction pour s'assurer que toutes les opérations se font ensemble
        DB::beginTransaction();

        try {
            // Logique pour recréer la commande
            $newCommande = new Commande();
            $newCommande->client_id = auth()->id();
            $newCommande->adresse_livraison = $commande->adresse_livraison;
            $newCommande->telephone = $commande->telephone;
            $newCommande->instructions = $commande->instructions;
            $newCommande->statut = 'en_attente';
            $newCommande->date_commande = now();
            $newCommande->numero = 'CMD-' . Str::upper(Str::random(8));
            $newCommande->montant_total = 0; // Sera calculé à partir des détails
            $newCommande->save();

            // Récupérer les détails de la commande originale
            $commande->load('details.produit');
            $montantTotal = 0;

            // Copier les détails de la commande d'origine
            foreach ($commande->details as $detail) {
                // Vérifier la disponibilité du produit
                $produit = Produit::find($detail->produit_id);
                if (!$produit || $produit->stock < $detail->quantite) {
                    throw new \Exception('Le produit "' . ($produit ? $produit->nom : 'Inconnu') . '" n\'est pas disponible en quantité suffisante.');
                }

                $newDetail = new DetailCommande();
                $newDetail->commande_id = $newCommande->id;
                $newDetail->produit_id = $detail->produit_id;
                $newDetail->quantite = $detail->quantite;
                $newDetail->prix_unitaire = $produit->prix; // Utiliser le prix actuel du produit
                $newDetail->sous_total = $produit->prix * $detail->quantite;
                $newDetail->save();

                // Mettre à jour le stock
                $produit->stock -= $detail->quantite;
                $produit->save();

                $montantTotal += $newDetail->sous_total;
            }

            // Mettre à jour le montant total
            $newCommande->montant_total = $montantTotal;
            $newCommande->save();

            // Envoyer l'email de confirmation
            Mail::to(auth()->user()->email)->send(new NouvelleCommande($newCommande));

            // Valider la transaction
            DB::commit();

            return redirect()->route('commandes.show', $newCommande->id)
                ->with('success', 'Votre commande a été recréée avec succès ! Un email de confirmation vous a été envoyé.');
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();

            return redirect()->route('commandes.index')
                ->with('error', 'Une erreur est survenue lors de la création de votre commande: ' . $e->getMessage());
        }
    }

    public function adminIndex()
    {
        // Vérifier que l'utilisateur a les droits d'administration
        if (!Auth::user()->estGestionnaire()) {
            abort(403, 'Accès non autorisé');
        }

        $commandes = Commande::with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.commandes.index', compact('commandes'));
    }

    public function cancel(Commande $commande)
    {
        // Vérifier que l'utilisateur actuel est le propriétaire de la commande
        if (Auth::id() !== $commande->client_id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la commande peut être annulée (statut en_attente uniquement)
        if ($commande->statut !== 'en_attente') {
            return redirect()->route('commandes.show', $commande)
                ->with('error', 'Seules les commandes en attente peuvent être annulées.');
        }

        try {
            DB::beginTransaction();

            // Mettre à jour le statut de la commande
            $commande->update(['statut' => 'annulee']);

            // Réintégrer les produits en stock
            foreach ($commande->details as $detail) {
                $produit = Produit::find($detail->produit_id);
                if ($produit) {
                    $produit->stock += $detail->quantite;
                    $produit->save();
                }
            }

            // Notifier le client par email (notification d'annulation)
            Mail::to(auth()->user()->email)->send(new NouvelleCommande($commande, true, 'annulee'));

            DB::commit();

            return redirect()->route('commandes.index')
                ->with('success', 'Votre commande a été annulée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('commandes.show', $commande)
                ->with('error', 'Une erreur est survenue lors de l\'annulation de votre commande: ' . $e->getMessage());
        }
    }

    // Générer une facture PDF
    public function generateInvoice(Commande $commande)
    {
        // Vérifier que l'utilisateur peut voir la facture
        if (Auth::id() !== $commande->client_id && !Auth::user()->estGestionnaire()) {
            abort(403, 'Accès non autorisé');
        }

        // Charger la commande avec les détails
        $commande->load(['details.produit', 'client']);

        // Générer le PDF
        $pdf = PDF::loadView('factures.facture', compact('commande'));

        // Télécharger la facture en PDF
        return $pdf->download('Facture_' . $commande->numero . '.pdf');
    }

    // Envoyer la facture par email
    public function sendInvoice(Commande $commande)
    {
        if (Auth::id() !== $commande->client_id && !Auth::user()->estGestionnaire()) {
            abort(403, 'Accès non autorisé');
        }

        try {
            // Charger la commande avec les détails
            $commande->load(['details.produit', 'client']);

            // Générer le PDF
            $pdf = PDF::loadView('factures.facture', compact('commande'));
            $pdfContent = $pdf->output();

            // Envoyer l'email avec la facture en pièce jointe
            Mail::to($commande->client->email)->send(new FactureCommande($commande, $pdfContent));

            return redirect()->route('commandes.show', $commande)
                ->with('success', 'La facture a été envoyée par e-mail avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('commandes.show', $commande)
                ->with('error', 'Une erreur est survenue lors de l\'envoi de la facture: ' . $e->getMessage());
        }
    }

    // Afficher la facture
    public function facture(Commande $commande)
    {
        // Vérifier que l'utilisateur peut voir la facture
        if (Auth::id() !== $commande->client_id && !Auth::user()->estGestionnaire()) {
            abort(403, 'Accès non autorisé');
        }

        // Charger la commande avec les détails
        $commande->load(['details.produit', 'client']);

        return view('factures.facture', compact('commande'));
    }
}

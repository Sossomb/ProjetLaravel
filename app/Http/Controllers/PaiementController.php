<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Paiement;
use App\Mail\FactureCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\PDF;

class PaiementController extends Controller
{
    /**
     * Affiche le formulaire pour créer un nouveau paiement
     *
     * @param Commande $commande
     * @return \Illuminate\View\View
     */
    public function create(Commande $commande)
    {
        // Vérification si la commande est déjà payée
        if ($commande->paiement) {
            return redirect()->route('commandes.show', $commande)
                ->with('warning', 'Cette commande a déjà été payée.');
        }

        return view('paiements.create', compact('commande'));
    }

    // ... Autres méthodes inchangées ...

    public function store(Request $request, Commande $commande)
    {
        // Vérifier si la commande appartient à l'utilisateur ou si l'utilisateur est un gestionnaire
        if (Auth::id() !== $commande->client_id && !Auth::user()->estGestionnaire()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier si la commande est déjà payée
        if ($commande->paiement) {
            return redirect()->route('commandes.show', $commande)
                ->with('warning', 'Cette commande a déjà été payée.');
        }

        $validated = $request->validate([
            'montant' => 'required|numeric|min:' . $commande->montant_total,
            'mode' => 'required|in:especes,carte,mobile',
            'commentaire' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Créer le paiement avec la correction des noms de champs et l'ajout de date_paiement
            Paiement::create([
                'commande_id' => $commande->id,
                'montant' => $validated['montant'],
                'mode_paiement' => $validated['mode'],
                'date_paiement' => now(),
                'commentaire' => $validated['commentaire'] ?? null,
            ]);

            // Mettre à jour le statut de la commande
            $commande->update(['statut' => 'payee']);

            // Charger la commande avec ses relations
            $commande->load(['client', 'detailCommandes.produit', 'paiement']);

            // Générer le PDF de facture
            $pdf = PDF::loadView('factures.facture', ['commande' => $commande]);
            $pdfContent = $pdf->output();

            // Envoyer l'email avec la facture PDF en pièce jointe
            Mail::to($commande->client->email)->send(new FactureCommande($commande, $pdfContent));

            DB::commit();

            return redirect()->route('commandes.show', $commande)
                ->with('success', 'Paiement enregistré avec succès et facture envoyée par email.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement du paiement: ' . $e->getMessage());
        }
    }
}

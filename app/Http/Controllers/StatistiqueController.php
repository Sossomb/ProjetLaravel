<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Produit;
use App\Models\Paiement;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('gestionnaire');
    }

    public function index()
    {
        // Commandes en cours de la journée
        $commandesEnCours = Commande::whereIn('statut', ['en_attente', 'en_preparation'])
            ->whereDate('date_commande', Carbon::today())
            ->count();

        // Commandes validées de la journée
        $commandesValidees = Commande::whereIn('statut', ['prete', 'payee'])
            ->whereDate('date_commande', Carbon::today())
            ->count();

        // Recettes journalières (paiements reçus)
        $recettesJour = Paiement::whereDate('date_paiement', Carbon::today())
            ->sum('montant');

        // Nombre de commandes par mois pour l'année en cours
        $commandesParMois = Commande::select(
            DB::raw('EXTRACT(MONTH FROM date_commande) as mois'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('date_commande', Carbon::now()->year)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // Formater les données pour ChartJS
        $moisData = [];
        $commandeData = [];

        foreach ($commandesParMois as $item) {
            $moisData[] = Carbon::create()->month($item->mois)->format('F');
            $commandeData[] = $item->total;
        }

        // Nombre de produits par catégorie
        $produitsParCategorie = Produit::select(
            'categories.libelle',
            DB::raw('COUNT(produits.id) as total')
        )
            ->join('categories', 'produits.categorie_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.libelle')
            ->get();

        // Formater pour ChartJS
        $categorieLabels = $produitsParCategorie->pluck('libelle')->toArray();
        $produitCounts = $produitsParCategorie->pluck('total')->toArray();

        return view('statistiques.index', compact(
            'commandesEnCours',
            'commandesValidees',
            'recettesJour',
            'moisData',
            'commandeData',
            'categorieLabels',
            'produitCounts'
        ));
    }

    public function dashboard()
    {
        // Récupérer les données nécessaires pour le tableau de bord
        $commandesJour = Commande::whereDate('date_commande', today())->count();
        $commandesValidees = Commande::whereDate('date_commande', today())->where('statut', 'prete')->count();
        $commandesEnAttente = Commande::whereDate('date_commande', today())->where('statut', 'en_attente')->count();

        // Recettes journalières (commandes payées)
        $recettesJour = Commande::whereDate('date_commande', today())
            ->where('statut', 'payee')
            ->sum('montant_total');

        // Récupérer les dernières commandes pour le tableau
        $dernieresCommandes = Commande::with('client')
            ->orderBy('date_commande', 'desc')
            ->limit(10)
            ->get();

        // Récupérer les produits en rupture de stock
        $produitsEnRupture = Produit::where('stock', '<=', 0)
            ->with('categorie')
            ->get();

        // Données pour les graphiques
        // Commandes par mois
        $commandesMensuelles = Commande::select(
            DB::raw('EXTRACT(MONTH FROM date_commande) as mois'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('date_commande', Carbon::now()->year)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        $commandesMensuellesLabels = [];
        $commandesMensuellesData = [];

        foreach ($commandesMensuelles as $data) {
            $commandesMensuellesLabels[] = Carbon::create()->month($data->mois)->format('F');
            $commandesMensuellesData[] = $data->total;
        }

        // Produits par catégorie
        $produitsParCategorie = Categorie::withCount('produits')
            ->get();

        $produitsParCategorieLabels = $produitsParCategorie->pluck('libelle')->toArray();
        $produitsParCategorieData = $produitsParCategorie->pluck('produits_count')->toArray();

        return view('dashboard', compact(
            'commandesJour',
            'commandesValidees',
            'commandesEnAttente',
            'recettesJour',
            'dernieresCommandes',
            'produitsEnRupture',
            'commandesMensuellesLabels',
            'commandesMensuellesData',
            'produitsParCategorieLabels',
            'produitsParCategorieData'
        ));
    }
}

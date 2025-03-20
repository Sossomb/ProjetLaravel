<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::with('categorie')->where('archive', false);

        if ($request->filled('libelle')) {
            $query->where('nom', 'like', '%' . $request->libelle . '%');
        }

        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }

        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        $produits = $query->paginate(12);
        $categories = Categorie::all();

        return view('produits.index', compact('produits', 'categories'));
    }

    public function show(Produit $produit)
    {
        return view('produits.show', compact('produit'));
    }

    public function create()
    {
        $this->authorize('create', Produit::class);
        $categories = Categorie::all();
        return view('produits.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Produit::class);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('produits', 'public');
            $validated['image'] = $path;
        }

        Produit::create($validated);

        return redirect()->route('produits.index')
            ->with('success', 'Produit ajouté avec succès.');
    }

    public function edit(Produit $produit)
    {
        $this->authorize('update', $produit);
        $categories = Categorie::all();
        return view('produits.edit', compact('produit', 'categories'));
    }

    public function update(Request $request, Produit $produit)
    {
        $this->authorize('update', $produit);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($produit->image) {
                Storage::disk('public')->delete($produit->image);
            }
            $path = $request->file('image')->store('produits', 'public');
            $validated['image'] = $path;
        }

        $produit->update($validated);

        return redirect()->route('produits.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Produit $produit)
    {
        $this->authorize('delete', $produit);

        // On préfère archiver plutôt que supprimer
        $produit->update(['archive' => true]);

        return redirect()->route('produits.index')
            ->with('success', 'Produit archivé avec succès.');
    }

    public function restore($id)
    {
        $produit = Produit::where('archive', true)->findOrFail($id);
        $this->authorize('restore', $produit);

        $produit->update(['archive' => false]);

        return redirect()->route('produits.index')
            ->with('success', 'Produit restauré avec succès.');
    }

    public function delete($id)
    {
        $produit = Produit::findOrFail($id);
        $this->authorize('forceDelete', $produit);

        // Supprimer l'image si elle existe
        if ($produit->image) {
            Storage::disk('public')->delete($produit->image);
        }

        // Suppression définitive
        $produit->forceDelete();

        return redirect()->route('produits.index')
            ->with('success', 'Produit supprimé définitivement.');
    }

    public function archives()
    {
        $this->authorize('viewArchived', Produit::class);

        $produits = Produit::with('categorie')
            ->where('archive', true)
            ->paginate(12);

        $categories = Categorie::all();

        return view('produits.archives', compact('produits', 'categories'));
    }
}

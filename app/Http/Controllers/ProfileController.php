<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Récupérer les commandes récentes de l'utilisateur
        $commandes = $request->user()->commandes()->latest()->take(5)->get();

        return view('profile.edit', [
            'user' => $request->user(),
            'commandes' => $commandes,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the client dashboard.
     */
    public function clientDashboard()
    {
        // Récupérer les informations nécessaires pour le tableau de bord client
        $user = auth()->user();
        $commandes = $user->commandes()->latest()->take(5)->get();

        // Assurez-vous que le chemin de la vue est correct
        return view('client.dashboard', compact('user', 'commandes'));
    }
}

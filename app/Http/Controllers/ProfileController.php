<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request): View
    {
        $user = $request->user();
        $teletravailleur = $user->teletravailleur;

        return view('profile.show', [
            'user' => $user,
            'teletravailleur' => $teletravailleur,
        ]);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();

    try {
        // Log des données reçues
        Log::info('Données reçues pour mise à jour', $request->all());

        // Validation des champs
        $rules = [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:utilisateurs,email,' . $user->id,
            'photoProfil' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        if ($user->teletravailleur) {
            $rules = array_merge($rules, [
                'cin' => 'required|string|max:20|unique:teletravailleurs,CIN,' . ($user->teletravailleur->id ?? ''),
                'telephone' => 'required|string|max:20',
                'adresse' => 'required|string|max:255',
            ]);
        }

        $validatedData = $request->validate($rules);
        Log::info('Données validées avec succès', $validatedData);

        // Mise à jour des champs communs dans utilisateurs
        $user->nom = $validatedData['nom'];
        $user->prenom = $validatedData['prenom'];
        $user->email = $validatedData['email'];

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Mise à jour des champs spécifiques au télétravailleur
        if ($user->teletravailleur) {
            $teletravailleur = $user->teletravailleur;
            $teletravailleur->CIN = $validatedData['cin'];
            $teletravailleur->telephone = $validatedData['telephone'];
            $teletravailleur->adresse = $validatedData['adresse'];

            // Gestion de la photo de profil dans teletravailleur
            if ($request->hasFile('photoProfil')) {
                if ($teletravailleur->photoProfil) {
                    Storage::disk('public')->delete($teletravailleur->photoProfil);
                    Log::info('Ancienne photo supprimée', ['path' => $teletravailleur->photoProfil]);
                }
                $path = $request->file('photoProfil')->store('avatars', 'public');
                $teletravailleur->photoProfil = $path;
                Log::info('Nouvelle photo uploadée', ['path' => $path]);
            }else {
                Log::info('Aucune nouvelle photo téléchargée', ['photoProfil_actuel' => $teletravailleur->photoProfil]);
            }

            $teletravailleur->save();
            Log::info('Télétravailleur mis à jour', ['teletravailleur' => $teletravailleur->fresh()->toArray()]);
        }

        // Sauvegarder les modifications de l'utilisateur
        $user->save();
        Log::info('Utilisateur mis à jour avec succès', ['user' => $user->fresh()->toArray()]);

        // Redirection conditionnelle
        if ($user->teletravailleur) {
            Log::info('Redirection vers le tableau de bord télétravailleur');
            return Redirect::route('teletravailleur.dashboard')->with('status', 'Profil mis à jour avec succès.');
        }

        Log::info('Redirection vers profile.show');
        return Redirect::route('profile.show')->with('status', 'Profil mis à jour avec succès.');
    } catch (\Exception $e) {
        Log::error('Erreur lors de la mise à jour du profil pour l\'utilisateur ' . $user->id . ' : ' . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
        return Redirect::route('profile.edit')->with('error', 'Erreur lors de la mise à jour du profil. Veuillez réessayer.');
    }
}
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
}

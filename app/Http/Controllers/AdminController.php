<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use App\Models\Teletravailleur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeTeletravailleur;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function getStatus()
    {
        $teletravailleurs = Teletravailleur::with('user')->get();
        return response()->json([
            'teletravailleurs' => $teletravailleurs->map(function ($teletravailleur) {
                return [
                    'id' => $teletravailleur->id,
                    'statut' => $teletravailleur->user->statut,
                    'isOnline' => $teletravailleur->user->isOnline(),
                    'last_activity' => $teletravailleur->user->last_activity,
                ];
            })
        ]);
    }

    public function dashboard()
    {
        $admin = Auth::user()->administrateur;
        $teletravailleurs = Utilisateur::with(['teletravailleur.screenshots', 'teletravailleur.workingHours'])
            ->whereHas('teletravailleur')
            ->get();

        return view('admin.dashboard', compact('teletravailleurs'));
    }

    public function index()
    {
        $teletravailleurs = Teletravailleur::with('utilisateur')->paginate(10);
        return view('admin.teletravailleurs.index', compact('teletravailleurs'));
    }

    public function showCreateTeletravailleurForm()
    {
        return view('admin.teletravailleurs.create');
    }

    public function storeTeletravailleur(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:utilisateurs',
            'CIN' => 'nullable|string|max:255|unique:teletravailleurs,CIN',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'photoProfil' => 'nullable|url',
        ]);

        $password = Str::random(10);

        // Valider explicitement le mot de passe généré (pour plus de sécurité)
        $passwordValidator = \Validator::make(['password' => $password], [
            'password' => 'required|string|min:8',
        ]);

        if ($passwordValidator->fails()) {
            \Log::error('Le mot de passe généré ne respecte pas les critères : ' . $password);
            return redirect()->route('admin.dashboard')->with('error', 'Erreur lors de la création du mot de passe.');
        }

        // Crée un utilisateur
        try {
            $user = Utilisateur::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'password' => $password,
                'statut' => false,
            ]);

            \Log::info('Utilisateur créé avec succès : ID ' . $user->id . ', Email : ' . $user->email);

            // Créer un télétravailleur avec un token unique
            $token = Str::random(60);
            $teletravailleur = Teletravailleur::create([
                'user_id' => $user->id,
                'nom' => $request->nom,
                'email' => $request->email,
                'token' => $token,
                'CIN' => $request->CIN,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'photoProfil' => $request->photoProfil,
            ]);

            \Log::info('Télétravailleur créé avec succès : ID ' . $teletravailleur->id . ', Token : ' . $token);

            // Envoyer un email de bienvenue avec le mot de passe et le lien de complétion
            $completionLink = route('teletravailleur.complete', ['token' => $token]);
            \Log::info('Lien de complétion généré : ' . $completionLink);

            Mail::to($user->email)->send(new WelcomeTeletravailleur($user, $password, $completionLink));
            \Log::info('Email envoyé à : ' . $user->email);

            return redirect()->route('admin.dashboard')->with('success', 'Télétravailleur créé avec succès. Un email a été envoyé pour compléter le profil.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du télétravailleur : ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function destroyTeletravailleur($id)
    {
        $teletravailleur = Teletravailleur::findOrFail($id);

        try {
            // Supprimer la photo de profil si elle existe
            if ($teletravailleur->photoProfil) {
                Storage::disk('public')->delete($teletravailleur->photoProfil);
            }

            // Vérifier et supprimer l'utilisateur lié
            if ($teletravailleur->utilisateur) {
                $utilisateur = $teletravailleur->utilisateur;
                Log::info('Télétravailleur et utilisateur supprimés : ' . $utilisateur->nom . ' ' . $utilisateur->prenom);
                $utilisateur->delete();
            }

            // Supprimer le télétravailleur
            $teletravailleur->delete();

            return redirect()->route('admin.teletravailleurs.index')->with('success', 'Télétravailleur et utilisateur supprimés avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression : ' . $e->getMessage());
            return redirect()->route('admin.teletravailleurs.index')->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }



}

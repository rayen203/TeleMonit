<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use App\Models\Teletravailleur;
use App\Models\WorkingHour;
use App\Models\Chatbot;
use App\Models\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TeletravailleurController extends Controller
{
    public function __construct()
{
    $this->middleware('auth')->except([
        'showChangePasswordForm',
        'changePassword',
        'showCompleteProfileForm',
        'completeProfile',
        'showUploadPhotoForm',
        'uploadPhoto'
    ]);
    $this->middleware('teletravailleur')->except([
        'showChangePasswordForm',
        'changePassword',
        'showCompleteProfileForm',
        'completeProfile',
        'showUploadPhotoForm',
        'uploadPhoto',
        'details'
    ]);
}


public function dashboard(Request $request)
{
    $utilisateur = Auth::user();
    $teletravailleur = $utilisateur->teletravailleur;

    if (!$teletravailleur) {
        abort(404, 'Télétravailleur non trouvé.');
    }

    $utilisateur->updateStatut();


    $workingHours = WorkingHour::where('teletravailleur_id', $teletravailleur->id)
        ->whereNotNull('stop_time')
        ->orderBy('date', 'desc')
        ->orderBy('start_time', 'desc')
        ->paginate(10);


    $currentPage = $request->input('page', 1);
    if ($currentPage > $workingHours->lastPage()) {
        return redirect()->route('teletravailleur.dashboard', ['page' => 1]);
    }

    \Log::info('Données de l’historique des heures travaillées (requête directe)', [
        'teletravailleur_id' => $teletravailleur->id,
        'workingHours' => $workingHours->toArray(),
    ]);


    $todaySessions = WorkingHour::where('teletravailleur_id', $teletravailleur->id)
        ->where('date', now()->toDateString())
        ->whereNotNull('stop_time')
        ->get();

    $todaySeconds = 0;
    foreach ($todaySessions as $session) {
        $effectiveSeconds = $session->total_seconds - ($session->pause_total_seconds ?? 0);
        $todaySeconds += max(0, $effectiveSeconds);
    }

    \Log::info('Total aujourd\'hui calculé', [
        'todaySeconds' => $todaySeconds,
        'sessions' => $todaySessions->toArray(),
    ]);


    if ($todaySeconds >= 3600) {
        $hours = floor($todaySeconds / 3600);
        $remainingSeconds = $todaySeconds % 3600;
        $minutes = floor($remainingSeconds / 60);
        $seconds = $remainingSeconds % 60;
        $todayFormatted = "$hours heure" . ($hours > 1 ? "s" : "");
        if ($minutes > 0) {
            $todayFormatted .= " et $minutes minute" . ($minutes > 1 ? "s" : "");
        }
        if ($seconds > 0) {
            $todayFormatted .= " et $seconds seconde" . ($seconds > 1 ? "s" : "");
        }
    } elseif ($todaySeconds >= 60) {
        $minutes = floor($todaySeconds / 60);
        $seconds = $todaySeconds % 60;
        $todayFormatted = "$minutes minute" . ($minutes > 1 ? "s" : "");
        if ($seconds > 0) {
            $todayFormatted .= " et $seconds seconde" . ($seconds > 1 ? "s" : "");
        }
    } else {
        $todayFormatted = "$todaySeconds seconde" . ($todaySeconds > 1 ? "s" : "");
    }


    $monthlySessions = WorkingHour::where('teletravailleur_id', $teletravailleur->id)
        ->whereMonth('date', now()->month)
        ->whereYear('date', now()->year)
        ->whereNotNull('stop_time')
        ->get();

    $monthlySeconds = 0;
    foreach ($monthlySessions as $session) {
        $effectiveSeconds = $session->total_seconds - ($session->pause_total_seconds ?? 0);
        $monthlySeconds += max(0, $effectiveSeconds);
    }


    if ($monthlySeconds >= 3600) {
        $hours = floor($monthlySeconds / 3600);
        $remainingSeconds = $monthlySeconds % 3600;
        $minutes = floor($remainingSeconds / 60);
        $seconds = $remainingSeconds % 60;
        $monthlyFormatted = "$hours heure" . ($hours > 1 ? "s" : "");
        if ($minutes > 0) {
            $monthlyFormatted .= " et $minutes minute" . ($minutes > 1 ? "s" : "");
        }
        if ($seconds > 0) {
            $monthlyFormatted .= " et $seconds seconde" . ($seconds > 1 ? "s" : "");
        }
    } elseif ($monthlySeconds >= 60) {
        $minutes = floor($monthlySeconds / 60);
        $seconds = $monthlySeconds % 60;
        $monthlyFormatted = "$minutes minute" . ($minutes > 1 ? "s" : "");
        if ($seconds > 0) {
            $monthlyFormatted .= " et $seconds seconde" . ($seconds > 1 ? "s" : "");
        }
    } else {
        $monthlyFormatted = "$monthlySeconds seconde" . ($monthlySeconds > 1 ? "s" : "");
    }


    $todayHours = $todaySeconds / 3600;
    $notification = '';
    if ($todayHours >= 8) {
        $notification = "Félicitations ! Vous avez atteint 8 heures de travail aujourd'hui. Il est temps de prendre un repos bien mérité.";
    } elseif ($todayHours >= 4) {
        $notification = "Vous avez travaillé 4 heures aujourd'hui. Il est recommandé de prendre une pause pour un repas.";
    }

    $screenshots = method_exists($teletravailleur, 'screenshots')
        ? $teletravailleur->screenshots()->paginate(10)
        : collect();

    $chatbot = class_exists(Chatbot::class)
        ? Chatbot::where('teletravailleur_id', $teletravailleur->id)->first()
        : null;

    return view('teletravailleur.dashboard', [
        'user' => $utilisateur,
        'teletravailleur' => $teletravailleur,
        'workingHours' => $workingHours,
        'todayFormatted' => $todayFormatted,
        'monthlyFormatted' => $monthlyFormatted,
        'notification' => $notification,
        'screenshots' => $screenshots,
        'chatbot' => $chatbot,
    ]);
}



    public function showChangePasswordForm($token)
{
    $teletravailleur = Teletravailleur::where('token', $token)->first();

    if (!$teletravailleur) {
        return redirect()->route('login')->withErrors(['error' => 'Le lien de complétion est invalide ou a déjà été utilisé. Veuillez vous connecter.']);
    }

    return view('teletravailleur.complete_password', compact('token'));
}

public function changePassword(Request $request, $token)
{
    $teletravailleur = Teletravailleur::where('token', $token)->first();

    if (!$teletravailleur) {
        return redirect()->route('login')->withErrors(['error' => 'Le lien de complétion est invalide ou a déjà été utilisé. Veuillez vous connecter.']);
    }

    if (!$teletravailleur->utilisateur) {
        return redirect()->back()->withErrors(['error' => 'Utilisateur associé non trouvé.']);
    }

    $request->validate([
        'old_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    if (!Hash::check($request->old_password, $teletravailleur->utilisateur->password)) {
        return redirect()->back()->withErrors(['old_password' => 'L’ancien mot de passe est incorrect']);
    }

    $teletravailleur->utilisateur->update([
        'password' => $request->new_password,
    ]);

    return redirect()->route('teletravailleur.complete.info', ['token' => $token]);
}
    public function showCompleteProfileForm($token)
    {
        $teletravailleur = Teletravailleur::where('token', $token)->firstOrFail();

        if ($teletravailleur->CIN && $teletravailleur->telephone && $teletravailleur->adresse) {
            return redirect()->route('teletravailleur.upload.photo.form', ['token' => $token]);
        }

        return view('teletravailleur.complete_info', compact('token'));
    }

    public function completeProfile(Request $request, $token)
    {
        $teletravailleur = Teletravailleur::where('token', $token)->first();

        if (!$teletravailleur) {
            return redirect()->route('login')->withErrors(['error' => 'Le lien de complétion est invalide ou a déjà été utilisé. Veuillez vous connecter.']);
        }

        $request->validate([
            'cin' => 'required|string|max:20|unique:teletravailleurs,CIN,' . $teletravailleur->id,
            'telephone' => 'required|string|max:15',
            'adresse' => 'required|string|max:255',
        ]);

        $teletravailleur->update([
            'CIN' => $request->cin,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
        ]);

        return redirect()->route('teletravailleur.upload.photo.form', ['token' => $token]);
    }

    public function showUploadPhotoForm($token)
    {
        $teletravailleur = Teletravailleur::where('token', $token)->first();

        if (!$teletravailleur) {
            return redirect()->route('login')->withErrors(['error' => 'Le lien de complétion est invalide ou a déjà été utilisé. Veuillez vous connecter.']);
        }

        Log::info('showUploadPhotoForm called', ['token' => $token, 'teletravailleur' => $teletravailleur]);

        if ($teletravailleur->CIN && $teletravailleur->telephone && $teletravailleur->adresse) {
            return view('teletravailleur.upload_photo', compact('token'));
        }

        return redirect()->route('teletravailleur.complete.info', ['token' => $token])->withErrors(['error' => 'Veuillez compléter vos informations d\'abord.']);
    }

    public function uploadPhoto(Request $request, $token)
    {
        $teletravailleur = Teletravailleur::where('token', $token)->first();

        if (!$teletravailleur) {
            \Log::error('Token invalide dans uploadPhoto', ['token' => $token]);
            return redirect()->route('login')->withErrors(['error' => 'Le lien de complétion est invalide ou a déjà été utilisé.']);
        }


    if ($request->has('avatar')) {
        $avatarName = $request->input('avatar');
        $path = 'images/' . $avatarName;

        if (file_exists(public_path($path))) {
            if ($teletravailleur->photoProfil) {
                Storage::disk('public')->delete($teletravailleur->photoProfil);
            }

            $teletravailleur->update(['photoProfil' => $path]);

            \Log::info('Avatar sélectionné avec succès', ['path' => $path, 'teletravailleur_id' => $teletravailleur->id]);

            $teletravailleur->update(['token' => null]);

            if ($teletravailleur->utilisateur) {
                $teletravailleur->utilisateur->update(['statut' => true]);
                \Log::info('Utilisateur statut updated', ['user_id' => $teletravailleur->utilisateur->id]);
            } else {
                \Log::error('Utilisateur associé non trouvé', ['teletravailleur_id' => $teletravailleur->id]);
                return redirect()->route('login')->withErrors(['error' => 'Utilisateur associé non trouvé.']);
            }

            return redirect()->to('/login')
                ->with('success', 'Avatar sélectionné avec succès ! Veuillez vous connecter.')
                ->with('completed', true);
        } else {
            \Log::error('Avatar non trouvé', ['avatar' => $avatarName, 'teletravailleur_id' => $teletravailleur->id]);
            return redirect()->back()->withErrors(['avatar' => 'L\'avatar sélectionné n\'existe pas.']);
        }
    }




        $validatedData = $request->validate([
            'photoProfil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            if ($request->hasFile('photoProfil')) {
                if ($teletravailleur->photoProfil) {
                    Storage::disk('public')->delete($teletravailleur->photoProfil);
                }

                $path = $request->file('photoProfil')->store('photos', 'public');
                $teletravailleur->update(['photoProfil' => $path]);

                \Log::info('Photo uploaded successfully', ['path' => $path, 'teletravailleur_id' => $teletravailleur->id]);

                $teletravailleur->update(['token' => null]);

                if ($teletravailleur->utilisateur) {
                    $teletravailleur->utilisateur->update(['statut' => true]);
                    \Log::info('Utilisateur statut updated', ['user_id' => $teletravailleur->utilisateur->id]);
                } else {
                    \Log::error('Utilisateur associé non trouvé', ['teletravailleur_id' => $teletravailleur->id]);
                    return redirect()->route('login')->withErrors(['error' => 'Utilisateur associé non trouvé.']);
                }


                \Log::info('Redirection forcée vers /login', ['token' => $token, 'admin_session' => Auth::guard('admin')->check(), 'teletravailleur_session' => Auth::guard('teletravailleur')->check()]);
                return redirect()->to('/login')
                    ->with('success', 'Profil complété avec succès ! Veuillez vous connecter.')
                    ->with('completed', true);
            } else {
                \Log::error('Aucun fichier photo fourni', ['teletravailleur_id' => $teletravailleur->id]);
                return redirect()->back()->withErrors(['photoProfil' => 'Veuillez uploader une photo valide.']);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'upload', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['photoProfil' => 'Erreur lors de l\'upload.']);
        }
    }

    public function showChat()
    {
        $teletravailleur = Auth::user()->teletravailleur;

        if (!$teletravailleur) {
            abort(404, 'Télétravailleur non trouvé.');
        }

        $chatbot = $teletravailleur->chatbot()->first();

        if (!$chatbot) {
            $chatbot = Chatbot::create([
                'teletravailleur_id' => $teletravailleur->id,
                'session' => 'default',
                'historique' => json_encode([]),
            ]);
        }

        return view('teletravailleur.chat', compact('teletravailleur', 'chatbot'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $teletravailleur = Auth::user()->teletravailleur;

        if (!$teletravailleur) {
            return redirect()->route('login')->with('error', 'Télétravailleur non trouvé.');
        }

        $chatbot = $teletravailleur->chatbot()->firstOrCreate(
            ['teletravailleur_id' => $teletravailleur->id],
            ['session' => 'default', 'historique' => json_encode([])]
        );

        try {
            $historique = json_decode($chatbot->historique, true) ?: [];
            $historique[] = [
                'message' => $request->message,
                'is_from_teletravailleur' => true,
                'date' => now()->format('d/m/Y H:i'),
            ];

            $response = $this->getAutomatedResponse($request->message);
            if ($response) {
                $historique[] = [
                    'message' => $response,
                    'is_from_teletravailleur' => false,
                    'date' => now()->format('d/m/Y H:i'),
                ];
            }

            $chatbot->historique = json_encode(array_slice($historique, -50));
            $chatbot->save();

            return redirect()->route('teletravailleur.chat.index')->with('success', 'Message envoyé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement du chatbot pour le télétravailleur ' . $teletravailleur->id . ' : ' . $e->getMessage());
            return redirect()->route('teletravailleur.chat.index')->with('error', 'Erreur lors de l\'enregistrement du message. Veuillez réessayer.');
        }
    }

    private function getAutomatedResponse(string $message): ?string
    {
        $message = strtolower(trim($message));

        if (strpos($message, 'profil') !== false) {
            return "Pour compléter ou mettre à jour votre profil, rendez-vous sur : " . route('profile.edit', Auth::user()->teletravailleur->id);
        } elseif (strpos($message, 'heures') !== false) {
            return "Accédez à votre tableau de bord pour consulter ou signaler vos heures de travail.";
        } elseif (strpos($message, 'problème') !== false) {
            return "Nous avons reçu votre signalement. L’administrateur sera informé sous peu.";
        }

        return "Désolé, je n'ai pas compris votre demande.";
    }

    public function details($id)
    {
        $teletravailleur = Utilisateur::with('teletravailleur')->findOrFail($id);
        $teletravailleurId = $teletravailleur->teletravailleur->id;

        $screenshots = Screenshot::where('teletravailleur_id', $teletravailleurId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $workingHours = WorkingHour::where('teletravailleur_id', $teletravailleurId)
            ->orderBy('date', 'desc')
            ->paginate(10);
        $todayHours = WorkingHour::where('teletravailleur_id', $teletravailleurId)
            ->whereDate('date', now()->toDateString())
            ->sum('total_seconds') / 3600;
        $monthlyHours = WorkingHour::where('teletravailleur_id', $teletravailleurId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('total_seconds') / 3600;

        return view('admin.teletravailleurs.details', compact('teletravailleur', 'screenshots', 'workingHours', 'todayHours', 'monthlyHours'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\WorkingHour;
use App\Models\Teletravailleur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\WorkHoursMail;
use Illuminate\Support\Facades\Mail;

class WorkingHourController extends Controller
{


    public function start(Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|string|in:start',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation échouée lors du démarrage.', [
                'error' => $e->errors(),
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'Requête invalide : action manquante ou incorrecte.'], 400);
        }

        $teletravailleur = Auth::user()->teletravailleur;
        if (!$teletravailleur) {
            \Log::error('Télétravailleur non trouvé lors du démarrage.', ['user_id' => Auth::id()]);
            return response()->json(['error' => 'Télétravailleur non trouvé.'], 404);
        }

        $existingSession = WorkingHour::where('teletravailleur_id', $teletravailleur->id)
            ->where('date', now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('stop_time')
                      ->orWhere('stop_time', '>', now()->subMinutes(1));
            })
            ->first();

        if ($existingSession) {
            try {
                if (is_null($existingSession->stop_time) && $existingSession->start_time) {
                    $existingSession->stop_time = now();
                    $existingSession->total_seconds = now()->diffInSeconds($existingSession->start_time);
                    $existingSession->save();
                    \Log::info('Session active arrêtée avant de démarrer une nouvelle.', [
                        'teletravailleur_id' => $teletravailleur->id,
                        'working_hour_id' => $existingSession->id,
                    ]);
                } else {
                    \Log::warning('Session récente détectée mais pas arrêtée manuellement.', [
                        'teletravailleur_id' => $teletravailleur->id,
                        'working_hour_id' => $existingSession->id,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l’arrêt de la session active.', [
                    'teletravailleur_id' => $teletravailleur->id,
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['error' => 'Erreur lors de la gestion de la session active.'], 500);
            }
        }

        try {
            $workingHour = new WorkingHour();
            $workingHour->teletravailleur_id = $teletravailleur->id;
            $workingHour->date = now()->toDateString();
            $workingHour->start_time = now();
            $workingHour->total_seconds = 0;
            $workingHour->pause_total_seconds = 0;
            $workingHour->save();

            \Log::info('Début du travail réussi.', [
                'teletravailleur_id' => $teletravailleur->id,
                'working_hour_id' => $workingHour->id,
            ]);
            return response()->json(['message' => 'Travail démarré.'], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du démarrage du travail.', [
                'teletravailleur_id' => $teletravailleur->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Erreur serveur lors du démarrage.'], 500);
        }
    }



    public function pause(Request $request)
    {
        $teletravailleur = Auth::user()->teletravailleur;
        if (!$teletravailleur) {
            \Log::error('Télétravailleur non trouvé lors de la pause.', ['user_id' => Auth::id()]);
            return response()->json(['error' => 'Télétravailleur non trouvé.'], 404);
        }

        $currentDate = now()->toDateString();
        \Log::info('Recherche de session active pour pause.', [
            'teletravailleur_id' => $teletravailleur->id,
            'date' => $currentDate
        ]);

        $workingHour = WorkingHour::where('teletravailleur_id', $teletravailleur->id)
            ->whereDate('date', $currentDate)
            ->whereNull('stop_time')
            ->orderBy('id', 'desc')
            ->first();

        if (!$workingHour) {
            $allSessions = WorkingHour::where('teletravailleur_id', $teletravailleur->id)->get()->toArray();
            \Log::error('Aucune session active trouvée pour la pause.', [
                'teletravailleur_id' => $teletravailleur->id,
                'date' => $currentDate,
                'all_sessions' => $allSessions
            ]);
            return response()->json(['error' => 'Aucune session active.'], 400);
        }

        if ($workingHour->pause_time && !$workingHour->resume_time) {
            \Log::warning('La session est déjà en pause.', [
                'teletravailleur_id' => $teletravailleur->id,
                'working_hour_id' => $workingHour->id,
                'pause_time' => $workingHour->pause_time,
            ]);
            return response()->json(['error' => 'La session est déjà en pause.'], 400);
        }

        $workingHour->resume_time = null;
        $workingHour->pause_time = now();
        $workingHour->save();

        \Log::info('Session mise en pause avec succès.', [
            'teletravailleur_id' => $teletravailleur->id,
            'working_hour_id' => $workingHour->id,
            'pause_time' => $workingHour->pause_time,
        ]);

        return response()->json(['message' => 'Travail mis en pause.', 'pause_time' => $workingHour->pause_time]);
    }



    public function resume(Request $request)
    {
        try {
            $teletravailleur = Auth::user()->teletravailleur;
            if (!$teletravailleur) {
                \Log::error('Télétravailleur non trouvé lors de la reprise.', [
                    'user_id' => Auth::id(),
                    'user' => Auth::user() ? Auth::user()->toArray() : null,
                ]);
                return response()->json(['error' => 'Télétravailleur non trouvé.'], 404);
            }

            $currentDate = now()->toDateString();
            \Log::info('Recherche de session active pour reprise.', [
                'teletravailleur_id' => $teletravailleur->id,
                'date' => $currentDate,
                'user_email' => Auth::user()->email,
            ]);

            $workingHour = WorkingHour::where('teletravailleur_id', $teletravailleur->id)
                ->whereDate('date', $currentDate)
                ->whereNull('stop_time')
                ->whereNotNull('pause_time')
                ->whereNull('resume_time')
                ->orderBy('id', 'desc')
                ->first();

            \Log::info('Résultat de la requête pour la session.', [
                'working_hour_found' => $workingHour ? true : false,
                'working_hour_id' => $workingHour ? $workingHour->id : null,
                'working_hour' => $workingHour ? $workingHour->toArray() : null,
            ]);

            if (!$workingHour) {
                $allSessions = WorkingHour::where('teletravailleur_id', $teletravailleur->id)->get()->toArray();
                \Log::error('Aucune session en pause trouvée pour la reprise.', [
                    'teletravailleur_id' => $teletravailleur->id,
                    'date' => $currentDate,
                    'all_sessions' => $allSessions,
                ]);
                return response()->json(['error' => 'Aucune session en pause.'], 400);
            }

            $workingHour->resume_time = now();
            if ($workingHour->pause_time) {
                $lastTime = $workingHour->pause_time;
                $timePaused = $workingHour->resume_time->diffInSeconds($lastTime);
                $workingHour->pause_total_seconds += $timePaused;
                \Log::info('Durée de la pause calculée.', [
                    'pause_time' => $workingHour->pause_time,
                    'resume_time' => $workingHour->resume_time,
                    'time_paused' => $timePaused,
                    'pause_total_seconds' => $workingHour->pause_total_seconds,
                ]);
            }

            $workingHour->save();
            \Log::info('Session sauvegardée avec succès.', ['working_hour_id' => $workingHour->id]);

            \Log::info('Session reprise avec succès.', [
                'teletravailleur_id' => $teletravailleur->id,
                'working_hour_id' => $workingHour->id,
                'resume_time' => $workingHour->resume_time,
                'pause_total_seconds' => $workingHour->pause_total_seconds,
            ]);

            return response()->json(['message' => 'Travail repris.', 'resume_time' => $workingHour->resume_time]);
        } catch (\Exception $e) {
            \Log::error('Erreur inattendue dans resume.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Erreur serveur : ' . $e->getMessage()], 500);
        }
    }



   public function stop(Request $request)
{
    $teletravailleur = Auth::user()->teletravailleur;
    if (!$teletravailleur) {
        \Log::error('Télétravailleur non trouvé lors de l\'arrêt.', ['user_id' => Auth::id()]);
        return response()->json(['error' => 'Télétravailleur non trouvé.'], 404);
    }

    // Optimisation : Utiliser une seule requête avec select pour réduire la charge
    $workingHour = WorkingHour::where('teletravailleur_id', $teletravailleur->id)
        ->where('date', now()->toDateString())
        ->whereNull('stop_time')
        ->select('id', 'teletravailleur_id', 'date', 'start_time', 'pause_time', 'resume_time', 'stop_time', 'total_seconds', 'pause_total_seconds')
        ->first();

    if (!$workingHour) {
        \Log::error('Aucune session active trouvée pour l\'arrêt.', ['teletravailleur_id' => $teletravailleur->id]);
        return response()->json(['error' => 'Aucune session active.'], 400);
    }

    // Vérifier et mettre à jour pause_total_seconds si une pause est en cours
    if ($workingHour->pause_time && !$workingHour->resume_time) {
        $timePaused = now()->diffInSeconds($workingHour->pause_time);
        $workingHour->pause_total_seconds += $timePaused;
        \Log::info('Pause en cours finalisée.', ['working_hour_id' => $workingHour->id, 'time_paused' => $timePaused]);
    }

    // Définir stop_time et calculer total_seconds
    $workingHour->stop_time = now();
    $effectiveSeconds = $workingHour->calculateTotalSeconds();
    $workingHour->total_seconds = max(0, $effectiveSeconds);
    $workingHour->pause_total_seconds = min($workingHour->pause_total_seconds ?? 0, $effectiveSeconds);
    $workingHour->save();

    \Log::info('Session arrêtée.', ['working_hour_id' => $workingHour->id, 'total_seconds' => $workingHour->total_seconds]);

    $this->checkDailyHours($teletravailleur->id, $workingHour->date);

    return response()->json(['message' => 'Session arrêtée.']);
}

    protected function checkDailyHours($teletravailleurId, $date)
    {
        \Log::info('Début de la vérification des heures quotidiennes.', [
            'teletravailleur_id' => $teletravailleurId,
            'date' => $date,
        ]);

        try {
            $totalSeconds = WorkingHour::where('teletravailleur_id', $teletravailleurId)
                ->where('date', $date)
                ->sum('total_seconds');

            $totalHours = $totalSeconds / 3600;
            \Log::info('Total des heures calculé.', [
                'total_seconds' => $totalSeconds,
                'total_hours' => $totalHours,
                'teletravailleur_id' => $teletravailleurId,
            ]);

            $teletravailleur = Teletravailleur::where('id', $teletravailleurId)->first();
            if (!$teletravailleur) {
                \Log::error('Télétravailleur non trouvé dans checkDailyHours.', [
                    'teletravailleur_id' => $teletravailleurId,
                ]);
                return;
            }

            $teletravailleurUser = $teletravailleur->utilisateur;
            if (!$teletravailleurUser) {
                \Log::error('Utilisateur non trouvé pour le télétravailleur.', [
                    'teletravailleur_id' => $teletravailleurId,
                ]);
                return;
            }

            $admin = Utilisateur::whereHas('administrateur')->first();
            \Log::info('Admin recherché.', [
                'admin_found' => $admin ? true : false,
                'admin_id' => $admin ? $admin->id : null,
            ]);

            if ($totalHours < 4) {
                $teleMessage = "You have not met the minimum of 4 working hours today ($date). Please make sure to work more!";
                $adminMessage = "Remote worker {$teletravailleurUser->name} did not meet the minimum requirement of 4 working hours today ($date).";

                \Log::info('Envoi d\'email au télétravailleur pour moins de 4 heures.', [
                    'teletravailleur_id' => $teletravailleur->id,
                    'teletravailleur_email' => $teletravailleurUser->email,
                ]);
                Mail::to($teletravailleurUser->email)->send(new WorkHoursMail($teleMessage));

                if ($admin) {
                    \Log::info('Envoi d\'email à l\'admin pour moins de 4 heures.', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                    ]);
                    Mail::to($admin->email)->send(new WorkHoursMail($adminMessage));
                }
            }

            if ($totalHours == 4) {
                $teleMessage = "You have worked for more than $totalHours hours today ($date). Take a break, grab a snack, and get back to work!";
                \Log::info('Envoi d\'email au télétravailleur pour 4 heures atteintes.', [
                    'teletravailleur_id' => $teletravailleur->id,
                    'teletravailleur_email' => $teletravailleurUser->email,
                ]);
                Mail::to($teletravailleurUser->email)->send(new WorkHoursMail($teleMessage));
            }

            if ($totalHours >= 6 && $totalHours <= 8) {
                $teleMessage = "Great job today, thank you so much!";
                $adminMessage = "Remote worker {$teletravailleurUser->name} has worked between 6 and 8 hours today ($date). Well done!";

                \Log::info('Envoi d\'email au télétravailleur pour 6 à 8 heures.', [
                    'teletravailleur_id' => $teletravailleur->id,
                    'teletravailleur_email' => $teletravailleurUser->email,
                ]);
                Mail::to($teletravailleurUser->email)->send(new WorkHoursMail($teleMessage));

                if ($admin) {
                    \Log::info('Envoi d\'email à l\'admin pour 6 à 8 heures.', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                    ]);
                    Mail::to($admin->email)->send(new WorkHoursMail($adminMessage));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erreur dans checkDailyHours.', [
                'teletravailleur_id' => $teletravailleurId,
                'date' => $date,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function testCheckDailyHours($teletravailleurId, $date)
    {
        return $this->checkDailyHours($teletravailleurId, $date);
    }
}

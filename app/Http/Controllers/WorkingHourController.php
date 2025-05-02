<?php

namespace App\Http\Controllers;

use App\Models\WorkingHour;
use App\Models\Teletravailleur;
use App\Models\Utilisateur;
use App\Notifications\WorkHoursNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkingHourController extends Controller
{
    /**
     * Démarrer une session de travail pour le télétravailleur connecté.
     */
    public function start(Request $request)
    {
        // Valider la requête
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

        // Vérifier si une session active existe déjà aujourd'hui
        $existingSession = WorkingHour::where('teletravailleur_id', $teletravailleur->id)
            ->where('date', now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('stop_time') // Session en cours
                      ->orWhere('stop_time', '>', now()->subMinutes(1)); // Session qui vient juste de se terminer
            })
            ->first();

        if ($existingSession) {
            try {
                // Forcer l'arrêt de la session active
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

        // Créer une nouvelle session
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

    /**
     * Mettre en pause une session de travail.
     */
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

        // Vérifier si la session est déjà en pause
        if ($workingHour->pause_time && !$workingHour->resume_time) {
            \Log::warning('La session est déjà en pause.', [
                'teletravailleur_id' => $teletravailleur->id,
                'working_hour_id' => $workingHour->id,
                'pause_time' => $workingHour->pause_time,
            ]);
            return response()->json(['error' => 'La session est déjà en pause.'], 400);
        }

        // Calculer le temps travaillé depuis le dernier point (start_time ou resume_time)
        $workingHour->pause_time = now();
        if ($workingHour->start_time) {
            $lastTime = $workingHour->resume_time ?? $workingHour->start_time;
            $timeWorked = $workingHour->pause_time->diffInSeconds($lastTime);
            $workingHour->total_seconds += $timeWorked;
        }

        // Réinitialiser resume_time pour permettre une nouvelle reprise
        $workingHour->resume_time = null;

        $workingHour->save();

        \Log::info('Session mise en pause avec succès.', [
            'teletravailleur_id' => $teletravailleur->id,
            'working_hour_id' => $workingHour->id,
            'pause_time' => $workingHour->pause_time,
            'total_seconds' => $workingHour->total_seconds,
        ]);

        return response()->json(['message' => 'Travail mis en pause.', 'pause_time' => $workingHour->pause_time]);
    }

    /**
     * Reprendre une session de travail après une pause.
     */
    public function resume(Request $request)
    {
        $teletravailleur = Auth::user()->teletravailleur;
        if (!$teletravailleur) {
            \Log::error('Télétravailleur non trouvé lors de la reprise.', ['user_id' => Auth::id()]);
            return response()->json(['error' => 'Télétravailleur non trouvé.'], 404);
        }

        $currentDate = now()->toDateString();
        \Log::info('Recherche de session active pour reprise.', [
            'teletravailleur_id' => $teletravailleur->id,
            'date' => $currentDate
        ]);

        $workingHour = WorkingHour::where('teletravailleur_id', $teletravailleur->id)
            ->whereDate('date', $currentDate)
            ->whereNull('stop_time')
            ->whereNotNull('pause_time')
            ->whereNull('resume_time')
            ->orderBy('id', 'desc')
            ->first();

        if (!$workingHour) {
            $allSessions = WorkingHour::where('teletravailleur_id', $teletravailleur->id)->get()->toArray();
            \Log::error('Aucune session en pause trouvée pour la reprise.', [
                'teletravailleur_id' => $teletravailleur->id,
                'date' => $currentDate,
                'all_sessions' => $allSessions
            ]);
            return response()->json(['error' => 'Aucune session en pause.'], 400);
        }

        // Calculer la durée de la pause
        $workingHour->resume_time = now();
        if ($workingHour->pause_time) {
            $lastTime = $workingHour->pause_time;
            $timePaused = $workingHour->resume_time->diffInSeconds($lastTime);
            $workingHour->pause_total_seconds += $timePaused;
        }
        $workingHour->save();

        // Vérifier si plus de 30 secondes consécutives
        $this->checkConsecutiveHours($workingHour);

        \Log::info('Session reprise avec succès.', [
            'teletravailleur_id' => $teletravailleur->id,
            'working_hour_id' => $workingHour->id,
            'resume_time' => $workingHour->resume_time,
            'pause_total_seconds' => $workingHour->pause_total_seconds,
        ]);

        return response()->json(['message' => 'Travail repris.', 'resume_time' => $workingHour->resume_time]);
    }

    /**
     * Arrêter une session de travail.
     */
    public function stop()
    {
        $teletravailleur = Auth::user()->teletravailleur;
        if (!$teletravailleur) {
            \Log::error('Télétravailleur non trouvé lors de l\'arrêt.', ['user_id' => Auth::id()]);
            return response()->json(['error' => 'Télétravailleur non trouvé.'], 404);
        }

        $workingHour = WorkingHour::where('teletravailleur_id', $teletravailleur->id)
            ->where('date', now()->toDateString())
            ->whereNull('stop_time')
            ->first();

        if (!$workingHour) {
            \Log::error('Aucune session active trouvée pour l\'arrêt.', [
                'teletravailleur_id' => $teletravailleur->id,
                'date' => now()->toDateString(),
            ]);
            return response()->json(['error' => 'Aucune session active.'], 400);
        }

        $workingHour->stop_time = now();
        // Recalculer total_seconds correctement
        $workingHour->total_seconds = $workingHour->start_time->diffInSeconds($workingHour->stop_time);
        // S'assurer que pause_total_seconds ne dépasse pas total_seconds
        $workingHour->pause_total_seconds = min($workingHour->pause_total_seconds ?? 0, $workingHour->total_seconds);
        $workingHour->save();

        \Log::info('Session arrêtée avec succès.', [
            'teletravailleur_id' => $teletravailleur->id,
            'working_hour_id' => $workingHour->id,
            'total_seconds' => $workingHour->total_seconds,
            'pause_total_seconds' => $workingHour->pause_total_seconds,
        ]);

        // Vérifier les secondes totales de la journée
        $this->checkDailyHours($teletravailleur->id, $workingHour->date);

        return response()->json(['message' => 'Session arrêtée.']);
    }

    /**
     * Vérifier si plus de 30 secondes consécutives ont été travaillées.
     */
    protected function checkConsecutiveHours($workingHour)
    {
        \Log::info('Vérification des heures consécutives.', [
            'working_hour_id' => $workingHour->id,
        ]);

        $teletravailleur = Teletravailleur::where('id', $workingHour->teletravailleur_id)->first();
        if (!$teletravailleur) {
            \Log::error('Télétravailleur non trouvé dans checkConsecutiveHours.', [
                'teletravailleur_id' => $workingHour->teletravailleur_id,
            ]);
            return;
        }

        $teletravailleurUser = $teletravailleur->utilisateur;
        if (!$teletravailleurUser) {
            \Log::error('Utilisateur non trouvé pour le télétravailleur dans checkConsecutiveHours.', [
                'teletravailleur_id' => $teletravailleur->id,
            ]);
            return;
        }

        $totalSeconds = $workingHour->total_seconds - $workingHour->pause_total_seconds;
        $consecutiveSeconds = $totalSeconds;

        \Log::info('Calcul des secondes consécutives.', [
            'total_seconds' => $workingHour->total_seconds,
            'pause_total_seconds' => $workingHour->pause_total_seconds,
            'consecutive_seconds' => $consecutiveSeconds,
        ]);

        // Vérifier si plus de 30 secondes consécutives
        if ($consecutiveSeconds >= 30) {
            $message = "Vous avez travaillé plus de 30 secondes consécutives. Faites une pause, mangez quelque chose, et retournez au travail !";
            \Log::info('Envoi d\'email au télétravailleur pour plus de 30 secondes consécutives.', [
                'teletravailleur_id' => $teletravailleur->id,
                'teletravailleur_email' => $teletravailleurUser->email,
            ]);
            $teletravailleurUser->notify(new WorkHoursNotification($message));
        }
    }

    /**
     * Vérifier les heures totales de la journée et envoyer des notifications si nécessaire.
     */
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

            $totalHours = $totalSeconds / 3600; // Convertir en heures
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

            // Trouver l'admin
            $admin = Utilisateur::whereHas('administrateur')->first();
            \Log::info('Admin recherché.', [
                'admin_found' => $admin ? true : false,
                'admin_id' => $admin ? $admin->id : null,
            ]);

            // Moins de 4 heures - Manque de travail (Télétravailleur et Admin)
            if ($totalHours < 4) {
                $teleMessage = "Vous n'avez pas atteint le minimum de 4 heures aujourd'hui ($date). Travaillez davantage !";
                $adminMessage = "Le télétravailleur {$teletravailleurUser->name} n'a pas atteint le minimum de 4 heures aujourd'hui ($date).";

                // Email au télétravailleur
                \Log::info('Envoi d\'email au télétravailleur pour moins de 4 heures.', [
                    'teletravailleur_id' => $teletravailleur->id,
                    'teletravailleur_email' => $teletravailleurUser->email,
                ]);
                $teletravailleurUser->notify(new WorkHoursNotification($teleMessage));

                // Email à l'admin
                if ($admin) {
                    \Log::info('Envoi d\'email à l\'admin pour moins de 4 heures.', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                    ]);
                    $admin->notify(new WorkHoursNotification($adminMessage));
                }
            }

            // Atteint 4 heures - Demander de se reposer (Télétravailleur uniquement)
            if ($totalHours == 4) {
                $teleMessage = "Vous avez travaillé $totalHours heures aujourd'hui ($date). Veuillez vous reposer !";
                \Log::info('Envoi d\'email au télétravailleur pour 4 heures atteintes.', [
                    'teletravailleur_id' => $teletravailleur->id,
                    'teletravailleur_email' => $teletravailleurUser->email,
                ]);
                $teletravailleurUser->notify(new WorkHoursNotification($teleMessage));
            }

            // Entre 6 et 8 heures - Bien travaillé (Télétravailleur et Admin)
            if ($totalHours >= 6 && $totalHours <= 8) {
                $teleMessage = "Vous avez travaillé bien aujourd'hui, merci beaucoup !";
                $adminMessage = "Le télétravailleur {$teletravailleurUser->name} a travaillé entre 6 et 8 heures aujourd'hui ($date). Félicitations !";

                // Email au télétravailleur
                \Log::info('Envoi d\'email au télétravailleur pour 6 à 8 heures.', [
                    'teletravailleur_id' => $teletravailleur->id,
                    'teletravailleur_email' => $teletravailleurUser->email,
                ]);
                $teletravailleurUser->notify(new WorkHoursNotification($teleMessage));

                // Email à l'admin
                if ($admin) {
                    \Log::info('Envoi d\'email à l\'admin pour 6 à 8 heures.', [
                        'admin_id' => $admin->id,
                        'admin_email' => $admin->email,
                    ]);
                    $admin->notify(new WorkHoursNotification($adminMessage));
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
}

<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Utilisateur;

class UpdateUserStatus
{

    public function handleLogin(Login $event)
    {
        $user = $event->user;
        if ($user && $user instanceof Utilisateur) {
            try {
                $user->statut = true;
                $user->save();
            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour du statut (Login) pour l\'utilisateur ' . $user->id . ' : ' . $e->getMessage());
            }
        }
    }


    public function handleLogout(Logout $event)
    {
        $user = $event->user;
        if ($user && $user instanceof Utilisateur) {
            try {
                $user->statut = false;
                $user->save();

                Cache::forget('user-is-online-' . $user->id);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour du statut (Logout) pour l\'utilisateur ' . $user->id . ' : ' . $e->getMessage());
            }
        }
    }


    public function updateAllUserStatuses()
    {
        try {
            Utilisateur::all()->each(function ($user) {
                $user->statut = $user->isOnline();
                $user->save();
            });
            Log::info('Statuts des utilisateurs mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des statuts de tous les utilisateurs : ' . $e->getMessage());
        }
    }
}

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
    /**
     * Lorsqu'un utilisateur se connecte, on met à jour son statut et son activité.
     */
    public function handleLogin(Login $event)
    {
        $user = $event->user;
        if ($user && $user instanceof Utilisateur) {
            try {
                $user->statut = true; // Actif
                $user->save();
            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour du statut (Login) pour l\'utilisateur ' . $user->id . ' : ' . $e->getMessage());
            }
        }
    }

    /**
     * Lorsqu'un utilisateur se déconnecte, on met à jour son statut.
     */
    public function handleLogout(Logout $event)
    {
        $user = $event->user;
        if ($user && $user instanceof Utilisateur) {
            try {
                $user->statut = false; // Inactif
                $user->save();
                // Supprimer la clé de cache lors de la déconnexion
                Cache::forget('user-is-online-' . $user->id);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la mise à jour du statut (Logout) pour l\'utilisateur ' . $user->id . ' : ' . $e->getMessage());
            }
        }
    }

    /**
     * Met à jour le statut de tous les utilisateurs en fonction de isOnline().
     */
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

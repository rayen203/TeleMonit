<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeletravailleurMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) { // Utilise le guard par défaut (web)
            Log::info('Utilisateur non authentifié', ['url' => $request->url()]);
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $user = Auth::user();
        if (!$user->teletravailleur instanceof \App\Models\Teletravailleur) {
            Log::info('Utilisateur non télétravailleur', ['user_id' => $user->id]);
            abort(403, "Accès réservé aux télétravailleurs.");
        }

        return $next($request);
    }
}

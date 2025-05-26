<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            Log::info('Utilisateur non authentifié', ['url' => $request->url()]);

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Non authentifié.'], 401);
            }

            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $user = Auth::user();
        if (!$user->administrateur) {
            Log::warning('Accès refusé : Utilisateur non admin', ['user_id' => $user->id]);

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Accès réservé aux administrateurs.'], 403);
            }

            abort(403, 'Accès réservé aux administrateurs');
        }

        return $next($request);
    }
}

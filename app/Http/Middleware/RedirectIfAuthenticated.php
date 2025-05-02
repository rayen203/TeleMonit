<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Si l'utilisateur est connecté, redirige selon son rôle
                $user = Auth::user();
                if ($user->administrateur instanceof \App\Models\Administrateur) {
                    return redirect('/admin/dashboard');
                } elseif ($user->teletravailleur instanceof \App\Models\Teletravailleur) {
                    return redirect('/teletravailleur/dashboard');
                }

                // Si aucun rôle spécifique, redirige vers une page par défaut (par exemple, /login)
                // Mais pour éviter une boucle, on peut simplement laisser passer la requête
                return $next($request);
            }
        }

        return $next($request);
    }
}

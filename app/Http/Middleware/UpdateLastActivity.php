<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Utilisateur; // Utiliser le bon modèle
use Illuminate\Support\Facades\Cache;


class UpdateLastActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->last_activity || $user->last_activity->lt(now()->subMinutes(5))) {
                $user->last_activity = now();
                $user->save();
                Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(10));
            }
        }
        return $next($request);
    }
}

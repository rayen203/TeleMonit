<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Screenshot;

class RestrictScreenshotAccess
{
    public function handle(Request $request, Closure $next)
    {
        $screenshotId = $request->route('id');
        $screenshot = Screenshot::findOrFail($screenshotId);
        $user = auth()->user();

        // Un admin a une relation administrateur (et pas de relation teletravailleur)
        $isAdmin = $user->administrateur !== null;
        // Vérifier si l'utilisateur est le télétravailleur associé
        $isOwner = $user->teletravailleur && $screenshot->teletravailleur_id === $user->teletravailleur->id;

        if (!$isAdmin && !$isOwner) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}

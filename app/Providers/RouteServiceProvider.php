<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/login';// Par défaut, redirige vers /login

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    public static function home()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->administrateur instanceof \App\Models\Administrateur) {
                return '/admin/dashboard';
            } elseif ($user->teletravailleur instanceof \App\Models\Teletravailleur) {
                return '/teletravailleur/dashboard';
            }
        }

        return '/login'; // Redirige vers /login si aucun rôle spécifique ou si l'utilisateur n'est pas connecté
    }
}

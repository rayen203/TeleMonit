<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Affiche le formulaire de réinitialisation du mot de passe.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Gère la soumission du nouveau mot de passe.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validation des champs
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        Log::info('Tentative de réinitialisation pour : ' . $request->email);

        // Réinitialisation du mot de passe
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                   'password' => $request->password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                Log::info('Mot de passe mis à jour pour : ' . $user->email);
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            Log::info('Réinitialisation réussie pour : ' . $request->email);
            return redirect()->route('login')->with('status', __('Votre mot de passe a été réinitialisé.'));
        } else {
            // Si l'état de réinitialisation échoue
            Log::error('Échec de la réinitialisation pour : ' . $request->email);
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UpdatePasswordController extends Controller
{
    /**
     * Show the update password form.
     */
    public function show(): View
    {
        return view('auth.update-password');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {

        $request->validate([
            'password' => ['required', 'current_password'],
            'new_password' => ['required', Password::defaults(), 'confirmed'],

        ]);

        // Mettre à jour le mot de passe
        $user = $request->user();
        $user->password = $request->new_password; // Le mutator hachera automatiquement
        $user->save();

        // Vérifier si l'utilisateur est un télétravailleur ou un administrateur
        if ($user->teletravailleur) {
            return redirect()->route('teletravailleur.dashboard')->with('status', 'Password updated successfully!');
        } else {
            return redirect()->route('admin.dashboard')->with('status', 'Password updated successfully!');
        }
    }
}

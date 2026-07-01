<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        try {
            $request->user()->notify(new \App\Notifications\SecurityNotification(
                'Mot de passe modifié',
                'Le mot de passe de votre compte vient d\'être changé.',
                'lock-closed-outline',
                true,
            ));
        } catch (\Throwable $e) {
            // silencieux
        }

        return back()->with('status', 'password-updated');
    }
}

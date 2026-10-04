<?php

namespace App\Http\Controllers;

use App\Rules\MalianPhone;
use App\Support\PhoneNumber;
use App\Support\WhatsAppVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneVerificationController extends Controller
{
    /** Envoie un code OTP sur le WhatsApp du numéro du profil (doit être enregistré d'abord). */
    public function send(Request $request)
    {
        $user = Auth::user();
        $phone = PhoneNumber::normalize($user->tel);

        if (!$phone) {
            return back()->with('error', 'Enregistrez d\'abord un numéro WhatsApp malien valide dans votre profil.');
        }

        try {
            WhatsAppVerification::send($phone);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Envoi OTP WhatsApp échoué : ' . $e->getMessage());

            return back()->with('error', 'Envoi du code WhatsApp impossible pour le moment. Réessayez plus tard.');
        }

        return back()->with('success', 'Code envoyé sur WhatsApp au ' . PhoneNumber::pretty($phone) . '.');
    }

    /** Vérifie le code saisi et marque le numéro comme vérifié. */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
            'tel' => ['nullable', 'string', new MalianPhone()],
        ]);

        $user = Auth::user();

        // Le client peut vérifier un nouveau numéro saisi (mis à jour si OK).
        $phone = PhoneNumber::normalize($request->input('tel', $user->tel));

        if (!$phone) {
            return back()->with('error', 'Numéro WhatsApp invalide.');
        }

        if (!WhatsAppVerification::check($phone, $request->input('code'))) {
            return back()->with('error', 'Code incorrect ou expiré. Demandez un nouveau code.');
        }

        $user->forceFill([
            'tel' => $phone,
            'tel_verified_at' => now(),
        ])->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Numéro ' . PhoneNumber::pretty($phone) . ' vérifié !');
    }
}

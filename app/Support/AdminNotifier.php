<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\AdminPaymentNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Envoie une notification (base + email) à tous les administrateurs.
 */
class AdminNotifier
{
    public static function notifyPayment(string $title, string $body, array $link = ['type' => 'none']): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new AdminPaymentNotification($title, $body, $link));
            }
        } catch (\Throwable $e) {
            Log::warning('Notification admin échouée : ' . $e->getMessage());
        }
    }
}

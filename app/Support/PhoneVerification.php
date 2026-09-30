<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Vérification du numéro par code OTP (6 chiffres, 10 minutes).
 * Envoi via SMS_DRIVER : "log" (dev : code visible dans les logs)
 * ou passerelle réelle à brancher ici (Orange/Moov API...).
 */
class PhoneVerification
{
    public static function send(string $phone): string
    {
        $code = (string) random_int(100000, 999999);

        Cache::put('phone_otp:' . $phone, $code, now()->addMinutes(10));
        Cache::put('phone_otp_tries:' . $phone, 0, now()->addMinutes(10));

        $driver = config('services.sms.driver', 'log');
        if ($driver === 'log') {
            Log::info("Code de vérification pour {$phone} : {$code}");
        } else {
            static::sendViaGateway($phone, $code);
        }

        return $code;
    }

    public static function check(string $phone, string $code): bool
    {
        $key = 'phone_otp:' . $phone;
        $expected = Cache::get($key);

        if (!$expected) {
            return false;
        }

        $tries = (int) Cache::get('phone_otp_tries:' . $phone, 0) + 1;
        Cache::put('phone_otp_tries:' . $phone, $tries, now()->addMinutes(10));

        if ($tries > 5 || !hash_equals((string) $expected, trim($code))) {
            if ($tries > 5) {
                Cache::forget($key);
            }

            return false;
        }

        Cache::forget($key);
        Cache::forget('phone_otp_tries:' . $phone);

        return true;
    }

    /**
     *Brancher ici la passerelle SMS réelle (API Orange, Moov...).
     */
    protected static function sendViaGateway(string $phone, string $code): void
    {
        Log::warning("SMS non configuré : code {$code} pour {$phone} (driver=" . config('services.sms.driver') . ')');
    }
}

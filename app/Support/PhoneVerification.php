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
     * Passerelle SMS réelle (API Orange : OAuth client_credentials + envoi).
     * Config : SMS_ORANGE_CLIENT_ID / SECRET / SENDER (tel:+223...) / BASE_URL.
     */
    protected static function sendViaGateway(string $phone, string $code): void
    {
        $driver = config('services.sms.driver');

        if ($driver === 'orange') {
            static::sendViaOrange($phone, $code);
            return;
        }

        Log::warning("SMS non configuré : code {$code} pour {$phone} (driver={$driver})");
    }

    protected static function sendViaOrange(string $phone, string $code): void
    {
        $base = rtrim((string) config('services.sms.orange.base_url', 'https://api.orange.com'), '/');
        $clientId = (string) config('services.sms.orange.client_id');
        $clientSecret = (string) config('services.sms.orange.client_secret');
        $sender = (string) config('services.sms.orange.sender');

        if ($clientId === '' || $clientSecret === '' || $sender === '') {
            throw new \RuntimeException('Passerelle Orange non configurée (SMS_ORANGE_CLIENT_ID/SECRET/SENDER).');
        }

        $message = "Boutique : votre code de vérification est {$code}. Il expire dans 10 minutes.";

        $token = \Illuminate\Support\Facades\Cache::remember('orange_sms_token', 3500, function () use ($base, $clientId, $clientSecret) {
            $res = \Illuminate\Support\Facades\Http::asForm()->post($base . '/oauth/v3/oauth', [
                'grant_type' => 'client_credentials',
            ])->withBasicAuth($clientId, $clientSecret);

            if (!$res->successful()) {
                throw new \RuntimeException('Orange OAuth refusé (HTTP ' . $res->status() . ').');
            }

            return $res->json('access_token');
        });

        if (!$token) {
            throw new \RuntimeException('Jeton Orange introuvable.');
        }

        $res = \Illuminate\Support\Facades\Http::withToken($token)
            ->post($base . '/smsmessaging/v1/outbound/tel:' . urlencode($sender) . '/requests', [
                'outboundSMSMessageRequest' => [
                    'address' => 'tel:+' . $phone,
                    'senderAddress' => 'tel:' . ltrim($sender, '+'),
                    'outboundSMSTextMessage' => ['message' => $message],
                ],
            ]);

        if (!$res->successful()) {
            \Illuminate\Support\Facades\Cache::forget('orange_sms_token');
            throw new \RuntimeException('Envoi SMS refusé (HTTP ' . $res->status() . ').');
        }

        Log::info("SMS Orange envoyé au +{$phone}.");
    }
}

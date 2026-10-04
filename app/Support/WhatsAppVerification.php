<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Vérification du numéro WhatsApp par code OTP (6 chiffres, 10 minutes).
 *
 * Envoi via WHATSAPP_OTP_DRIVER :
 * - "log"     (défaut/dev) : code visible dans les logs, aucun coût.
 * - "gateway" (recommandé) : passerelle HTTP branchée sur TON numéro WhatsApp
 *   (ex : Ultramsg, Green-API — numéro connecté via QR code). Le site génère
 *   le code et la passerelle l'envoie depuis ton numéro.
 * - "meta"    (alternative officielle) : Meta WhatsApp Cloud API, template
 *   utilitaire contenant {{1}} = le code. Accès API gratuit, chaque OTP livré
 *   est facturé par Meta au tarif template du pays (quelques FCFA).
 *
 * Les numéros du site sont des numéros WhatsApp (Mali : 223XXXXXXXX).
 */
class WhatsAppVerification
{
    public static function send(string $phone): string
    {
        $code = (string) random_int(100000, 999999);

        Cache::put('wa_otp:' . $phone, $code, now()->addMinutes(10));
        Cache::put('wa_otp_tries:' . $phone, 0, now()->addMinutes(10));

        $driver = (string) config('services.whatsapp.otp_driver', 'log');
        if ($driver === 'meta') {
            static::sendViaMeta($phone, $code);
        } elseif ($driver === 'gateway') {
            static::sendViaGateway($phone, $code);
        } else {
            Log::info("Code WhatsApp pour {$phone} : {$code}");
        }

        return $code;
    }

    public static function check(string $phone, ?string $code): bool
    {
        $key = 'wa_otp:' . $phone;
        $expected = Cache::get($key);

        if (!$expected || $code === null || $code === '') {
            return false;
        }

        $tries = (int) Cache::get('wa_otp_tries:' . $phone, 0) + 1;
        Cache::put('wa_otp_tries:' . $phone, $tries, now()->addMinutes(10));

        if ($tries > 5 || !hash_equals((string) $expected, trim((string) $code))) {
            if ($tries > 5) {
                Cache::forget($key);
            }

            return false;
        }

        Cache::forget($key);
        Cache::forget('wa_otp_tries:' . $phone);

        return true;
    }

    /**
     * Demande vendeur en attente (step 1 validée, step 2 = code WhatsApp).
     * Le mot de passe y est stocké DÉJÀ HACHÉ, jamais en clair.
     */
    public static function stashPending(string $phone, array $data): void
    {
        Cache::put('vendeur_pending:' . $phone, $data, now()->addMinutes(15));
    }

    public static function peekPending(string $phone): ?array
    {
        $pending = Cache::get('vendeur_pending:' . $phone);

        return is_array($pending) ? $pending : null;
    }

    public static function clearPending(string $phone): void
    {
        Cache::forget('vendeur_pending:' . $phone);
    }

    /**
     * Envoi via une passerelle HTTP connectée à TON numéro WhatsApp
     * (le site génère le code, la passerelle l'envoie depuis ton numéro).
     *
     * Exemples :
     * - Ultramsg : URL=https://api.ultramsg.com/{instance}/messages/chat,
     *   FORMAT=form, TO=to, TEXT=body, TOKEN(+TOKEN_PARAM=token) = token API.
     * - Green-API : URL=https://api.green-api.com/waInstance{id}/sendMessage/{token},
     *   FORMAT=json, TO=chatId, TEXT=message, SUFFIX=@c.us (token déjà dans l'URL).
     */
    protected static function sendViaGateway(string $phone, string $code): void
    {
        $url = (string) config('services.whatsapp.gateway.url');
        if ($url === '') {
            throw new \RuntimeException('Passerelle WhatsApp non configurée (WHATSAPP_GATEWAY_URL).');
        }

        $method = strtoupper((string) config('services.whatsapp.gateway.method', 'POST'));
        $format = strtolower((string) config('services.whatsapp.gateway.format', 'json'));
        $toParam = (string) config('services.whatsapp.gateway.to_param', 'to');
        $textParam = (string) config('services.whatsapp.gateway.text_param', 'body');
        $suffix = (string) config('services.whatsapp.gateway.to_suffix', '');
        $token = (string) config('services.whatsapp.gateway.token', '');
        $tokenParam = (string) config('services.whatsapp.gateway.token_param', 'token');

        $message = "Boutique : votre code de vérification est {$code}. Il expire dans 10 minutes.";

        $params = [$toParam => $phone . $suffix, $textParam => $message];
        if ($token !== '') {
            $params[$tokenParam] = $token;
        }

        if ($method === 'GET') {
            $res = Http::get($url, $params);
        } elseif ($format === 'form') {
            $res = Http::asForm()->post($url, $params);
        } else {
            $res = Http::post($url, $params);
        }

        if (!$res->successful()) {
            Log::warning('Passerelle WhatsApp refusée (HTTP ' . $res->status() . ') : ' . $res->body());
            throw new \RuntimeException('Envoi WhatsApp refusé (HTTP ' . $res->status() . ').');
        }

        // Contrôle optionnel d'un champ de succès (ex : "sent" chez Ultramsg).
        $successKey = (string) config('services.whatsapp.gateway.success_key', '');
        if ($successKey !== '' && !$res->json($successKey)) {
            Log::warning('Passerelle WhatsApp : envoi non confirmé : ' . $res->body());
            throw new \RuntimeException('Envoi WhatsApp non confirmé par la passerelle.');
        }

        Log::info("Code WhatsApp envoyé au +{$phone} via passerelle.");
    }

    /**
     * Envoi du code via Meta WhatsApp Cloud API (template utilitaire).
     * Template à créer dans le dashboard Meta (catégorie UTILITY, ex FR) :
     *   « Boutique : votre code de vérification est {{1}}. Il expire dans 10 minutes. »
     */
    protected static function sendViaMeta(string $phone, string $code): void
    {
        $base = rtrim((string) config('services.whatsapp.base_url', 'https://graph.facebook.com/v21.0'), '/');
        $token = (string) config('services.whatsapp.token');
        $phoneNumberId = (string) config('services.whatsapp.phone_number_id');
        $template = (string) config('services.whatsapp.template', 'otp_boutique');
        $lang = (string) config('services.whatsapp.lang', 'fr');

        if ($token === '' || $phoneNumberId === '') {
            throw new \RuntimeException('WhatsApp non configuré (WHATSAPP_TOKEN / WHATSAPP_PHONE_NUMBER_ID).');
        }

        $res = Http::withToken($token)->post($base . '/' . $phoneNumberId . '/messages', [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $template,
                'language' => ['code' => $lang],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [['type' => 'text', 'text' => $code]],
                    ],
                ],
            ],
        ]);

        if (!$res->successful()) {
            Log::warning('Envoi WhatsApp refusé (HTTP ' . $res->status() . ') : ' . $res->body());
            throw new \RuntimeException('Envoi WhatsApp refusé (HTTP ' . $res->status() . ').');
        }

        Log::info("Code WhatsApp envoyé au +{$phone} (template {$template}).");
    }
}

<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Géolocalise une adresse IP via l'API gratuite ip-api.com (sans clé).
 * Résultat mis en cache 7 jours. Renvoie null pour les IP locales/privées.
 */
class IpGeolocator
{
    public static function locate(?string $ip): ?array
    {
        if (!$ip || self::isLocal($ip)) {
            return null;
        }

        return Cache::remember("geoip:{$ip}", now()->addDays(7), function () use ($ip) {
            try {
                $resp = Http::timeout(5)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,country,regionName,city,lat,lon',
                ]);
                $d = $resp->json();
                if (!is_array($d) || ($d['status'] ?? '') !== 'success') {
                    return null;
                }
                return [
                    'lat' => $d['lat'] ?? null,
                    'lon' => $d['lon'] ?? null,
                    'city' => $d['city'] ?? null,
                    'region' => $d['regionName'] ?? null,
                    'country' => $d['country'] ?? null,
                ];
            } catch (\Throwable $e) {
                return null;
            }
        });
    }

    protected static function isLocal(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1'], true)
            || str_starts_with($ip, '192.168.')
            || str_starts_with($ip, '10.')
            || str_starts_with($ip, '172.16.')
            || str_starts_with($ip, '172.17.')
            || str_starts_with($ip, '172.18.');
    }
}

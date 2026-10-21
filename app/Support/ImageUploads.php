<?php

namespace App\Support;

use App\Models\photos as Photos;
use App\Models\Produits;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Téléversement d'images sans multipart.
 *
 * Le pare-feu de l'hébergement bloque les requêtes multipart avant qu'elles
 * n'atteignent Laravel. Les formulaires envoient donc l'image en base64 dans
 * un corps JSON ; ce service la décode, vérifie qu'il s'agit bien d'une image
 * et l'écrit sur le disque public.
 *
 * Les fichiers sont rangés dans un dossier propre à l'utilisateur connecté
 * (`uploads/admin-10`, `uploads/vendeur-14`, ...). Au moment d'enregistrer le
 * produit, seuls les chemins de ce dossier peuvent être attachés : un vendeur
 * ne peut donc pas réutiliser l'image téléversée par quelqu'un d'autre.
 */
class ImageUploads
{
    /** Taille maximale du fichier décodé (2 Mo, comme les formulaires web). */
    public const MAX_BYTES = 2 * 1024 * 1024;

    /** Nombre maximal de photos par produit. */
    public const MAX_PRODUCT_IMAGES = 10;

    /** Types réellement acceptés (les SVG ne sont volontairement pas autorisés). */
    private const MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    /**
     * Décode et stocke une image envoyée en base64.
     *
     * @return array{path: string, url: string, name: string|null}
     */
    public static function storeBase64(User $user, string $payload, ?string $originalName = null): array
    {
        $raw = trim($payload);

        // Accepte aussi bien le base64 brut qu'une data-URL.
        if (preg_match('/^data:image\/[a-z0-9.+-]+;base64,(.*)$/is', $raw, $matches)) {
            $raw = $matches[1];
        }

        $raw = preg_replace('/\s+/', '', $raw) ?? '';

        // Limite avant décodage : évite de charger en mémoire une chaîne
        // démesurée. 2 Mo en base64 représentent environ 2,8 Mo de texte.
        if ($raw === '' || strlen($raw) > (int) ceil(self::MAX_BYTES * 4 / 3) + 16) {
            throw ValidationException::withMessages([
                'data' => 'Image invalide ou trop volumineuse (2 Mo maximum).',
            ]);
        }

        $binary = base64_decode($raw, true);
        if ($binary === false || $binary === '') {
            throw ValidationException::withMessages([
                'data' => 'Le contenu de l’image est invalide.',
            ]);
        }

        if (strlen($binary) > self::MAX_BYTES) {
            throw ValidationException::withMessages([
                'data' => 'Image trop volumineuse (2 Mo maximum).',
            ]);
        }

        $info = @getimagesizefromstring($binary);
        $mime = is_array($info) && ! empty($info['mime']) ? strtolower($info['mime']) : '';
        $width = is_array($info) ? (int) ($info[0] ?? 0) : 0;
        $height = is_array($info) ? (int) ($info[1] ?? 0) : 0;

        if (! isset(self::MIME_EXTENSIONS[$mime])) {
            throw ValidationException::withMessages([
                'data' => 'Format d’image non pris en charge (JPG, PNG, WEBP ou GIF).',
            ]);
        }

        // Protège le redimensionnement Glide contre les images démesurées.
        if ($width < 1 || $height < 1 || $width > 8000 || $height > 8000) {
            throw ValidationException::withMessages([
                'data' => 'Les dimensions de l’image sont invalides (8000 px maximum).',
            ]);
        }

        $directory = self::directoryFor($user);
        $path = $directory.'/'.Str::uuid().'.'.self::MIME_EXTENSIONS[$mime];

        if (! Storage::disk('public')->put($path, $binary)) {
            throw ValidationException::withMessages([
                'data' => 'Impossible d’enregistrer l’image sur le serveur.',
            ]);
        }

        return [
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'name' => $originalName,
        ];
    }

    /** Dossier de téléversement propre à l'utilisateur. */
    public static function directoryFor(User $user): string
    {
        $role = preg_replace('/[^a-z0-9_-]/i', '', (string) $user->role) ?: 'user';

        return 'uploads/'.$role.'-'.(int) $user->id;
    }

    /** Vérifie qu'un chemin appartient bien à l'utilisateur et existe. */
    public static function ownedBy(?User $user, mixed $path): bool
    {
        if (! $user || ! is_string($path) || $path === '' || str_contains($path, '..')) {
            return false;
        }

        $prefix = self::directoryFor($user).'/';

        return str_starts_with($path, $prefix)
            && Storage::disk('public')->exists($path);
    }

    /** Le chemin est-il déjà rattaché à un enregistrement photos ? */
    public static function isAttached(string $path): bool
    {
        return Photos::where('filename', $path)->exists();
    }

    /**
     * Attache des images téléversées à un produit (10 photos maximum).
     * La première photo de la galerie devient l'image principale.
     */
    public static function attachToProduct(Produits $product, array $paths, User $user): int
    {
        $existing = $product->photos()->pluck('filename')->all();
        $remaining = max(0, self::MAX_PRODUCT_IMAGES - count($existing));
        $added = 0;

        foreach (array_values(array_unique($paths)) as $path) {
            if ($added >= $remaining) {
                break;
            }

            if (
                ! is_string($path)
                || in_array($path, $existing, true)
                || ! self::ownedBy($user, $path)
                || self::isAttached($path)
            ) {
                continue;
            }

            $product->photos()->create(['filename' => $path]);
            $existing[] = $path;
            $added++;
        }

        self::syncPrimary($product);

        return $added;
    }

    /** Aligne `produits.image` sur la première photo de la galerie. */
    public static function syncPrimary(Produits $product): void
    {
        $first = $product->photos()->orderBy('id')->first();
        $product->forceFill(['image' => $first?->filename])->save();
    }

    /** Supprime un fichier en attente (uniquement s'il appartient à l'utilisateur). */
    public static function deletePending(User $user, mixed $path): void
    {
        if (! self::ownedBy($user, $path) || self::isAttached((string) $path)) {
            return;
        }

        Storage::disk('public')->delete((string) $path);
    }

    /** URL publique d'un fichier du disque public. */
    public static function url(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
}

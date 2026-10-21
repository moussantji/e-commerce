<?php

namespace App\Http\Controllers;

use App\Support\ImageUploads;
use Illuminate\Http\Request;

/**
 * Point d'entrée JSON pour les images des formulaires admin/vendeur.
 *
 * Aucun fichier multipart n'est envoyé : le client transmet une chaîne
 * base64. Le contrôleur vérifie le rôle puis délègue le décodage/stockage à
 * ImageUploads.
 */
class ImageUploadController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user && in_array($user->role, ['admin', 'vendeur'], true),
            403,
            'Accès réservé aux administrateurs et aux vendeurs.',
        );

        $data = $request->validate([
            'data' => 'required|string',
            'name' => 'nullable|string|max:255',
            'mime' => 'nullable|string|max:100',
        ]);

        $result = ImageUploads::storeBase64(
            $user,
            $data['data'],
            $data['name'] ?? null,
        );

        return response()->json($result, 201);
    }
}

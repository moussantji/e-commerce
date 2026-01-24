<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Log;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', // 5MB max
            ]);

            if (!$request->hasFile('file')) {
                throw new \Exception('Aucun fichier fourni');
            }

            $file = $request->file('file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            // Créer un nom de fichier unique
            $filename = Str::slug($originalName) . '-' . time() . '.' . $extension;

            // Stocker le fichier
            $path = $file->storeAs('public/media', $filename);

            // Créer des versions redimensionnées si nécessaire
            $this->createImageVersions($file, $filename);

            return response()->json([
                'success' => true,
                'location' => Storage::url($path),
                'filename' => $filename,
                'original_name' => $originalName . '.' . $extension
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Crée des versions redimensionnées de l'image
     */
    private function createImageVersions($file, $filename)
    {
        try {
            // Créer un dossier pour les miniatures s'il n'existe pas
            $thumbnailPath = storage_path('app/public/media/thumbnails');
            if (!file_exists($thumbnailPath)) {
                mkdir($thumbnailPath, 0755, true);
            }

            // Créer une instance d'intervention image
            $image = Image::make($file->getRealPath());

            // Redimensionner pour la version miniature (300x300)
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($thumbnailPath . '/' . $filename);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création des versions d\'image: ' . $e->getMessage());
            return false;
        }
    }
}

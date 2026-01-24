<?php

namespace App\Http\Controllers;

use Nyholm\Psr7\Stream;
use Nyholm\Psr7\Response;
use Illuminate\Http\Request;
use League\Glide\ServerFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Illuminate\Support\Facades\Storage;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Responses\PsrResponseFactory;
use League\Glide\Responses\SymfonyResponseFactory;

class ImageController extends Controller
{
    public function show(Request $request, string $path)
    {
        SignatureFactory::create(config('glide.key'))->validateRequest($request->path(), $request->all());
        $server = ServerFactory::create([
            'source' => Storage::disk('public')->getDriver(),
            'cache' => Storage::disk('local')->getDriver(),
            'cache_path_prefix' => '.cache',
            'base_url' => 'images'
        ]);
        // Glide 3 : génère ET renvoie directement les octets de l'image
        $imageData = $server->outputImage($path, $request->all());

        // MIME type automatique
        $mime = $server->getSource()->mimeType($path) ?: 'image/png';

        return response($imageData, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}

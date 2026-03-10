<?php

namespace App\Http\Controllers;

use App\Models\photos;
use Illuminate\Http\Request;

class PhotoControler extends Controller
{
    public function destroyPhoto(photos $photo)
    {
        // Supprime DB
        $photo->delete();

        return '';
    }
}

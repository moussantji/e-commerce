<?php

namespace App\Models;

use App\Models\AvisClient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewResponse extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewResponseFactory> */
    use HasFactory;

    protected $fillable = ['avis_client_id', 'admin_id', 'message'];

    public function review()
    {
        return $this->belongsTo(AvisClient::class, 'avis_client_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

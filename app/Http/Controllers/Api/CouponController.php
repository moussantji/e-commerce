<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /** Bons disponibles (codes promo actifs et valides). */
    public function index(Request $request)
    {
        $codes = PromoCode::where('is_active', true)
            ->orderByDesc('value')
            ->get()
            ->filter(fn (PromoCode $c) => $c->isValid())
            ->values();

        $data = $codes->map(fn (PromoCode $c) => [
            'id' => $c->id,
            'code' => $c->code,
            'type' => $c->type,
            'value' => (float) $c->value,
            'description' => $c->type === 'percentage'
                ? 'Réduction de ' . rtrim(rtrim(number_format($c->value, 2), '0'), '.') . '%'
                : 'Bon de ' . number_format($c->value, 0, ',', ' ') . ' FCFA',
            'expires_at' => optional($c->expires_at)->format('d/m/Y'),
        ]);

        return response()->json([
            'data' => $data,
            'count' => $data->count(),
        ]);
    }
}

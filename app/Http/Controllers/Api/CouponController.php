<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /** Bons attribués au client connecté (jamais la liste publique). */
    public function index(Request $request)
    {
        $codes = $request->user()
            ->coupons()
            ->where('promo_codes.is_active', true)
            ->orderByPivot('sent_at', 'desc')
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
            'assigned_at' => optional($c->pivot->sent_at)->format('d/m/Y'),
            'source' => $c->pivot->source,
        ]);

        return response()->json([
            'data' => $data,
            'count' => $data->count(),
        ]);
    }

    /** Valide un code et renvoie la réduction pour un sous-total donné. */
    public function apply(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $promo = PromoCode::whereCode((string) $data['code'])->first();
        if (!$promo || !$promo->isValid()) {
            return response()->json(
                ['valid' => false, 'message' => 'Code promo invalide ou expiré.'],
                422,
            );
        }

        if ($promo->is_automatic && !$promo->isAssignedTo($request->user())) {
            return response()->json(
                ['valid' => false, 'message' => 'Ce coupon est réservé aux clients éligibles.'],
                422,
            );
        }

        return response()->json([
            'valid' => true,
            'code' => $promo->code,
            'type' => $promo->type,
            'value' => (float) $promo->value,
            'discount' => round($promo->calculateDiscount((float) $data['subtotal']), 2),
        ]);
    }
}

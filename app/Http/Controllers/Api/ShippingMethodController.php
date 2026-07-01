<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Livraison;
use Illuminate\Http\Request;

/**
 * Méthodes de livraison disponibles (pour la sélection au checkout).
 */
class ShippingMethodController extends Controller
{
    public function index(Request $request)
    {
        $methods = Livraison::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->method_name,
                'price' => (float) ($m->price ?? 0),
                'delay' => $this->delay($m),
                'description' => $m->description,
            ]);

        return response()->json(['data' => $methods]);
    }

    private function delay(Livraison $m): ?string
    {
        if ($m->delivery_time) {
            return $m->delivery_time;
        }
        $unit = $m->delivery_time_unit ?: 'jours';
        if ($m->delivery_time_min && $m->delivery_time_max) {
            return "{$m->delivery_time_min}-{$m->delivery_time_max} {$unit}";
        }
        if ($m->delivery_time_max) {
            return "{$m->delivery_time_max} {$unit}";
        }
        return null;
    }
}

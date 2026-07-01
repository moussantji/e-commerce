<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paiements;
use Illuminate\Http\Request;

/**
 * Méthodes de paiement disponibles (Orange Money, Moov Money, Wave...)
 * avec leurs instructions à suivre par le client.
 */
class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $methods = Paiements::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->method_name,
                'provider' => $m->provider_name,
                'description' => $m->description,
                'instructions' => $m->instructions,
                'account_number' => $m->account_number,
                'cod' => $m->isCashOnDelivery(),
                'fee' => (float) ($m->fee ?? 0),
                'fee_percentage' => (float) ($m->fee_percentage ?? 0),
                'logo' => $this->abs($m->logoUrl()),
            ]);

        return response()->json(['data' => $methods]);
    }

    private function abs(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        return rtrim(request()->getSchemeAndHttpHost(), '/') . '/' . ltrim($path, '/');
    }
}

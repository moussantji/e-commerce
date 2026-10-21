<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Services\CouponDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    /**
     * Affiche la liste des coupons.
     */
    public function index()
    {
        $coupons = PromoCode::withCount('users')->latest()->paginate(10);
        $hasAutomaticCoupon = PromoCode::where('is_automatic', true)->exists();

        return view('admin.coupons.index', compact('coupons', 'hasAutomaticCoupon'));
    }

    /**
     * Affiche le formulaire de création d'un coupon.
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Enregistre un nouveau coupon.
     */
    public function store(Request $request)
    {
        $validated = $this->validateCoupon($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_automatic'] = $request->boolean('is_automatic');

        $promoCode = DB::transaction(function () use ($validated) {
            if ($validated['is_automatic']) {
                // Un seul coupon peut piloter la distribution automatique.
                PromoCode::where('is_automatic', true)->update(['is_automatic' => false]);
            }

            return PromoCode::create($validated);
        });

        $message = 'Le coupon a été créé avec succès.';
        if ($promoCode->is_automatic) {
            $message .= ' Il sera appliqué/attribué automatiquement selon les conditions configurées.';
        }

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', $message);
    }

    /**
     * Affiche le formulaire de modification d'un coupon.
     */
    public function edit(PromoCode $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Met à jour un coupon existant.
     */
    public function update(Request $request, PromoCode $coupon)
    {
        $validated = $this->validateCoupon($request, $coupon);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_automatic'] = $request->boolean('is_automatic');

        DB::transaction(function () use ($coupon, $validated) {
            if ($validated['is_automatic']) {
                PromoCode::where('id', '!=', $coupon->id)
                    ->where('is_automatic', true)
                    ->update(['is_automatic' => false]);
            }

            $coupon->update($validated);
        });

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Le code promo a été mis à jour avec succès.');
    }

    /**
     * Envoie le coupon aux clients qui atteignent déjà les seuils mensuels.
     */
    public function distribute(PromoCode $coupon)
    {
        if (!$coupon->is_automatic) {
            return back()->with('error', 'Activez d’abord « Distribution automatique » sur ce coupon.');
        }

        $sent = app(CouponDistributionService::class)->distributeToEligibleUsers($coupon);

        return back()->with(
            'success',
            $sent > 0
                ? $sent . ' client(s) éligible(s) ont reçu le coupon par notification et email.'
                : 'Aucun nouveau client éligible à contacter pour le moment.'
        );
    }

    /**
     * Supprime un coupon.
     */
    public function destroy(PromoCode $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Le code promo a été supprimé avec succès.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCoupon(Request $request, ?PromoCode $coupon = null): array
    {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('promo_codes', 'code')->ignore($coupon?->id),
            ],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => [
                'required',
                'numeric',
                'min:0.01',
                Rule::when(
                    $request->input('type') === 'percentage',
                    ['max:100'],
                    []
                ),
            ],
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
            'is_automatic' => 'boolean',
        ]);
    }
}

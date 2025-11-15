<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    /**
     * Affiche la liste des coupons
     */
    public function index()
    {
        $promoCodes = PromoCode::latest()->paginate(10);
        return view('admin.coupons.index', compact('promoCodes'));
    }

    /**
     * Affiche le formulaire de création d'un coupon
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Enregistre un nouveau coupon
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean'
        ]);

        $promoCode = PromoCode::create($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Le coupon a été créé avec succès.');
    }

    /**
     * Affiche les détails d'un coupon
     */
    public function show(PromoCode $promoCode)
    {
        return view('admin.coupons.show', compact('promoCode'));
    }

    /**
     * Affiche le formulaire de modification d'un coupon
     */
    public function edit(PromoCode $promoCode)
    {
        return view('admin.coupons.edit', compact('promoCode'));
    }

    /**
     * Met à jour un coupon existant
     */
    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('promo_codes', 'code')->ignore($promoCode->id)
            ],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean'
        ]);

        $promoCode->update($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Le code promo a été mis à jour avec succès.');
    }

    /**
     * Supprime un coupon
     */
    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Le code promo a été supprimé avec succès.');
    }
}

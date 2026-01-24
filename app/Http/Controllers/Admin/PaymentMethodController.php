<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    /**
     * Affiche la liste des méthodes de paiement
     */
    public function index()
    {
        $paymentMethods = Paiements::orderBy('sort_order')->paginate(10);
        return view('admin.settings.payment_methods.index', compact('paymentMethods'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('admin.settings.payment_methods.form');
    }

    /**
     * Enregistre une nouvelle méthode de paiement
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        // Conversion du JSON de configuration
        if (!empty($validated['config'])) {
            $validated['config'] = json_decode($validated['config'], true);
        }

        $paiements = Paiements::create($validated);

        // ✅ 2️⃣ ENSUITE attacher logo
        if ($request->hasFile('logo')) {
            $paiements->attachfiles([$request->file('logo')]);
        }

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Méthode de paiement créée avec succès');
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Paiements $paymentMethod)
    {
        return view('admin.settings.payment_methods.form', [
            'paymentMethod' => $paymentMethod
        ]);
    }

    /**
     * Met à jour une méthode de paiement existante
     */
    public function update(Request $request, Paiements $paymentMethod)
    {
        $validated = $this->validateRequest($request, $paymentMethod->id);

        /// Dans BrandController::update() ET ::destroy()
        if ($request->hasFile('logo') || $request->filled('remove_logo')) {
            // ✅ 1️⃣ SUPPRIME FICHIERS d'ABORD
            foreach ($paymentMethod->photos as $photo) {
                Storage::disk('public')->delete($photo->filename);
            }

            // ✅ 2️⃣ SUPPRIME DB
            $paymentMethod->photos()->delete();
        }

        // ✅ 2️⃣ ENSUITE attacher logo
        if ($request->hasFile('logo')) {
            $paymentMethod->attachfiles([$request->file('logo')]);
        }

        // Conversion du JSON de configuration
        if (!empty($validated['config'])) {
            $validated['config'] = json_decode($validated['config'], true);
        } else {
            $validated['config'] = null;
        }

        $paymentMethod->update($validated);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Méthode de paiement mise à jour avec succès');
    }

    /**
     * Supprime une méthode de paiement
     */
    public function destroy(Paiements $paymentMethod)
    {
        // Vérifier s'il y a des commandes liées
        if ($paymentMethod->orders()->exists()) {
            return back()->with('error', 'Impossible de supprimer cette méthode car elle est utilisée dans des commandes.');
        }

        // ✅ 1️⃣ SUPPRIME TOUTES les photos PHYSIQUES
        foreach ($paymentMethod->photos as $photo) {
            if (Storage::disk('public')->exists($photo->filename)) {
                Storage::disk('public')->delete($photo->filename);
            }
            $photo->delete(); // ✅ Supprime ligne DB photos
        }

        // ✅ 2️⃣ MAINTENANT supprime shipping
        $paymentMethod->delete();

        return redirect()
            ->route('admin.payment-methods.index')
            ->with('success', 'Méthode de paiement supprimée avec succès');
    }

    /**
     * Valide les données de la requête
     */
    protected function validateRequest(Request $request, $id = null)
    {
        $rules = [
            'method_name' => ['required', 'string', 'max:255'],
            'provider_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'fee_percentage' => ['nullable', 'numeric', 'between:0,100'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'config' => ['nullable', 'json'],
        ];

        return $request->validate($rules);
    }
}

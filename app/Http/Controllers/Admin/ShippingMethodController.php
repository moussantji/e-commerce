<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Livraison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ShippingMethodController extends Controller
{
    /**
     * Affiche la liste des méthodes de livraison
     */
    public function index()
    {
        $shippingMethods = Livraison::orderBy('sort_order')->paginate(10);
        return view('admin.settings.shipping_methods.index', compact('shippingMethods'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('admin.settings.shipping_methods.form');
    }

    /**
     * Enregistre une nouvelle méthode de livraison
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        // Conversion des champs JSON avant création.
        $validated = $this->processJsonFields($validated);

        // ✅ CRÉER le shipping method (une seule fois)
        $livraison = Livraison::create($validated);

        // ✅ ENSUITE attacher logo
        if ($request->hasFile('logo')) {
            $livraison->attachfiles([$request->file('logo')]);
        }

        return redirect()
            ->route('admin.shipping-methods.index')
            ->with('success', 'Méthode de livraison créée avec succès');
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Livraison $shippingMethod)
    {
        return view('admin.settings.shipping_methods.form', [
            'shippingMethod' => $shippingMethod
        ]);
    }

    /**
     * Met à jour une méthode de livraison existante
     */
    public function update(Request $request, Livraison $shippingMethod)
    {
        $validated = $this->validateRequest($request, $shippingMethod->id);

        // Dans BrandController::update() ET ::destroy()
        if ($request->hasFile('logo') || $request->filled('remove_logo')) {
            // ✅ 1️⃣ SUPPRIME FICHIERS d'ABORD
            foreach ($shippingMethod->photos as $photo) {
                Storage::disk('public')->delete($photo->filename);
            }

            // ✅ 2️⃣ SUPPRIME DB
            $shippingMethod->photos()->delete();
        }

        // ✅ 2️⃣ ENSUITE attacher logo
        if ($request->hasFile('logo')) {
            $shippingMethod->attachfiles([$request->file('logo')]);
        }

        // Conversion des champs JSON
        $validated = $this->processJsonFields($validated);

        $shippingMethod->update($validated);

        return redirect()
            ->route('admin.shipping-methods.index')
            ->with('success', 'Méthode de livraison mise à jour avec succès');
    }

    /**
     * Supprime une méthode de livraison
     */
    public function destroy(Livraison $shippingMethod)
    {
        // Vérifier s'il y a des commandes liées
        if ($shippingMethod->orders()->exists()) {
            return back()->with('error', 'Impossible de supprimer cette méthode car elle est utilisée dans des commandes.');
        }

        // ✅ 1️⃣ SUPPRIME TOUTES les photos PHYSIQUES
        foreach ($shippingMethod->photos as $photo) {
            if (Storage::disk('public')->exists($photo->filename)) {
                Storage::disk('public')->delete($photo->filename);
            }
            $photo->delete(); // ✅ Supprime ligne DB photos
        }

        // ✅ 2️⃣ MAINTENANT supprime shipping
        $shippingMethod->delete();

        return redirect()
            ->route('admin.shipping-methods.index')
            ->with('success', 'Méthode de livraison supprimée avec succès');
    }

    /**
     * Traite les champs JSON avant enregistrement
     */
    protected function processJsonFields($data)
    {
        // Zones de livraison
        if (!empty($data['zones'])) {
            $data['zones'] = json_decode($data['zones'], true);
        } else {
            $data['zones'] = null;
        }

        // Configuration
        if (!empty($data['config'])) {
            $data['config'] = json_decode($data['config'], true);
        } else {
            $data['config'] = null;
        }

        return $data;
    }

    /**
     * Valide les données de la requête
     */
    protected function validateRequest(Request $request, $id = null)
    {
        $rules = [
            'method_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'delivery_time_min' => ['nullable', 'integer', 'min:0'],
            'delivery_time_max' => ['nullable', 'integer', 'min:0', 'gte:delivery_time_min'],
            'delivery_time_unit' => ['required', 'in:hours,days,weeks'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'weight_limit' => ['nullable', 'numeric', 'min:0'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'zones' => ['nullable', 'json'],
            'config' => ['nullable', 'json'],
        ];

        return $request->validate($rules);
    }
}

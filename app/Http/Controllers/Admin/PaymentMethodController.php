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
        
        // Gestion du logo
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('payment-methods', 'public');
        }
        
        // Conversion du JSON de configuration
        if (!empty($validated['config'])) {
            $validated['config'] = json_decode($validated['config'], true);
        }
        
        Paiements::create($validated);
        
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
        
        // Gestion du logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si nécessaire
            if ($paymentMethod->logo) {
                Storage::disk('public')->delete($paymentMethod->logo);
            }
            $validated['logo'] = $request->file('logo')->store('payment-methods', 'public');
        } elseif ($request->has('remove_logo') && $paymentMethod->logo) {
            Storage::disk('public')->delete($paymentMethod->logo);
            $validated['logo'] = null;
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
        
        // Supprimer le logo si nécessaire
        if ($paymentMethod->logo) {
            Storage::disk('public')->delete($paymentMethod->logo);
        }
        
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

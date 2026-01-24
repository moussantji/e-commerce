<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::orderBy('sort_order')->latest('id')->paginate(15);
        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ✅ $brand = null → mode "Ajouter"
        $brand = null;
        return view('admin.brands.form', compact('brand'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // ✅ 1️⃣ CRÉER d'ABORD la Brand (pour $this->id)
        $brand = Brand::create($data);

        // ✅ 2️⃣ ENSUITE attacher logo
        if ($request->hasFile('logo')) {
            $brand->attachfiles([$request->file('logo')]);
        }

        return redirect()->route('admin.brands.index')->with('success', 'Marque créée !');
    }

    public function show(Brand $brand)
    {
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        // ✅ CORRIGÉ : $brand (pas $brands)
        return view('admin.brands.form', compact('brand'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'slug' => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $data = $request->all();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // ✅ 1️⃣ UPDATE Brand (sans logo)
        $brand->update($data);

        // Dans BrandController::update() ET ::destroy()
        if ($request->hasFile('logo') || $request->filled('remove_logo')) {
            // ✅ 1️⃣ SUPPRIME FICHIERS d'ABORD
            foreach ($brand->photos as $photo) {
                Storage::disk('public')->delete($photo->filename);
            }

            // ✅ 2️⃣ SUPPRIME DB
            $brand->photos()->delete();
        }

        // ✅ 2️⃣ ENSUITE attacher logo
        if ($request->hasFile('logo')) {
            $brand->attachfiles([$request->file('logo')]);
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marque modifiée avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        // Supprimer l'image associée si elle existe
        if ($brand->image_path) {
            Storage::disk('public')->delete($brand->image_path);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marque supprimée avec succès !');

    }
}

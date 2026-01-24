<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Caracteristiques;
use App\Http\Controllers\Controller;

class CaracteristiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $caracteristiques = Caracteristiques::orderBy('id') // ✅ Au lieu de sort_order
        ->paginate(20);
        return view('admin.caracteristiques.index', compact('caracteristiques'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.caracteristiques.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:caracteristiques,name',
            'type' => 'required|string|max:100',
            'unit' => 'nullable|string|max:50',
            'is_filterable' => 'boolean',
        ]);

        Caracteristiques::create($request->all());

        return redirect()->route('admin.caracteristiques.index')
            ->with('success', 'Caractéristique créée avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Caracteristiques $caracteristique)
    {
        return view('admin.caracteristiques.form', compact('caracteristique'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Caracteristiques $caracteristique)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:caracteristiques,name,' . $caracteristique->id,
            'type' => 'required|string|max:100',
            'unit' => 'nullable|string|max:50',
            'is_filterable' => 'boolean',
        ]);

        $caracteristique->update($request->all());

        return redirect()->route('admin.caracteristiques.index')
            ->with('success', 'Caractéristique modifiée avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Caracteristiques $caracteristique)
    {
        $caracteristique->delete();

        return redirect()->route('admin.caracteristiques.index')
            ->with('success', 'Caractéristique supprimée avec succès !');
    }
}

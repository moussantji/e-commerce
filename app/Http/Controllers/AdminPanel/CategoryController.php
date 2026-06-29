<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Categories as Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories
     */
    public function index()
    {
        $categories = Category::with('photos')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->orderBy('sort_order')->get();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Enregistre une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        // Slug auto-généré si vide
        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? null, $validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $category = Category::create($validated);

        // Lier l'image via la relation photos (utilisée par getPhoto())
        if ($request->hasFile('image')) {
            $this->storeCategoryImage($category, $request->file('image'));
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès');
    }

    /**
     * Affiche une catégorie spécifique
     */
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Affiche le formulaire de modification
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->orderBy('sort_order')->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Met à jour une catégorie
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? null, $validated['name'], $category->id);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        // Remplacer l'image si une nouvelle est fournie
        if ($request->hasFile('image')) {
            $this->storeCategoryImage($category, $request->file('image'), true);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès');
    }

    /**
     * Supprime une catégorie
     */
    public function destroy(Category $category)
    {
        // Vérifier si la catégorie est utilisée par des produits
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Impossible de supprimer une catégorie contenant des produits');
        }

        // Supprimer les photos associées (le model Photos supprime aussi le fichier)
        foreach ($category->photos as $photo) {
            $photo->delete();
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès');
    }

    /**
     * Enregistre l'image d'une catégorie : crée un enregistrement dans la table
     * photos (lu par getPhoto()) et conserve aussi le chemin dans la colonne image.
     */
    protected function storeCategoryImage(Category $category, $file, bool $replace = false): void
    {
        if (!$file || $file->getError()) {
            return;
        }

        // Supprimer l'ancienne image lors d'un remplacement
        if ($replace) {
            foreach ($category->photos()->get() as $photo) {
                $photo->delete();
            }
        }

        $filename = $file->store('Category/' . $category->id, 'public');

        $category->photos()->create(['filename' => $filename]);

        // Conserve aussi le chemin dans la colonne image (compatibilité)
        $category->forceFill(['image' => $filename])->save();
    }

    /**
     * Génère un slug unique à partir du slug fourni ou du nom.
     */
    protected function uniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name);
        if ($base === '') {
            $base = Str::slug($name) ?: 'categorie';
        }

        $slug = $base;
        $i = 1;
        while (
            Category::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}

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
        // Affichage arborescent : racines + leurs sous-catégories
        $categories = Category::whereNull('parent_id')
            ->with([
                'photos',
                'children' => fn ($q) => $q->with('photos')->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->paginate(10);

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
            'banner_image_1' => 'nullable|image|max:2048',
            'banner_image_2' => 'nullable|image|max:2048',
        ]);

        // Les fichiers sont gérés séparément (pas en mass-assignment)
        unset($validated['banner_image_1'], $validated['banner_image_2']);

        // Slug auto-généré si vide
        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? null, $validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        if ($error = $this->hierarchyError($request->integer('parent_id') ?: null)) {
            return back()->withInput()->withErrors(['parent_id' => $error]);
        }

        $category = Category::create($validated);

        // Lier l'image via la relation photos (utilisée par getPhoto())
        if ($request->hasFile('image')) {
            $this->storeCategoryImage($category, $request->file('image'));
        }

        // Images de bannière (choisies manuellement)
        $this->storeBannerImage($category, $request->file('banner_image_1'), 'banner_image_1');
        $this->storeBannerImage($category, $request->file('banner_image_2'), 'banner_image_2');

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
        // Seules les catégories principales (racines) peuvent être parentes,
        // et jamais la catégorie elle-même.
        $categories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('sort_order')
            ->get();

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
            'banner_image_1' => 'nullable|image|max:2048',
            'banner_image_2' => 'nullable|image|max:2048',
        ]);

        // Les fichiers sont gérés séparément (pas en mass-assignment)
        unset($validated['banner_image_1'], $validated['banner_image_2']);

        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? null, $validated['name'], $category->id);
        $validated['is_active'] = $request->boolean('is_active');

        if ($error = $this->hierarchyError($request->integer('parent_id') ?: null, $category)) {
            return back()->withInput()->withErrors(['parent_id' => $error]);
        }

        $category->update($validated);

        // Remplacer l'image si une nouvelle est fournie
        if ($request->hasFile('image')) {
            $this->storeCategoryImage($category, $request->file('image'), true);
        }

        // Images de bannière (remplacées si de nouvelles sont fournies)
        $this->storeBannerImage($category, $request->file('banner_image_1'), 'banner_image_1');
        $this->storeBannerImage($category, $request->file('banner_image_2'), 'banner_image_2');

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

        // Empêcher la suppression d'une catégorie ayant des sous-catégories
        if ($category->children()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Supprimez ou déplacez d\'abord les sous-catégories de cette catégorie');
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
     * Enregistre une image de bannière dans la colonne dédiée (banner_image_1 / 2).
     * Remplace l'ancienne si présente.
     */
    protected function storeBannerImage(Category $category, $file, string $column): void
    {
        if (!$file || $file->getError()) {
            return;
        }

        // Supprime l'ancien fichier s'il existe
        if ($category->$column) {
            Storage::disk('public')->delete($category->$column);
        }

        $path = $file->store('Category/' . $category->id . '/banners', 'public');

        $category->forceFill([$column => $path])->save();
    }

    /**
     * Valide la cohérence de la hiérarchie parent → enfant.
     * Retourne un message d'erreur, ou null si tout est valide.
     *
     * Règles :
     *  - une catégorie ne peut pas être sa propre parente ;
     *  - la parente doit être une catégorie principale (hiérarchie limitée à 2 niveaux) ;
     *  - une catégorie possédant des sous-catégories ne peut pas devenir une sous-catégorie.
     */
    protected function hierarchyError(?int $parentId, ?Category $category = null): ?string
    {
        if (!$parentId) {
            return null;
        }

        if ($category && $parentId === $category->id) {
            return "Une catégorie ne peut pas être sa propre catégorie parente.";
        }

        $parent = Category::find($parentId);
        if (!$parent || $parent->parent_id !== null) {
            return "La catégorie parente doit être une catégorie principale (hiérarchie limitée à 2 niveaux).";
        }

        if ($category && $category->children()->exists()) {
            return "Cette catégorie possède des sous-catégories : elle ne peut pas devenir elle-même une sous-catégorie.";
        }

        return null;
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

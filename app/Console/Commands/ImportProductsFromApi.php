<?php

namespace App\Console\Commands;

use App\Models\Categories;
use App\Models\Produits;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Importe des produits de démonstration depuis l'API publique DummyJSON
 * (https://dummyjson.com/products) et les insère dans la table produits,
 * avec catégorie et images téléchargées (servies ensuite via Glide).
 *
 * Exemples :
 *   php artisan products:import
 *   php artisan products:import --limit=50 --price-factor=1000
 *   php artisan products:import --fresh   (vide d'abord les produits importés)
 */
class ImportProductsFromApi extends Command
{
    protected $signature = 'products:import
        {--limit=30 : Nombre de produits à importer}
        {--price-factor=1000 : Multiplie le prix de l\'API (utile pour passer en FCFA)}
        {--with-images : Télécharge aussi les images (plus lent)}
        {--fresh : Supprime d\'abord les produits déjà importés (SKU API-*)}';

    protected $description = 'Importe des produits depuis l\'API DummyJSON vers la base';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $factor = (float) $this->option('price-factor');
        $withImages = (bool) $this->option('with-images');

        if ($this->option('fresh')) {
            $count = Produits::where('sku', 'like', 'API-%')->count();
            Produits::where('sku', 'like', 'API-%')->each(function ($p) {
                foreach ($p->photos as $photo) {
                    $photo->delete();
                }
                $p->delete();
            });
            $this->warn("🧹 {$count} produits importés précédemment supprimés.");
        }

        $this->info("📡 Récupération de {$limit} produits depuis DummyJSON...");

        $response = Http::timeout(30)->get('https://dummyjson.com/products', [
            'limit' => $limit,
        ]);

        if (! $response->successful()) {
            $this->error('❌ Échec de l\'appel API : HTTP ' . $response->status());
            return self::FAILURE;
        }

        $products = $response->json('products') ?? [];
        if (empty($products)) {
            $this->error('❌ Aucun produit reçu depuis l\'API.');
            return self::FAILURE;
        }

        $created = 0;
        $skipped = 0;
        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        foreach ($products as $item) {
            $sku = 'API-' . ($item['id'] ?? Str::random(6));

            // Idempotent : on ne réimporte pas un produit déjà présent
            if (Produits::where('sku', $sku)->exists()) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Catégorie (créée si absente)
            $categoryName = ucfirst(str_replace('-', ' ', $item['category'] ?? 'Divers'));
            $category = Categories::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'is_active' => true]
            );

            $price = round((float) ($item['price'] ?? 0) * $factor, 2);
            $discount = (float) ($item['discountPercentage'] ?? 0);
            $salePrice = $discount > 0 ? (int) round($price * (1 - $discount / 100)) : null;

            $produit = Produits::create([
                'name' => $item['title'] ?? 'Produit importé',
                'description' => $item['description'] ?? '',
                'price' => $price,
                'sale_price' => $salePrice,
                'stock' => (int) ($item['stock'] ?? 0),
                'sku' => $sku,
                'category_id' => $category->id,
                'is_active' => true,
            ]);

            // is_featured selon la note (colonne hors fillable -> forceFill)
            if (($item['rating'] ?? 0) >= 4.5) {
                $produit->forceFill(['is_featured' => true])->save();
            }

            // Images
            if ($withImages) {
                $this->downloadImages($produit, $item);
            }

            $created++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ {$created} produits créés, {$skipped} ignorés (déjà présents).");
        if (! $withImages) {
            $this->line('ℹ️  Astuce : ajoutez --with-images pour télécharger aussi les visuels.');
        }

        return self::SUCCESS;
    }

    /**
     * Télécharge les images du produit et crée les enregistrements photos
     * (même convention que les uploads admin : Produits/{id}/...).
     */
    protected function downloadImages(Produits $produit, array $item): void
    {
        $urls = [];
        if (! empty($item['thumbnail'])) {
            $urls[] = $item['thumbnail'];
        }
        foreach (array_slice($item['images'] ?? [], 0, 3) as $img) {
            $urls[] = $img;
        }
        $urls = array_unique($urls);

        $i = 0;
        foreach ($urls as $url) {
            try {
                $resp = Http::timeout(30)->get($url);
                if (! $resp->successful()) {
                    continue;
                }
                $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $path = 'Produits/' . $produit->id . '/img_' . ($i++) . '.' . $ext;
                Storage::disk('public')->put($path, $resp->body());
                $produit->photos()->create(['filename' => $path]);
            } catch (\Throwable $e) {
                // On ignore une image qui échoue, sans bloquer l'import
                continue;
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategorySeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        // Dossier de stockage des images
        $imagePath = 'public/categories';
        if (!Storage::exists($imagePath)) {
            Storage::makeDirectory($imagePath);
        }
        
        // Catégories parentes avec des images plus fiables
        $parentCategories = [
            [
                'name' => 'Électronique',
                'description' => 'Tout l\'électronique et les appareils électroniques',
                'image' => 'https://source.unsplash.com/random/800x600/?electronics',
            ],
            [
                'name' => 'Mode',
                'description' => 'Vêtements, chaussures et accessoires de mode',
                'image' => 'https://source.unsplash.com/random/800x600/?fashion',
            ],
            [
                'name' => 'Maison & Jardin',
                'description' => 'Meubles et décoration pour la maison et le jardin',
                'image' => 'https://source.unsplash.com/random/800x600/?home,garden',
            ],
        ];
        
        $parentIds = [];
        
        foreach ($parentCategories as $category) {
            // Télécharger l'image avec gestion des erreurs
            $imageUrl = $category['image'];
            unset($category['image']);
            
            $imageName = Str::slug($category['name']) . '.jpg';
            $imagePath = 'categories/' . $imageName;
            
            if (!Storage::exists('public/' . $imagePath)) {
                try {
                    $context = stream_context_create([
                        'http' => [
                            'method' => 'GET',
                            'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                        ]
                    ]);
                    
                    $contents = @file_get_contents($imageUrl, false, $context);
                    
                    if ($contents === false) {
                        throw new \Exception("Impossible de télécharger l'image: $imageUrl");
                    }
                    
                    Storage::put('public/' . $imagePath, $contents);
                    $this->command->info("Image téléchargée: $imageName");
                } catch (\Exception $e) {
                    $this->command->error("Erreur lors du téléchargement de l'image pour {$category['name']}: " . $e->getMessage());
                    // Utiliser une image par défaut si le téléchargement échoue
                    $defaultImagePath = 'categories/default.jpg';
                    if (!Storage::exists('public/' . $defaultImagePath)) {
                        // Créer une image par défaut si elle n'existe pas
                        $defaultImage = imagecreatetruecolor(800, 600);
                        $bgColor = imagecolorallocate($defaultImage, 200, 200, 200);
                        $textColor = imagecolorallocate($defaultImage, 100, 100, 100);
                        imagefill($defaultImage, 0, 0, $bgColor);
                        $text = 'Image non disponible';
                        $fontSize = 5;
                        $textWidth = imagefontwidth($fontSize) * strlen($text);
                        $x = (800 - $textWidth) / 2;
                        $y = 300;
                        imagestring($defaultImage, $fontSize, $x, $y, $text, $textColor);
                        ob_start();
                        imagejpeg($defaultImage);
                        $imageData = ob_get_clean();
                        Storage::put('public/' . $defaultImagePath, $imageData);
                        imagedestroy($defaultImage);
                    }
                    $imagePath = $defaultImagePath;
                }
            }
            
            $category['image'] = $imagePath;
            $category['slug'] = Str::slug($category['name']);
            $category['is_active'] = true;
            
            $created = Categories::create($category);
            $parentIds[] = $created->id;
        }
        
        // Sous-catégories
        $subCategories = [
            // Électronique
            [
                'name' => 'Téléphones portables',
                'description' => 'Smartphones et téléphones portables',
                'parent_id' => $parentIds[0],
                'image' => 'https://images.unsplash.com/photo-1598327105666-5b893731ff0e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1472&q=80',
            ],
            [
                'name' => 'Ordinateurs portables',
                'description' => 'Ordinateurs portables et accessoires',
                'parent_id' => $parentIds[0],
                'image' => 'https://images.unsplash.com/photo-1496181133205-80b8f089b10a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80',
            ],
            
            // Mode
            [
                'name' => 'Vêtements Homme',
                'description' => 'Vêtements pour hommes',
                'parent_id' => $parentIds[1],
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80',
            ],
            [
                'name' => 'Vêtements Femme',
                'description' => 'Vêtements pour femmes',
                'parent_id' => $parentIds[1],
                'image' => 'https://images.unsplash.com/photo-1490114538077-0a7f8cb49891?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80',
            ],
            
            // Maison & Jardin
            [
                'name' => 'Meubles de salon',
                'description' => 'Canapés, fauteuils et tables basses',
                'parent_id' => $parentIds[2],
                'image' => 'https://images.unsplash.com/photo-1555041463-a586c61ea9bc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80',
            ],
            [
                'name' => 'Jardinage',
                'description' => 'Plantes, outils et accessoires de jardinage',
                'parent_id' => $parentIds[2],
                'image' => 'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1632&q=80',
            ],
        ];
        
        foreach ($subCategories as $category) {
            // Télécharger l'image avec gestion des erreurs
            $imageUrl = $category['image'];
            unset($category['image']);
            
            $imageName = Str::slug($category['name']) . '.jpg';
            $imagePath = 'categories/' . $imageName;
            
            if (!Storage::exists('public/' . $imagePath)) {
                try {
                    $context = stream_context_create([
                        'http' => [
                            'method' => 'GET',
                            'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                        ]
                    ]);
                    
                    $contents = @file_get_contents($imageUrl, false, $context);
                    
                    if ($contents === false) {
                        throw new \Exception("Impossible de télécharger l'image: $imageUrl");
                    }
                    
                    Storage::put('public/' . $imagePath, $contents);
                    $this->command->info("Image téléchargée: $imageName");
                } catch (\Exception $e) {
                    $this->command->error("Erreur lors du téléchargement de l'image pour {$category['name']}: " . $e->getMessage());
                    // Utiliser une image par défaut si le téléchargement échoue
                    $defaultImagePath = 'categories/default.jpg';
                    if (!Storage::exists('public/' . $defaultImagePath)) {
                        // Créer une image par défaut si elle n'existe pas
                        $defaultImage = imagecreatetruecolor(800, 600);
                        $bgColor = imagecolorallocate($defaultImage, 200, 200, 200);
                        $textColor = imagecolorallocate($defaultImage, 100, 100, 100);
                        imagefill($defaultImage, 0, 0, $bgColor);
                        $text = 'Image non disponible';
                        $fontSize = 5;
                        $textWidth = imagefontwidth($fontSize) * strlen($text);
                        $x = (800 - $textWidth) / 2;
                        $y = 300;
                        imagestring($defaultImage, $fontSize, $x, $y, $text, $textColor);
                        ob_start();
                        imagejpeg($defaultImage);
                        $imageData = ob_get_clean();
                        Storage::put('public/' . $defaultImagePath, $imageData);
                        imagedestroy($defaultImage);
                    }
                    $imagePath = $defaultImagePath;
                }
            }
            
            $category['image'] = $imagePath;
            $category['slug'] = Str::slug($category['name']);
            $category['is_active'] = true;
            
            Categories::create($category);
        }
        
        $this->command->info('Catégories créées avec succès !');
    }
}

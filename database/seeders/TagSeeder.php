<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        $tags = [
            // Tags généraux
            'Nouveauté', 'Promo', 'Meilleure vente', 'Coup de cœur', 'Éco-responsable',
            'Fabriqué en France', 'Limité', 'Exclusivité web', 'Soldes', 'Black Friday',
            'Noël', 'Été', 'Hiver', 'Printemps', 'Automne',
            
            // Tags électronique
            'Écran OLED', 'Haut de gamme', 'Économique', 'Reconditionné', 'Garantie 2 ans',
            'Sans fil', 'Bluetooth', 'USB-C', 'Étanche', 'Anti-choc',
            
            // Tags mode
            'Tendance', 'Classique', 'Sport', 'Décontracté', 'Soirée',
            'Grandes tailles', 'Petites tailles', 'Maternité', 'Étudiant', 'Luxe',
            
            // Tags maison & jardin
            'Design', 'Moderne', 'Vintage', 'Industriel', 'Scandinave',
            'Écologique', 'Fait main', 'Sur mesure', 'Facile à monter', 'Rangement',
            
            // Tags promotionnels
            'Livraison gratuite', 'Retour gratuit', 'Paiement en 3x', 'Cadeau', 'Pack duo',
            'Offre spéciale', 'Destockage', 'Dernière pièce', 'Bon plan', 'Réduction',
        ];
        
        foreach ($tags as $tagName) {
            Tag::firstOrCreate(
                ['name' => $tagName],
                [
                    'slug' => Str::slug($tagName),
                    'description' => 'Produit avec le tag ' . $tagName,
                ]
            );
        }
        
        $this->command->info('Tags créés avec succès !');
    }
}

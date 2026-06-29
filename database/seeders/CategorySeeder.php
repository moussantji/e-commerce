<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        $parentCategories = [
            [
                'name' => 'Électronique',
                'description' => 'Smartphones, ordinateurs, audio, TV et accessoires électroniques.',
            ],
            [
                'name' => 'Mode',
                'description' => 'Vêtements, chaussures et accessoires pour homme, femme et enfant.',
            ],
            [
                'name' => 'Maison & Jardin',
                'description' => 'Mobilier, décoration, bricolage, jardinage et aménagement intérieur.',
            ],
            [
                'name' => 'Beauté & Santé',
                'description' => 'Cosmétiques, soins, parfums, bien-être et nutrition.',
            ],
            [
                'name' => 'Sport & Outdoor',
                'description' => 'Équipements de sport, fitness, camping et activités de plein air.',
            ],
            [
                'name' => 'Bébé & Enfants',
                'description' => 'Puériculture, jouets, vêtements et mobilier pour les enfants.',
            ],
            [
                'name' => 'Alimentation & Boissons',
                'description' => 'Épicerie, boissons, produits frais et spécialités gourmandes.',
            ],
            [
                'name' => 'Auto & Moto',
                'description' => 'Pièces détachées, accessoires et équipements pour voiture et moto.',
            ],
        ];

        $parentIds = [];

        foreach ($parentCategories as $category) {
            $category['slug'] = Str::slug($category['name']);
            $category['is_active'] = true;
            $category['image'] = null;

            $created = Categories::create($category);
            $parentIds[] = $created->id;
        }

        $subCategories = [
            // Électronique
            [
                'name' => 'Smartphones',
                'description' => 'Téléphones mobiles, accessoires et recharges.',
                'parent_id' => $parentIds[0],
            ],
            [
                'name' => 'Ordinateurs portables',
                'description' => 'PC portables, ultrabooks et accessoires.',
                'parent_id' => $parentIds[0],
            ],
            [
                'name' => 'Audio & Hi-Fi',
                'description' => 'Casques, enceintes, barres de son et systèmes audio.',
                'parent_id' => $parentIds[0],
            ],
            [
                'name' => 'TV & Home cinéma',
                'description' => 'Téléviseurs, projecteurs et appareils de home cinéma.',
                'parent_id' => $parentIds[0],
            ],

            // Mode
            [
                'name' => 'Vêtements Homme',
                'description' => 'Pantalons, chemises, vestes et tenues masculines.',
                'parent_id' => $parentIds[1],
            ],
            [
                'name' => 'Vêtements Femme',
                'description' => 'Robe, jupes, jeans et tenues féminines.',
                'parent_id' => $parentIds[1],
            ],
            [
                'name' => 'Chaussures',
                'description' => 'Sneakers, bottes, sandales et chaussures de ville.',
                'parent_id' => $parentIds[1],
            ],
            [
                'name' => 'Accessoires',
                'description' => 'Sacs, bijoux, montres, ceintures et lunettes.',
                'parent_id' => $parentIds[1],
            ],

            // Maison & Jardin
            [
                'name' => 'Meubles',
                'description' => 'Canapés, tables, chaises, lits et rangements.',
                'parent_id' => $parentIds[2],
            ],
            [
                'name' => 'Décoration',
                'description' => 'Luminaires, textiles, tableaux et objets décoratifs.',
                'parent_id' => $parentIds[2],
            ],
            [
                'name' => 'Bricolage',
                'description' => 'Outils, matériaux et équipements de bricolage.',
                'parent_id' => $parentIds[2],
            ],
            [
                'name' => 'Jardinage',
                'description' => 'Plantes, mobilier de jardin et accessoires extérieurs.',
                'parent_id' => $parentIds[2],
            ],

            // Beauté & Santé
            [
                'name' => 'Soins du visage',
                'description' => 'Crèmes, sérums et routines de soin.',
                'parent_id' => $parentIds[3],
            ],
            [
                'name' => 'Maquillage',
                'description' => 'Rouges à lèvres, fonds de teint, palettes et pinceaux.',
                'parent_id' => $parentIds[3],
            ],
            [
                'name' => 'Parfums',
                'description' => 'Parfums pour femmes et hommes.',
                'parent_id' => $parentIds[3],
            ],

            // Sport & Outdoor
            [
                'name' => 'Fitness & Musculation',
                'description' => 'Équipements de gym, haltères et accessoires fitness.',
                'parent_id' => $parentIds[4],
            ],
            [
                'name' => 'Camping & Outdoor',
                'description' => 'Tentes, sacs de couchage et matériel de randonnée.',
                'parent_id' => $parentIds[4],
            ],
            [
                'name' => 'Cyclisme',
                'description' => 'Vélos, casques et accessoires de cyclisme.',
                'parent_id' => $parentIds[4],
            ],

            // Bébé & Enfants
            [
                'name' => 'Poussettes & Sièges auto',
                'description' => 'Poussettes, cosy et sièges auto pour bébé.',
                'parent_id' => $parentIds[5],
            ],
            [
                'name' => 'Jouets',
                'description' => 'Jouets, jeux éducatifs et peluches.',
                'parent_id' => $parentIds[5],
            ],
            [
                'name' => 'Vêtements bébé',
                'description' => 'Bodys, pyjamas, ensembles et accessoires bébé.',
                'parent_id' => $parentIds[5],
            ],

            // Alimentation & Boissons
            [
                'name' => 'Épicerie salée',
                'description' => 'Conserves, pâtes, riz et produits du quotidien.',
                'parent_id' => $parentIds[6],
            ],
            [
                'name' => 'Boissons',
                'description' => 'Eaux, jus, sodas et boissons chaudes.',
                'parent_id' => $parentIds[6],
            ],
            [
                'name' => 'Produits frais',
                'description' => 'Fruits, légumes, viandes et produits laitiers.',
                'parent_id' => $parentIds[6],
            ],

            // Auto & Moto
            [
                'name' => 'Pièces détachées',
                'description' => 'Pièces de rechange pour voiture et moto.',
                'parent_id' => $parentIds[7],
            ],
            [
                'name' => 'Entretien auto',
                'description' => 'Huiles, batteries, produits d’entretien et accessoires.',
                'parent_id' => $parentIds[7],
            ],
            [
                'name' => 'Équipements moto',
                'description' => 'Casques, gants, blousons et accessoires moto.',
                'parent_id' => $parentIds[7],
            ],
        ];

        foreach ($subCategories as $category) {
            $category['slug'] = Str::slug($category['name']);
            $category['is_active'] = true;
            $category['image'] = null;

            Categories::create($category);
        }

        $this->command->info('Catégories créées avec succès !');
    }
}

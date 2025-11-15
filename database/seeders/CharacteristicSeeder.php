<?php

namespace Database\Seeders;

use App\Models\Caracteristiques;
use Illuminate\Database\Seeder;

class CharacteristicSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        $characteristics = [
            // Caractéristiques générales
            [
                'name' => 'Couleur',
                'type' => 'couleur',
                'unite' => null,
                'values' => ['Noir', 'Blanc', 'Rouge', 'Bleu', 'Vert', 'Jaune', 'Gris', 'Rose', 'Or', 'Argent'],
            ],
            [
                'name' => 'Taille',
                'type' => 'taille',
                'unite' => null,
                'values' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'],
            ],
            [
                'name' => 'Poids',
                'type' => 'poids',
                'unite' => 'kg',
            ],
            [
                'name' => 'Marque',
                'type' => 'marque',
                'unite' => null,
                'values' => ['Apple', 'Samsung', 'Sony', 'LG', 'HP', 'Dell', 'Nike', 'Adidas', 'Zara', 'H&M'],
            ],
            
            // Caractéristiques électroniques
            [
                'name' => 'Capacité de stockage',
                'type' => 'stockage',
                'unite' => 'Go',
                'values' => ['32', '64', '128', '256', '512', '1024', '2048'],
            ],
            [
                'name' => 'Mémoire RAM',
                'type' => 'ram',
                'unite' => 'Go',
                'values' => ['2', '4', '6', '8', '12', '16', '32', '64'],
            ],
            [
                'name' => 'Taille d\'écran',
                'type' => 'ecran',
                'unite' => 'pouces',
                'values' => ['4.7"', '5.0"', '5.5"', '6.0"', '6.5"', '13"', '14"', '15.6"', '17"', '24"', '27"', '32"'],
            ],
            [
                'name' => 'Résolution d\'écran',
                'type' => 'resolution',
                'unite' => null,
                'values' => ['HD (1280x720)', 'Full HD (1920x1080)', '2K (2560x1440)', '4K (3840x2160)', 'Retina'],
            ],
            
            // Caractéristiques vêtements
            [
                'name' => 'Matière',
                'type' => 'matiere',
                'unite' => null,
                'values' => ['Coton', 'Polyester', 'Laine', 'Soie', 'Lin', 'Cachemire', 'Denim', 'Cuir', 'Daim'],
            ],
            [
                'name' => 'Sexe',
                'type' => 'genre',
                'unite' => null,
                'values' => ['Homme', 'Femme', 'Unisexe', 'Enfant'],
            ],
            
            // Caractéristiques meubles
            [
                'name' => 'Matériau',
                'type' => 'materiau',
                'unite' => null,
                'values' => ['Bois massif', 'Mélaminé', 'Verre', 'Métal', 'Plastique', 'Cuir', 'Tissu'],
            ],
            [
                'name' => 'Couleur du tissu',
                'type' => 'couleur_tissu',
                'unite' => null,
                'values' => ['Beige', 'Gris', 'Noir', 'Bleu', 'Vert', 'Rouge', 'Jaune', 'Multicolore'],
            ],
        ];
        
        foreach ($characteristics as $characteristic) {
            $values = $characteristic['values'] ?? null;
            unset($characteristic['values']);
            
            // Créer la caractéristique si elle n'existe pas déjà
            $created = Caracteristiques::firstOrCreate(
                ['name' => $characteristic['name']],
                $characteristic
            );
            
            // Si des valeurs prédéfinies sont fournies, les enregistrer dans la table pivot
            if ($values && is_array($values)) {
                // Vérifier si la relation existe avant de l'utiliser
                if (method_exists($created, 'predefinedValues')) {
                    foreach ($values as $value) {
                        $created->predefinedValues()->firstOrCreate(['value' => $value]);
                    }
                }
            }
        }
        
        $this->command->info('Caractéristiques créées avec succès !');
    }
}

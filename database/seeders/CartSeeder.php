<?php

namespace Database\Seeders;

use App\Models\Paniers;
use App\Models\Produits;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        try {
            // Activer le logging des requêtes SQL
            \DB::listen(function($query) {
                \Log::info('SQL: ' . $query->sql . ' - Bindings: ' . json_encode($query->bindings));
            });
            
            $this->command->info('Début du seeder de paniers...');
            // Désactiver les contraintes de clé étrangère temporairement
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('panier_produit')->truncate();
            DB::table('paniers')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Récupérer tous les utilisateurs (sauf les administrateurs)
            $users = User::where('role', '!=', 'admin')
                        ->get();
            
            $this->command->info('Nombre d\'utilisateurs trouvés: ' . $users->count());
            
            if ($users->isEmpty()) {
                $this->command->warn('Aucun utilisateur trouvé. Veuillez exécuter le UserSeeder d\'abord.');
                return;
            }
            
            // Récupérer tous les produits actifs
            $products = Produits::where('is_active', true)->get();
            
            if ($products->isEmpty()) {
                $this->command->warn('Aucun produit actif trouvé. Veuillez exécuter le ProductSeeder d\'abord.');
                return;
            }

            $this->command->info("Création des paniers pour " . $users->count() . " utilisateurs...");

            foreach ($users as $user) {
                // 70% de chance qu'un utilisateur ait un panier
                // 100% de chance qu'un utilisateur ait un panier pour le test
                if (true) {
                    $cart = Paniers::create([
                        'user_id' => $user->id,
                        'status' => 'actif',
                        'date_maj' => now(),
                        'created_at' => now()->subDays(rand(0, 30)),
                        'updated_at' => now(),
                    ]);

                    // Ajouter entre 1 et 5 produits au panier
                    $cartProducts = $products->random(rand(1, min(5, $products->count())));
                    
                    foreach ($cartProducts as $product) {
                        $quantity = (string) rand(1, 5); // Convertir en string pour correspondre au type de la colonne
                        $unitPrice = (string) $product->price; // Convertir en string pour correspondre au type de la colonne
                        $totalLigne = (string) ($quantity * $unitPrice); // Convertir en string pour correspondre au type de la colonne
                        
                        try {
                            DB::table('panier_produit')->insert([
                                'paniers_id' => $cart->id,
                                'produits_id' => $product->id,
                                'quantite' => $quantity,
                                'prix_unitaire' => $unitPrice,
                                'total_ligne' => $totalLigne,
                                // Pas de timestamps car ils ne sont pas dans la migration
                            ]);
                            
                            $this->command->info(sprintf(
                                '  - Ajout du produit %s (quantité: %s, prix: %s FCFA)',
                                $product->name,
                                $quantity,
                                number_format($unitPrice, 0, ',', ' ')
                            ));
                            
                        } catch (\Exception $e) {
                            $this->command->error('Erreur lors de l\'ajout du produit au panier: ' . $e->getMessage());
                        }
                    }
                    
                    $this->command->info("Panier créé pour l'utilisateur: " . $user->name);
                }
            }
            
            $this->command->info('Seeders des paniers terminé avec succès !');
            
        } catch (\Exception $e) {
            $this->command->error('Erreur lors de l\'exécution du seeder: ' . $e->getMessage());
            $this->command->error('Fichier: ' . $e->getFile());
            $this->command->error('Ligne: ' . $e->getLine());
            $this->command->error($e->getTraceAsString());
        }
    }
}

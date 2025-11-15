<?php

namespace Database\Seeders;

use App\Models\Commandes;
use App\Models\Produits;
use App\Models\User;
use App\Models\Paiements;
use App\Models\Livraison;
use App\Models\PromoCode;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        
        // Récupérer les utilisateurs clients
        $users = User::where('role', 'customer')->get();
        if ($users->isEmpty()) {
            $this->command->warn('Aucun utilisateur client trouvé. Créez d\'abord des utilisateurs.');
            return;
        }
        
        // Récupérer les méthodes de paiement
        $paymentMethods = Paiements::where('is_active', true)->get();
        if ($paymentMethods->isEmpty()) {
            $this->command->warn('Aucune méthode de paiement active trouvée. Créez d\'abord des méthodes de paiement.');
            return;
        }
        
        // Récupérer les méthodes de livraison
        $shippingMethods = Livraison::where('is_active', true)->get();
        if ($shippingMethods->isEmpty()) {
            $this->command->warn('Aucune méthode de livraison active trouvée. Créez d\'abord des méthodes de livraison.');
            return;
        }
        
        // Récupérer les produits
        $products = Produits::where('stock', '>', 0)->get();
        if ($products->isEmpty()) {
            $this->command->warn('Aucun produit en stock trouvé. Créez d\'abord des produits avec du stock.');
            return;
        }
        
        // Récupérer les codes promo actifs
        $promoCodes = PromoCode::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->get();
        
        // Créer des commandes pour chaque utilisateur
        foreach ($users as $user) {
            // Entre 5 et 15 commandes par utilisateur pour avoir plus de données
            $orderCount = $faker->numberBetween(5, 15);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $paymentMethod = $paymentMethods->random();
                $shippingMethod = $shippingMethods->random();
                
                // Générer une date aléatoire pour la commande (dans les 6 derniers mois)
                $orderDate = $faker->dateTimeBetween('-6 months', 'now');
                
                // Statut de la commande avec distribution plus réaliste
                $statuses = ['en_attente', 'traitement', 'expedie', 'livre', 'annule'];
                // Probabilités ajustées pour avoir plus de commandes livrées
                $statusWeights = [
                    'en_attente' => 5,
                    'traitement' => 20,
                    'expedie' => 25,
                    'livre' => 45,
                    'annule' => 5
                ];
                $status = $faker->randomElement(
                    array_merge(
                        array_fill(0, $statusWeights['en_attente'], 'en_attente'),
                        array_fill(0, $statusWeights['traitement'], 'traitement'),
                        array_fill(0, $statusWeights['expedie'], 'expedie'),
                        array_fill(0, $statusWeights['livre'], 'livre'),
                        array_fill(0, $statusWeights['annule'], 'annule')
                    )
                );
                
                // Créer la commande
                $orderData = [
                    'user_id' => $user->id,
                    'paiement_id' => $paymentMethod->id,
                    'livraison_id' => $shippingMethod->id,
                    'numero_commande' => 'CMD-' . strtoupper(uniqid()),
                    'statut' => $status,
                    'created_at' => $orderDate,
                    'sous_total' => 0, // Sera mis à jour plus tard
                    'frais_livraison' => $shippingMethod->price,
                    'remise' => 0, // Pas de remise par défaut
                    'promo_discount' => 0,
                    'total' => 0, // Sera mis à jour plus tard
                    'adresse_facturation' => json_encode([
                        'prenom' => $faker->firstName,
                        'nom' => $faker->lastName,
                        'societe' => $faker->boolean(20) ? $faker->company : null,
                        'adresse' => $faker->streetAddress,
                        'complement' => $faker->boolean(30) ? $faker->secondaryAddress : null,
                        'code_postal' => $faker->postcode,
                        'ville' => $faker->city,
                        'pays' => 'France',
                        'telephone' => $faker->phoneNumber,
                        'email' => $faker->email,
                    ]),
                    'adresse_livraison' => json_encode([
                        'prenom' => $faker->firstName,
                        'nom' => $faker->lastName,
                        'societe' => $faker->boolean(20) ? $faker->company : null,
                        'adresse' => $faker->streetAddress,
                        'complement' => $faker->boolean(30) ? $faker->secondaryAddress : null,
                        'code_postal' => $faker->postcode,
                        'ville' => $faker->city,
                        'pays' => 'France',
                        'telephone' => $faker->phoneNumber,
                    ]),
                    'notes' => $faker->optional(0.3)->sentence,
                ];
                
                // Ajouter des produits à la commande
                $orderItems = [];
                $subTotal = 0;
                $productCount = $faker->numberBetween(1, 5);
                $selectedProducts = $products->random($productCount);
                
                foreach ($selectedProducts as $product) {
                    $quantity = $faker->numberBetween(1, min(3, $product->stock));
                    $price = $product->price;
                    $total = $price * $quantity;
                    
                    $orderItems[$product->id] = [
                        'quantite' => $quantity,
                        'prix_unitaire' => $price,
                        'total' => $total,
                    ];
                    
                    $subTotal += $total;
                    
                    // Mettre à jour le stock du produit
                    $product->decrement('stock', $quantity);
                }
                
                // Calculer la remise (10% de chance d'avoir une remise)
                $discount = 0;
                if ($faker->boolean(10)) {
                    $discount = $faker->randomFloat(2, 5, 20); // Remise entre 5% et 20%
                    $discountAmount = ($subTotal * $discount) / 100;
                    $subTotal -= $discountAmount;
                }
                
                // Appliquer un code promo aléatoire (30% de chance)
                $promoCode = null;
                $promoDiscount = 0;
                
                if ($promoCodes->isNotEmpty() && $faker->boolean(30)) {
                    $promoCode = $faker->randomElement($promoCodes);
                    $promoDiscount = $promoCode->calculateDiscount($subTotal);
                    
                    // Mettre à jour le compteur d'utilisation
                    $promoCode->increment('usage_count');
                }
                
                // Calculer le total
                $total = max(0, $subTotal + $shippingMethod->price - $promoDiscount);
                
                // Mettre à jour la commande avec les totaux
                $orderData['sous_total'] = $subTotal;
                $orderData['remise'] = $discount;
                $orderData['promo_discount'] = $promoDiscount;
                $orderData['total'] = $total;
                
                // Ajouter le code promo s'il y en a un
                if ($promoCode) {
                    $orderData['promo_code_id'] = $promoCode->id;
                    
                    // Ajouter une note sur le code promo utilisé
                    $orderData['notes'] = ($orderData['notes'] ?? '') . 
                        "\n\nCode promo appliqué: {$promoCode->code} " .
                        ($promoCode->type === 'percentage' ? 
                            "({$promoCode->value}% de réduction)" : 
                            "({$promoCode->value}€ de réduction)");
                }
                
                $order = Commandes::create($orderData);
                
                // Préparer les données pour l'insertion dans la table de liaison
                $orderItemsData = [];
                foreach ($orderItems as $productId => $item) {
                    $orderItemsData[$productId] = [
                        'quantite' => $item['quantite'],
                        'prix_unitaire' => $item['prix_unitaire'],
                        'total' => $item['total'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                // Attacher les produits à la commande avec les bonnes colonnes
                $order->produits()->attach($orderItemsData);
                
                // Mettre à jour les dates de statut en fonction du statut
                $statusDates = [
                    'en_attente' => 'date_en_attente',
                    'traitement' => 'date_traitement',
                    'expedie' => 'date_expedition',
                    'livre' => 'date_livraison',
                    'annule' => 'date_annulation',
                ];
                
                // Mettre à jour les dates de statut
                $updates = [];
                foreach ($statusDates as $statusKey => $dateField) {
                    if ($status === $statusKey || array_search($statusKey, $statuses) <= array_search($status, $statuses)) {
                        $dateValue = $orderDate;
                        if ($statusKey === 'traitement') {
                            $dateValue = (clone $orderDate)->modify('+1 day');
                        } elseif ($statusKey === 'expedie') {
                            $dateValue = (clone $orderDate)->modify('+2 days');
                        } elseif ($statusKey === 'livre') {
                            $dateValue = (clone $orderDate)->modify('+3 days');
                        }

                        $updates[$dateField] = $dateValue;
                    }
                }
                
                if (!empty($updates)) {
                    $order->update($updates);
                }
            }
        }
        
        $this->command->info('Commandes créées avec succès !');
    }
    
    /**
     * Génère une adresse factice.
     */
    protected function generateAddress($faker)
    {
        return json_encode([
            'prenom' => $faker->firstName,
            'nom' => $faker->lastName,
            'societe' => $faker->boolean(20) ? $faker->company : null,
            'adresse' => $faker->streetAddress,
            'complement' => $faker->boolean(30) ? $faker->secondaryAddress : null,
            'code_postal' => $faker->postcode,
            'ville' => $faker->city,
            'pays' => 'France',
            'telephone' => $faker->phoneNumber,
            'email' => $faker->email,
        ]);
    }
}

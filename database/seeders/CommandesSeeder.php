<?php

namespace Database\Seeders;

use App\Models\Commandes;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CommandesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un utilisateur existant ou en créer un si nécessaire
        $user = User::first();
        
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password')
            ]);
        }

        $statuses = ['en_attente', 'traitement', 'expedie', 'livre', 'annule'];
        $now = now();
        
        // Créer des commandes pour les 6 derniers mois
        for ($month = 0; $month < 6; $month++) {
            $startDate = $now->copy()->subMonths(5 - $month)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            
            // Nombre de commandes pour ce mois (entre 10 et 30)
            $orderCount = rand(10, 30);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $orderDate = $this->randomDateInRange($startDate, $endDate);
                $status = $statuses[array_rand($statuses)];
                $total = rand(5000, 50000) / 100; // Montant entre 50 et 500 FCFA
                
                // Générer un numéro de commande unique avec un timestamp et un identifiant aléatoire
                $microtime = microtime(true);
                $randomString = substr(str_replace(['.', '0'], '', (string)$microtime), 0, 6);
                $orderNumber = 'CMD' . $orderDate->format('Ymd') . strtoupper($randomString) . $i;
                
                $order = Commandes::create([
                    'user_id' => $user->id,
                    'paiement_id' => 1, // Utiliser un ID de paiement existant
                    'livraison_id' => 1, // Utiliser un ID de livraison existant
                    'numero_commande' => $orderNumber,
                    'statut' => $status,
                    'sous_total' => $total * 0.9, // 90% du total
                    'frais_livraison' => $total * 0.1, // 10% du total
                    'remise' => 0,
                    'total' => $total,
                    'adresse_facturation' => [
                        'nom' => $user->name,
                        'adresse' => '123 Rue des Exemples',
                        'ville' => 'Abidjan',
                        'pays' => 'Côte d\'Ivoire',
                        'telephone' => '+2250102030405'
                    ],
                    'adresse_livraison' => [
                        'nom' => $user->name,
                        'adresse' => '123 Rue des Exemples',
                        'ville' => 'Abidjan',
                        'pays' => 'Côte d\'Ivoire',
                        'telephone' => '+2250102030405'
                    ],
                    'notes' => 'Commande de test générée automatiquement',
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                // Mettre à jour les dates en fonction du statut
                $dates = [
                    'date_en_attente' => $orderDate,
                    'updated_at' => $orderDate,
                ];

                if (in_array($status, ['traitement', 'expedie', 'livre', 'annule'])) {
                    $dates['date_traitement'] = $orderDate->copy()->addHours(rand(1, 24));
                }
                
                if (in_array($status, ['expedie', 'livre'])) {
                    $dates['date_expedition'] = $orderDate->copy()->addDays(rand(1, 3));
                }
                
                if ($status === 'livre') {
                    $dates['date_livraison'] = $orderDate->copy()->addDays(rand(3, 7));
                }
                
                if ($status === 'annule') {
                    $dates['date_annulation'] = $orderDate->copy()->addHours(rand(1, 48));
                }

                $order->update($dates);
            }
        }
    }

    /**
     * Génère une date aléatoire dans une plage donnée
     */
    private function randomDateInRange($startDate, $endDate)
    {
        $min = strtotime($startDate->format('Y-m-d H:i:s'));
        $max = strtotime($endDate->format('Y-m-d H:i:s'));
        $randomDate = rand($min, $max);
        return Carbon::createFromTimestamp($randomDate);
    }
}

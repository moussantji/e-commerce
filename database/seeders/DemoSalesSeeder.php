<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Génère des commandes "réussies" (statuts livre / expedie) réparties sur le
 * mois en cours et le mois précédent, afin d'alimenter le graphe des ventes
 * du tableau de bord admin.
 *
 * Lancer :   php artisan db:seed --class=DemoSalesSeeder
 * Nettoyer : les commandes créées ont un numéro préfixé par "DEMO-".
 */
class DemoSalesSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent : on repart à zéro pour les données de démo
        DB::table('commandes')->where('numero_commande', 'like', 'DEMO-%')->delete();

        // 1) Un utilisateur (réutilise un existant, sinon en crée un)
        $userId = DB::table('users')->value('id');
        if (! $userId) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Client Démo',
                'email' => 'demo.client@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2) Un mode de paiement (réutilise sinon crée le minimum requis)
        $paiementId = DB::table('paiements')->value('id');
        if (! $paiementId) {
            $paiementId = DB::table('paiements')->insertGetId([
                'method_name' => 'Paiement Démo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3) Un mode de livraison (réutilise sinon crée le minimum requis)
        $livraisonId = DB::table('livraisons')->value('id');
        if (! $livraisonId) {
            $livraisonId = DB::table('livraisons')->insertGetId([
                'method_name' => 'Livraison Démo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $adresse = json_encode([
            'nom' => 'Client Démo',
            'rue' => '123 Avenue de la Démo',
            'ville' => 'Abidjan',
            'pays' => 'Côte d\'Ivoire',
        ]);

        $statuts = ['livre', 'expedie']; // comptés comme "réussi" par le dashboard
        $rows = [];
        $compteur = 0;

        // Mois précédent (entier) + mois en cours (jusqu'à aujourd'hui)
        foreach ([1, 0] as $monthsBack) {
            $debut = Carbon::now()->subMonths($monthsBack)->startOfMonth();
            $fin = $monthsBack === 0
                ? Carbon::now()
                : Carbon::now()->subMonths($monthsBack)->endOfMonth();

            for ($jour = $debut->copy(); $jour->lte($fin); $jour->addDay()) {
                // ~65 % des jours ont des ventes, pour une courbe variée
                if (random_int(1, 100) > 65) {
                    continue;
                }

                $nbCommandes = random_int(1, 3);
                for ($i = 0; $i < $nbCommandes; $i++) {
                    $total = random_int(5000, 150000);
                    $statut = $statuts[array_rand($statuts)];
                    $date = $jour->copy()->setTime(random_int(8, 20), random_int(0, 59));

                    $rows[] = [
                        'user_id' => $userId,
                        'paiement_id' => $paiementId,
                        'livraison_id' => $livraisonId,
                        'numero_commande' => 'DEMO-' . $date->format('Ymd') . '-' . str_pad(++$compteur, 4, '0', STR_PAD_LEFT),
                        'statut' => $statut,
                        'sous_total' => $total - 2000,
                        'frais_livraison' => 2000,
                        'remise' => 0,
                        'total' => $total,
                        'adresse_facturation' => $adresse,
                        'adresse_livraison' => $adresse,
                        'date_expedition' => $date,
                        'date_livraison' => $statut === 'livre' ? $date : null,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ];
                }
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('commandes')->insert($chunk);
        }

        $this->command->info("✅ {$compteur} commandes de démo créées (statuts livre/expedie) sur le mois en cours et le précédent.");
        $this->command->info('ℹ️  Pour les supprimer : php artisan tinker --execute="DB::table(\'commandes\')->where(\'numero_commande\',\'like\',\'DEMO-%\')->delete();"');
    }
}

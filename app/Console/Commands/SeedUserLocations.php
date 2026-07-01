<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Attribue des positions de démonstration aux utilisateurs sans coordonnées,
 * afin de visualiser la carte du tableau de bord immédiatement.
 *
 * En production, les positions réelles sont enregistrées automatiquement à la
 * connexion (géolocalisation par IP).
 *
 *   php artisan users:demo-locations
 */
class SeedUserLocations extends Command
{
    protected $signature = 'users:demo-locations {--all : Réattribue une position à TOUS les utilisateurs}';

    protected $description = 'Attribue des positions de démo aux utilisateurs (pour la carte du dashboard)';

    public function handle(): int
    {
        $cities = [
            ['Abidjan', "Côte d'Ivoire", 5.3599, -4.0083],
            ['Yamoussoukro', "Côte d'Ivoire", 6.8276, -5.2893],
            ['Paris', 'France', 48.8566, 2.3522],
            ['Dakar', 'Sénégal', 14.7167, -17.4677],
            ['Lagos', 'Nigeria', 6.5244, 3.3792],
            ['Casablanca', 'Maroc', 33.5731, -7.5898],
            ['Montréal', 'Canada', 45.5019, -73.5674],
            ['Bruxelles', 'Belgique', 50.8503, 4.3517],
            ['Accra', 'Ghana', 5.6037, -0.1870],
            ['Douala', 'Cameroun', 4.0511, 9.7679],
        ];

        $query = $this->option('all')
            ? User::query()
            : User::where(function ($q) {
                $q->whereNull('latitude')->orWhereNull('longitude');
            });

        $users = $query->get();
        if ($users->isEmpty()) {
            $this->info('Aucun utilisateur à mettre à jour.');
            return self::SUCCESS;
        }

        foreach ($users as $user) {
            $c = $cities[array_rand($cities)];
            $user->forceFill([
                'ville' => $c[0],
                'pays' => $c[1],
                'latitude' => $c[2] + mt_rand(-60, 60) / 1000,   // léger décalage
                'longitude' => $c[3] + mt_rand(-60, 60) / 1000,
            ])->save();
        }

        $this->info("✅ {$users->count()} utilisateur(s) positionné(s) sur la carte.");
        return self::SUCCESS;
    }
}

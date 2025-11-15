<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        
        $promoCodes = [
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10,
                'starts_at' => $now->copy()->subDays(30),
                'expires_at' => $now->copy()->addMonths(3),
                'usage_limit' => 100,
                'is_active' => true,
            ],
            [
                'code' => 'SUMMER25',
                'type' => 'percentage',
                'value' => 25,
                'starts_at' => $now->copy()->subDays(15),
                'expires_at' => $now->copy()->addMonths(2),
                'usage_limit' => 50,
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'fixed',
                'value' => 10.00, // 10€ de réduction sur les frais de port
                'starts_at' => $now->copy()->subDays(7),
                'expires_at' => $now->copy()->addMonth(),
                'usage_limit' => 200,
                'is_active' => true,
            ],
            [
                'code' => 'FLASH50',
                'type' => 'percentage',
                'value' => 50,
                'starts_at' => $now->copy()->subDay(),
                'expires_at' => $now->copy()->addDays(2),
                'usage_limit' => 20,
                'is_active' => true,
            ],
            [
                'code' => 'LOYALTY15',
                'type' => 'percentage',
                'value' => 15,
                'starts_at' => $now->copy()->subMonth(),
                'expires_at' => $now->copy()->addYears(10), // 10 ans pour simuler "pas d'expiration"
                'usage_limit' => 999999, // Très grand nombre pour simuler "pas de limite"
                'is_active' => true,
            ],
        ];

        foreach ($promoCodes as $promoCode) {
            PromoCode::firstOrCreate(
                ['code' => $promoCode['code']],
                $promoCode
            );
        }

        $this->command->info('Codes promo créés avec succès !');
    }
}

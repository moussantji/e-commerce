<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('banners')->insert([
            [
                'title1' => 'Promo Hiver 2026',
                'title2' => 'Jusqu\'à -50%',
                'percentage' => 50.00,
                'button_link' => '/produits?category=electronique',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title1' => 'Nouveautés E-commerce',
                'title2' => 'Découvrez nos offres',
                'percentage' => 25.50,
                'button_link' => '/produits?category=electronique',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title1' => 'Soldes Flash',
                'title2' => 'Économisez 75%',
                'percentage' => 75.00,
                'button_link' => '/produits?tag=promo',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

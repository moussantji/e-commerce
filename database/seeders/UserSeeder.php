<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'prenom' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Customer',
                'prenom' => 'Customer',
                'email' => 'customer@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($users as $userData) {
            // Vérifier si l'utilisateur existe déjà
            $user = User::where('email', $userData['email'])->first();
            
            if (!$user) {
                // Créer l'utilisateur s'il n'existe pas
                User::create($userData);
                $this->command->info("Utilisateur créé : " . $userData['email']);
            } else {
                // Mettre à jour l'utilisateur s'il existe déjà
                $user->update($userData);
                $this->command->info("Utilisateur mis à jour : " . $userData['email']);
            }
        }

        $this->command->info('Users seeded successfully!');
    }
}

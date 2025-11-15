<?php

namespace Database\Seeders;

use App\Models\Paiements;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        // Dossier de stockage des logos
        $logoPath = 'public/payment-methods';
        if (!Storage::exists($logoPath)) {
            Storage::makeDirectory($logoPath);
        }
        
        // Copier les logos de démonstration
        $logos = [
            'visa-mastercard.png' => 'https://raw.githubusercontent.com/fawazahmed0/payment-logos/ec37dacd0c4a1d0a2a1f7b8b8b8b8b8b8b8b8b8b8/src/logos/visa.svg',
            'paypal.png' => 'https://raw.githubusercontent.com/fawazahmed0/payment-logos/ec37dacd0c4a1d0a2a1f7b8b8b8b8b8b8b8b8b8b8/src/logos/paypal.svg',
            'bank-transfer.png' => 'https://raw.githubusercontent.com/fawazahmed0/payment-logos/ec37dacd0c4a1d0a2a1f7b8b8b8b8b8b8b8b8b8b8/src/logos/bank.svg',
            'cash-on-delivery.png' => 'https://raw.githubusercontent.com/fawazahmed0/payment-logos/ec37dacd0c4a1d0a2a1f7b8b8b8b8b8b8b8b8b8b8/src/logos/cash.svg',
        ];
        
        foreach ($logos as $filename => $url) {
            $destination = $logoPath . '/' . $filename;
            if (!Storage::exists($destination)) {
                try {
                    $contents = @file_get_contents($url);
                    if ($contents === false) {
                        $this->command->warn("Impossible de télécharger l'image: $url");
                        continue;
                    }
                    Storage::put($destination, $contents);
                    $this->command->info("Image téléchargée: $filename");
                } catch (\Exception $e) {
                    $this->command->error("Erreur lors du téléchargement de $url: " . $e->getMessage());
                }
            }
        }
        
        // Méthodes de paiement par défaut en FCFA
        $methods = [
            [
                'method_name' => 'Carte de crédit',
                'provider_name' => 'Stripe',
                'description' => 'Paiement sécurisé par carte bancaire (Visa, Mastercard, etc.)',
                'fee' => 150, // ~0.23€
                'fee_percentage' => 2.9,
                'logo' => 'payment-methods/visa-mastercard.png',
                'is_active' => true,
                'sort_order' => 1,
                'config' => json_encode([
                    'public_key' => 'pk_test_'.Str::random(48),
                    'secret_key' => 'sk_test_'.Str::random(48),
                    'webhook_secret' => 'whsec_'.Str::random(32),
                    'test_mode' => true,
                ]),
            ],
            [
                'method_name' => 'Orange Money',
                'provider_name' => 'Orange Money',
                'description' => 'Paiement mobile avec Orange Money',
                'fee' => 50, // ~0.08€
                'fee_percentage' => 1.5,
                'logo' => 'payment-methods/orange-money.png',
                'is_active' => true,
                'sort_order' => 2,
                'config' => json_encode([
                    'merchant_code' => 'CI'.rand(100000, 999999),
                    'test_mode' => true,
                ]),
            ],
            [
                'method_name' => 'MTN Mobile Money',
                'provider_name' => 'MTN Mobile Money',
                'description' => 'Paiement mobile avec MTN Mobile Money',
                'fee' => 50, // ~0.08€
                'fee_percentage' => 1.5,
                'logo' => 'payment-methods/mtn-money.png',
                'is_active' => true,
                'sort_order' => 3,
                'config' => json_encode([
                    'merchant_code' => 'CI'.rand(100000, 999999),
                    'test_mode' => true,
                ]),
            ],
            [
                'method_name' => 'Virement bancaire',
                'provider_name' => 'Banque',
                'description' => 'Paiement par virement bancaire',
                'fee' => 0,
                'fee_percentage' => 1.0,
                'logo' => 'payment-methods/bank-transfer.png',
                'is_active' => true,
                'sort_order' => 4,
                'config' => json_encode([
                    'bank_name' => 'Banque Internationale',
                    'account_name' => 'VOTRE ENTREPRISE',
                    'account_number' => 'CI05999999999999999999999',
                    'iban' => 'CI05999999999999999999999',
                    'bic' => 'ABCDCIAXXXX',
                ]),
            ],
            [
                'method_name' => 'Paiement à la livraison',
                'provider_name' => 'LIVRAISON',
                'description' => 'Paiement en espèces à la livraison',
                'fee' => 0,
                'fee_percentage' => 0,
                'logo' => 'payment-methods/cash-on-delivery.png',
                'is_active' => true,
                'sort_order' => 5,
                'config' => json_encode([
                    'instructions' => 'Paiement en espèces à la livraison uniquement',
                    'min_amount' => 0,
                    'max_amount' => 500000, // ~762€
                ]),
            ],
        ];
        
        foreach ($methods as $method) {
            // Vérifier si la méthode existe déjà
            $exists = Paiements::where('method_name', $method['method_name'])->exists();
            
            if (!$exists) {
                // La configuration est déjà encodée en JSON
                
                Paiements::create($method);
            }
        }
        
        $this->command->info('Méthodes de paiement créées avec succès !');
    }
}

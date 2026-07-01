<?php

namespace Database\Seeders;

use App\Models\Paiements;
use Illuminate\Database\Seeder;

/**
 * Méthodes de paiement : Orange Money, Moov Money, Wave (les seules actives),
 * chacune avec ses instructions à suivre par le client.
 */
class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'method_name' => 'Orange Money',
                'provider_name' => 'Orange',
                'account_number' => '07 00 00 00 00',
                'description' => 'Paiement via Orange Money.',
                'instructions' => "1. Composez #144# sur votre téléphone Orange.\n"
                    . "2. Choisissez « Transfert d'argent ».\n"
                    . "3. Envoyez le montant exact au numéro indiqué ci-dessus.\n"
                    . "4. Conservez la référence de transaction reçue par SMS.\n"
                    . "5. Saisissez cette référence puis cliquez sur « J'ai payé ».",
                'sort_order' => 1,
            ],
            [
                'method_name' => 'Moov Money',
                'provider_name' => 'Moov',
                'account_number' => '01 00 00 00 00',
                'description' => 'Paiement via Moov Money.',
                'instructions' => "1. Composez *155# sur votre téléphone Moov.\n"
                    . "2. Choisissez « Transfert d'argent ».\n"
                    . "3. Envoyez le montant exact au numéro indiqué ci-dessus.\n"
                    . "4. Conservez la référence de transaction reçue par SMS.\n"
                    . "5. Saisissez cette référence puis cliquez sur « J'ai payé ».",
                'sort_order' => 2,
            ],
            [
                'method_name' => 'Wave',
                'provider_name' => 'Wave',
                'account_number' => '05 00 00 00 00',
                'description' => 'Paiement via Wave.',
                'instructions' => "1. Ouvrez l'application Wave.\n"
                    . "2. Sélectionnez « Envoyer de l'argent ».\n"
                    . "3. Envoyez le montant exact au numéro indiqué ci-dessus.\n"
                    . "4. Conservez la référence de la transaction.\n"
                    . "5. Saisissez cette référence puis cliquez sur « J'ai payé ».",
                'sort_order' => 3,
            ],
        ];

        $keep = [];
        foreach ($methods as $data) {
            $m = Paiements::updateOrCreate(
                ['method_name' => $data['method_name']],
                array_merge($data, ['is_active' => true, 'fee' => 0, 'fee_percentage' => 0]),
            );
            $keep[] = $m->id;
        }

        // Toute autre méthode existante est désactivée (Orange/Moov/Wave seules actives)
        Paiements::whereNotIn('id', $keep)->update(['is_active' => false]);
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CinetPayService
{
    private $siteId = env('CINETPAY_SITE_ID');
    private $apiKey = env('CINETPAY_API_KEY');
    private $url = 'https://api-checkout.cinetpay.com/v1';

    public function payer($montant, $nom, $telephone, $description)
    {
        $transaction_id = 'CMD' . time();

        $data = [
            'apikey' => $this->apiKey,
            'site_id' => $this->siteId,
            'transaction_id' => $transaction_id,
            'amount' => $montant,
            'currency' => 'XOF',
            'channels' => ['ORANGE_MONEY', 'MOOV_MONEY', 'WAVE'],
            'description' => $description,
            'customer' => [
                'phone_number' => $telephone,
                'first_name' => $nom,
                'country' => 'ML'
            ],
            'notification_url' => route('paiement.callback'),
            'return_url' => route('paiement.success')
        ];

        $response = Http::post($this->url . '/initiate', $data);
        $json = $response->json();

        return [
            'ok' => $response->successful(),
            'url' => $json['data']['payment_url'] ?? null,
            'transaction_id' => $transaction_id
        ];
    }

    public function verifier($transaction_id)
    {
        $response = Http::get($this->url . '/confirm/' . $transaction_id, [
            'apikey' => $this->apiKey,
            'site_id' => $this->siteId
        ]);
        $data = $response->json();
        return $data['data']['status'] === 'SUCCESSFUL';
    }
}

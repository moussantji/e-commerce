<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FedapayService
{
    protected $baseUrl;
    protected $secretKey;
    protected $publicKey;

    public function __construct()
    {
        $this->baseUrl = config('services.fedapay.base_url', env('FEDAPAY_BASE', 'https://sandbox.fedapay.com/api'));
        $this->secretKey = config('services.fedapay.secret_key', env('FEDAPAY_SECRET_KEY'));
        $this->publicKey = config('services.fedapay.public_key', env('FEDAPAY_PUBLIC_KEY'));
    }

    /**
     * Create a Fedapay payment and return redirect url + raw response
     * @param float $amount (in CFA)
     * @param string $currency
     * @param string $provider e.g. 'orange', 'wave', 'moov'
     * @param array $customer ['name'=>..., 'email'=>..., 'phone'=>...]
     * @param string $orderRef
     * @param string $returnUrl
     * @param string $notifyUrl
     * @return array ['ok'=>bool, 'url'=>string|null, 'response'=>array]
     */
    public function createPayment(float $amount, string $currency, string $provider, array $customer, string $orderRef, string $returnUrl, string $notifyUrl): array
    {
        try {
            $payload = [
                'amount' => (int) round($amount),
                'currency' => $currency,
                'payment_method' => $provider,
                'customer' => [
                    'name' => $customer['name'] ?? null,
                    'email' => $customer['email'] ?? null,
                    'phone' => $customer['phone'] ?? null,
                ],
                'merchant' => [
                    'order_ref' => $orderRef,
                ],
                'redirect_url' => $returnUrl,
                'webhook_url' => $notifyUrl,
            ];

            $verify = filter_var(env('FEDAPAY_VERIFY_SSL', true), FILTER_VALIDATE_BOOLEAN);
            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Accept' => 'application/json',
            ])->withOptions([
                'verify' => $verify,
                'timeout' => 15,
            ])->post(rtrim($this->baseUrl, '/') . '/payments', $payload);

            $body = $res->json();
            if ($res->successful() && isset($body['data']['checkout_url'])) {
                return ['ok' => true, 'url' => $body['data']['checkout_url'], 'response' => $body];
            }
            return ['ok' => false, 'url' => null, 'response' => $body];
        } catch (\Exception $e) {
            return ['ok' => false, 'url' => null, 'response' => ['error' => $e->getMessage()]];
        }
    }
}

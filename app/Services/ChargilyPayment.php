<?php
class ChargilyPayment {
    private $publicKey = 'test_pk_N0I2SuAcTczUfGVxDrAPT8Gu2U4wacSCdKGbe4KE';
    private $secretKey = 'test_sk_QVy1sF1UdTrIJPFmxfZpME5v5x0x611uqBT2Tub0';
    private $baseUrl = 'https://pay.chargily.net/test/api/v2';
    private $appDomain = 'https://parkingpro.free.nf';

    public function createCheckout($amount, $currency = 'DZD', $orderId, $description) {
        $url = $this->baseUrl . '/checkouts';
        
        // Dynamic Domain Support
        // If BASE_URL is defined (in config.php), use it.
        $domain = defined('BASE_URL') ? rtrim(BASE_URL, '/') : 'https://parkingpro.free.nf';
        
        // Clean URL construction
        $successUrl = $domain . "/reservation/payment_success?order_id=$orderId";
        $cancelUrl = $domain . "/reservation/payment_cancel?order_id=$orderId";

        $payload = [
            "amount" => $amount, 
            "currency" => strtolower($currency),
            "success_url" => $successUrl,
            "failure_url" => $cancelUrl,
            "metadata" => [
                "order_id" => $orderId
            ],
            "description" => $description
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json'
        ]);
        
        // CRITICAL: Disable SSL Verification for Shared Hosting (InfinityFree)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            return ['error' => 'Curl Connection Error: ' . curl_error($ch)];
        }
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Return raw response for debugging
            return ['error' => 'Invalid API Response: ' . substr($response, 0, 100)];
        }
        
        // Handle API Level Errors (e.g. Validation, Auth)
        if (!isset($result['checkout_url'])) {
             $errorMsg = $result['message'] ?? 'Unknown API Error';
             // If validation errors exist
             if (isset($result['errors']) && is_array($result['errors'])) {
                 $errorMsg .= ' - ' . json_encode($result['errors']);
             }
             return ['error' => $errorMsg];
        }
        
        return $result;
    }
}

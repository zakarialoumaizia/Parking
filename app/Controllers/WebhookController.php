<?php
require_once '../app/Services/ChargilyPayment.php';

class WebhookController extends Controller {

    private $secretKey = 'test_sk_QVy1sF1UdTrIJPFmxfZpME5v5x0x611uqBT2Tub0';

    public function index() {
        // 1. Get content
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_SIGNATURE'] ?? '';

        if (!$payload || !$signature) {
             http_response_code(400);
             exit('Invalid Request');
        }

        // 2. Verify Signature
        // Chargily V2 uses HMAC SHA256
        $computedSignature = hash_hmac('sha256', $payload, $this->secretKey);

        if (!hash_equals($signature, $computedSignature)) {
             http_response_code(403);
             exit('Invalid Signature');
        }

        // 3. Process Event
        $data = json_decode($payload, true);
        $event = $data['type'] ?? '';

        if ($event === 'checkout.paid') {
             $checkout = $data['data'];
             $metadata = $checkout['metadata'];
             $orderId = $metadata['order_id'] ?? null;

             if ($orderId) {
                 $resModel = $this->model('Reservation');
                 // Update status to 'paye'
                 $resModel->updatePaymentStatusByCode($orderId, 'paye');
             }
        }

        http_response_code(200);
    }
}

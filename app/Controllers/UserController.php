<?php
class UserController extends Controller {
    public function dashboard() {
        if (!isLoggedIn()) {
             $this->redirect('auth/login');
        }
        
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];
        $reservationModel = $this->model('Reservation');
        
        // Handle New Subscription Booking
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_subscription'])) {
            $months = intval($_POST['duration_months']);
            $billing = $_POST['billing_mode']; // 'now' or 'monthly' (deferred)
            
            if ($months < 1) $months = 1;
            
            // 1. Find Free Place (Standard)
            // Ideally we check for a specific type, but taking any free 'standard' spot
            $stmt = $db->query("SELECT id, numero FROM places WHERE statut = 'libre' AND type = 'standard' LIMIT 1");
            $place = $stmt->fetch();
            
            if (!$place) {
                setFlashMessage('error', 'Désolé, aucune place standard n\'est disponible pour un abonnement.');
            } else {
                $pricePerMonth = 12000; // Fixed Subscription Rate
                $total = $pricePerMonth * $months;
                
                $start = date('Y-m-d H:i:s');
                $end = date('Y-m-d H:i:s', strtotime("+$months months"));
                
                // Determine Payment Status logic
                // If 'monthly' (Deferred), status is 'non_paye' (Pending Bill)
                // If 'now', status is 'paye' (Simulated online payment)
                $payStatus = ($billing === 'monthly') ? 'non_paye' : 'paye';
                $payMode = ($billing === 'monthly') ? 'groupe' : 'en_ligne';
                
                // Create Reservation
                $code = $reservationModel->createReservation($userId, $place['id'], $start, $end, $total, $payMode, $payStatus);
                
                if ($code) {
                    // Lock the Place
                    $db->prepare("UPDATE places SET statut = 'occupee' WHERE id = ?")->execute([$place['id']]);
                    setFlashMessage('success', "Félicitations ! Votre abonnement est actif. Place attribuée : <strong>" . $place['numero'] . "</strong>");
                } else {
                    setFlashMessage('error', "Une erreur est survenue lors de la réservation.");
                }
            }
            $this->redirect('user/dashboard');
        }

        $data = [
            'activeReservations' => $reservationModel->getActiveReservations($userId),
            'historyReservations' => $reservationModel->getReservationHistory($userId),
            'subscriptionPrice' => 12000 // Monthly Rate passed to view
        ];

        $this->view('user/dashboard', $data);
    }
}

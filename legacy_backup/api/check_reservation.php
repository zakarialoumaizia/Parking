<?php
require_once '../db_config.php';
requireRole('agent');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $reservation_id = $data['id'] ?? '';

    if (empty($reservation_id)) {
        echo json_encode(['success' => false, 'message' => 'ID de réservation manquant']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$reservation_id]);
        $reservation = $stmt->fetch();

        if (!$reservation) {
            echo json_encode(['success' => false, 'message' => 'Réservation non trouvée']);
            exit;
        }

        $now = new DateTime();
        $end_date = new DateTime($reservation['date_fin']);

        if ($end_date < $now) {
            $message = 'Stationnement expiré';
            // Update reservation status
            $stmt = $pdo->prepare("UPDATE reservations SET statut = 'terminee' WHERE id = ?");
            $stmt->execute([$reservation_id]);
        } else {
            $message = 'Stationnement valide';
        }

        echo json_encode(['success' => true, 'message' => $message]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>

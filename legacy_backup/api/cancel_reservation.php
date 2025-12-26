<?php
require_once '../db_config.php';
requireRole('usager');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $reservation_id = $data['id'] ?? '';

    if (empty($reservation_id)) {
        echo json_encode(['success' => false, 'message' => 'ID de réservation manquant']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Check if reservation belongs to user
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? AND utilisateur_id = ?");
        $stmt->execute([$reservation_id, $_SESSION['user_id']]);
        $reservation = $stmt->fetch();

        if (!$reservation) {
            echo json_encode(['success' => false, 'message' => 'Réservation non trouvée']);
            exit;
        }

        // Update reservation status
        $stmt = $pdo->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = ?");
        $stmt->execute([$reservation_id]);

        // Update place status
        $stmt = $pdo->prepare("UPDATE places SET statut = 'libre' WHERE id = ?");
        $stmt->execute([$reservation['place_id']]);

        // Create notification
        $message = "Réservation #{$reservation['code_reservation']} annulée avec succès.";
        $stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, message) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $message]);

        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Réservation annulée']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>

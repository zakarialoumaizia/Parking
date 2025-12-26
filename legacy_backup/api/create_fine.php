<?php
require_once '../db_config.php';
requireRole('agent');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $reservation_id = $data['reservation_id'] ?? '';
    $montant = $data['montant'] ?? '';
    $motif = $data['motif'] ?? '';
    $details = $data['details'] ?? '';

    if (empty($reservation_id) || empty($montant) || empty($motif)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Get reservation details
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$reservation_id]);
        $reservation = $stmt->fetch();

        if (!$reservation) {
            echo json_encode(['success' => false, 'message' => 'Réservation non trouvée']);
            exit;
        }

        // Create fine
        $stmt = $pdo->prepare("INSERT INTO amendes (agent_id, reservation_id, montant, motif, statut) VALUES (?, ?, ?, ?, 'non_payee')");
        $stmt->execute([$_SESSION['user_id'], $reservation_id, $montant, $motif . ($details ? ': ' . $details : '')]);

        // Create notification for user
        $message = "Amende de {$montant}€ émise pour la réservation #{$reservation['code_reservation']}. Motif: {$motif}";
        $stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, message) VALUES (?, ?)");
        $stmt->execute([$reservation['utilisateur_id'], $message]);

        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Amende émise avec succès']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>

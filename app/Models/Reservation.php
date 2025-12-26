<?php
class Reservation extends Model {
    
    public function getActiveReservations($userId) {
        $stmt = $this->db->prepare("
            SELECT r.*, p.numero as place_numero, pay.statut as paiement_statut
            FROM reservations r 
            JOIN places p ON r.place_id = p.id 
            LEFT JOIN paiements pay ON r.id = pay.reservation_id
            WHERE r.utilisateur_id = ? AND r.statut = 'active'
            ORDER BY r.date_debut ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getReservationHistory($userId, $limit = 5) {
         $stmt = $this->db->prepare("
            SELECT r.*, p.numero as place_numero, pay.statut as paiement_statut
            FROM reservations r 
            JOIN places p ON r.place_id = p.id 
            LEFT JOIN paiements pay ON r.id = pay.reservation_id
            WHERE r.utilisateur_id = ? AND r.statut != 'active'
            ORDER BY r.date_debut DESC LIMIT $limit
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getCurrentActiveReservations() {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT r.*, u.nom as user_name 
                FROM reservations r 
                JOIN utilisateurs u ON r.utilisateur_id = u.id 
                WHERE r.statut='active' AND :now BETWEEN r.date_debut AND r.date_fin";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':now', $now);
        $stmt->execute();
        
        $reservationsByPlace = [];
        foreach ($stmt->fetchAll() as $res) {
            $reservationsByPlace[$res['place_id']] = $res;
        }
        return $reservationsByPlace;
        return $reservationsByPlace;
    }

    public function createReservation($userId, $placeId, $start_dt, $end_dt, $price, $mode = 'en_ligne', $payStatus = 'paye') {
        try {
            $this->db->beginTransaction();
            $code = 'RES-' . strtoupper(uniqid());

            // Create Reservation
            $stmtRes = $this->db->prepare("INSERT INTO reservations (code_reservation, utilisateur_id, place_id, date_debut, date_fin, statut) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmtRes->execute([$code, $userId, $placeId, $start_dt, $end_dt]);
            $resId = $this->db->lastInsertId();

            // Create Payment Record
            $stmtPay = $this->db->prepare("INSERT INTO paiements (reservation_id, montant, mode, statut, date_paiement) VALUES (?, ?, ?, ?, NOW())");
            $stmtPay->execute([$resId, $price, $mode, $payStatus]);

            // Notification
            $stmtNotif = $this->db->prepare("INSERT INTO notifications (utilisateur_id, message) VALUES (?, ?)");
            $stmtNotif->execute([$userId, "Reservation Created: Code " . $code]);

            $this->db->commit();
            return $code;

        } catch (Exception $e) {
            $this->db->rollBack();
            return "ERROR: " . $e->getMessage(); 
        }
    }

    public function updatePaymentStatusByCode($code, $status) {
        // Find Res ID
        $stmt = $this->db->prepare("SELECT id FROM reservations WHERE code_reservation = ?");
        $stmt->execute([$code]);
        $resId = $stmt->fetchColumn();
        
        if ($resId) {
             $stmtUpdate = $this->db->prepare("UPDATE paiements SET statut = ? WHERE reservation_id = ?");
             $stmtUpdate->execute([$status, $resId]);
        }
    }
    public function getReservationByCode($code) {
        $stmt = $this->db->prepare("SELECT r.*, pay.statut as paiement_statut, pay.montant FROM reservations r JOIN paiements pay ON r.id = pay.reservation_id WHERE r.code_reservation = ?");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }
}

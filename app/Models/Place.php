<?php
class Place extends Model {
    public function getAllPlaces() {
        $stmt = $this->db->prepare("SELECT * FROM places ORDER BY numero");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Find ALL available places for manual selection
    public function getAvailablePlaces($type, $start_dt, $end_dt) {
         $sql = "
            SELECT p.* 
            FROM places p 
            WHERE p.type = ? 
            AND p.statut != 'indisponible' 
            AND p.id NOT IN (
                SELECT r.place_id 
                FROM reservations r 
                WHERE r.statut = 'active'
                AND (
                    (r.date_debut < ? AND r.date_fin > ?) 
                )
            )
            ORDER BY p.numero
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type, $end_dt, $start_dt]);
        return $stmt->fetchAll();
    }

    public function isPlaceAvailable($placeId, $start_dt, $end_dt) {
        $sql = "SELECT id FROM reservations 
                WHERE place_id = ? 
                AND statut = 'active' 
                AND (date_debut < ? AND date_fin > ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$placeId, $end_dt, $start_dt]);
        return ($stmt->fetch() === false);
    }

    public function getPlaceById($id) {
        $stmt = $this->db->prepare("SELECT * FROM places WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getTariff($type) {
        $stmt = $this->db->prepare("SELECT prix_heure FROM tarifs WHERE type_place = ?");
        $stmt->execute([$type]);
        return $stmt->fetchColumn();
    }
}

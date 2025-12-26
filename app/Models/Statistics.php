<?php
class Statistics extends Model {
    public function getTotalUsers() {
        return $this->db->query("SELECT COUNT(*) FROM utilisateurs WHERE role IN ('usager', 'premium')")->fetchColumn();
    }

    public function getReservationsToday() {
        return $this->db->query("SELECT COUNT(*) FROM reservations WHERE DATE(date_debut) = CURDATE()")->fetchColumn();
    }

    public function getOccupancyRate() {
        $totalPlaces = $this->db->query("SELECT COUNT(*) FROM places")->fetchColumn();
        $activeRes = $this->db->query("SELECT COUNT(*) FROM reservations WHERE statut = 'active'")->fetchColumn();
        return $totalPlaces > 0 ? round(($activeRes / $totalPlaces) * 100) : 0;
    }

    public function getTotalRevenue() {
        return $this->db->query("SELECT SUM(montant) FROM paiements WHERE statut = 'paye'")->fetchColumn();
    }

    public function getLastActivity($limit = 5) {
        return $this->db->query("SELECT r.*, u.nom FROM reservations r JOIN utilisateurs u ON r.utilisateur_id = u.id ORDER BY r.id DESC LIMIT $limit")->fetchAll();
    }

    public function getRevenueByMonth() {
        return $this->db->query("
            SELECT DATE_FORMAT(date_paiement, '%Y-%m') as mois, SUM(montant) as total 
            FROM paiements 
            WHERE statut = 'paye' 
            GROUP BY mois 
            ORDER BY mois DESC 
            LIMIT 12
        ")->fetchAll();
    }

    public function getUsageByType() {
        return $this->db->query("
            SELECT p.type, COUNT(r.id) as count 
            FROM reservations r 
            JOIN places p ON r.place_id = p.id 
            GROUP BY p.type
        ")->fetchAll();
    }
}

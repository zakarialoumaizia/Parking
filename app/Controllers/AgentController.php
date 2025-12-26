<?php
class AgentController extends Controller {
    public function dashboard() {
        if (!isLoggedIn() || !hasRole(['agent'])) {
             $this->redirect('auth/login');
        }

        $placeModel = $this->model('Place');
        $reservationModel = $this->model('Reservation');

        $data = [
            'places' => $placeModel->getAllPlaces(),
            'reservationsByPlace' => $reservationModel->getCurrentActiveReservations()
        ];

        $this->view('agent/dashboard', $data);
    }
    
    public function amendes() {
        $db = Database::getInstance()->getConnection();
        
        // Fetch Tax Types for Dropdown
        $types_amende = $db->query("SELECT * FROM types_taxe WHERE slug != 'tva_global'")->fetchAll();
        
        // Fetch Current Occupant Info if place is set
        $currentReservation = null;
        if (isset($_GET['place'])) {
            $stmt = $db->prepare("
                SELECT r.*, u.nom, u.email, p.numero as place_num
                FROM reservations r
                JOIN utilisateurs u ON r.utilisateur_id = u.id
                JOIN places p ON r.place_id = p.id
                WHERE r.place_id = ? AND r.statut = 'active'
                ORDER BY r.id DESC LIMIT 1
            ");
            $stmt->execute([$_GET['place']]);
            $currentReservation = $stmt->fetch();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_amende'])) {
            $place_id = $_POST['place_id'];
            $montant = $_POST['montant'];
            $motif = $_POST['motif'];
            
            // Find Active Reservation (Re-verify)
            $stmt = $db->prepare("SELECT id FROM reservations WHERE place_id = ? AND statut = 'active' ORDER BY id DESC LIMIT 1");
            $stmt->execute([$place_id]);
            $res = $stmt->fetch();
            
            if ($res) {
                 $reservId = $res['id'];
                 $sql = "INSERT INTO amendes (reservation_id, montant, motif, statut, date_amende) VALUES (?, ?, ?, 'non_payee', NOW())";
                 $insert = $db->prepare($sql);
                 if ($insert->execute([$reservId, $montant, $motif])) {
                     setFlashMessage('success', 'Amende enregistrée avec succès.');
                     $this->redirect('agent/dashboard');
                 } else {
                     setFlashMessage('error', 'Erreur BD lors de l\'enregistrement.');
                 }
            } else {
                 setFlashMessage('error', 'Erreur: Aucune réservation active sur cette place pour lier l\'amende.');
            }
        }
        
        $data = [
            'types_amende' => $types_amende,
            'currentReservation' => $currentReservation
        ];
        $this->view('agent/amendes', $data); 
    }
}

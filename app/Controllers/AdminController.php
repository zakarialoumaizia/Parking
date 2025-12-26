<?php
class AdminController extends Controller {
    
    public function __construct() {
        if (!isLoggedIn() || !hasRole(['admin'])) {
             $this->redirect('auth/login');
        }
    }

    public function dashboard() {
        $statsModel = $this->model('Statistics');
        $data = [
            'nbUsers' => $statsModel->getTotalUsers(),
            'nbResToday' => $statsModel->getReservationsToday(),
            'occupancy' => $statsModel->getOccupancyRate(),
            'revenue' => $statsModel->getTotalRevenue(),
            'lastActivity' => $statsModel->getLastActivity()
        ];
        $this->view('admin/dashboard', $data);
    }

    public function places() {
        $db = Database::getInstance()->getConnection();
        
        // Handle Add Place
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_place'])) {
            $numero = trim($_POST['numero']);
            $type = $_POST['type'];
            
            if (empty($numero)) {
                 setFlashMessage('error', 'Le numéro est requis.');
            } else {
                // Check if exists
                $stmt = $db->prepare("SELECT id FROM places WHERE numero = ?");
                $stmt->execute([$numero]);
                if ($stmt->fetch()) {
                    setFlashMessage('error', 'Ce numéro de place existe déjà.');
                } else {
                    $stmt = $db->prepare("INSERT INTO places (numero, type, statut) VALUES (?, ?, 'libre')");
                    if ($stmt->execute([$numero, $type])) {
                         setFlashMessage('success', 'Place ajoutée avec succès.');
                    } else {
                         setFlashMessage('error', 'Erreur lors de l\'ajout.');
                    }
                }
            }
            $this->redirect('admin/places');
        }

        $placeModel = $this->model('Place');
        $data = ['places' => $placeModel->getAllPlaces()];
        $this->view('admin/places', $data);
    }

     public function rapports() {
        $statsModel = $this->model('Statistics');
        $data = [
            'revenueMonth' => $statsModel->getRevenueByMonth(),
            'usageType' => $statsModel->getUsageByType(),
            'totalRevenue' => $statsModel->getTotalRevenue(),
            'totalUsers' => $statsModel->getTotalUsers(),
            'occupancy' => $statsModel->getOccupancyRate()
        ];
        $this->view('admin/rapports', $data);
    }
    
    public function users() {
        $db = Database::getInstance()->getConnection();
        
        // Handle Update Role
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
            $id = $_POST['user_id'];
            $role = $_POST['role'];
            
            // Safety check: Prevent changing self role to non-admin if you are the one logged in
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id && $role != 'admin') {
                 setFlashMessage('error', 'Vous ne pouvez pas révoquer vos propres droits administrateur.');
            } else {
                $stmt = $db->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?");
                if ($stmt->execute([$role, $id])) {
                    setFlashMessage('success', 'Rôle mis à jour avec succès.');
                } else {
                    setFlashMessage('error', 'Erreur lors de la mise à jour.');
                }
            }
            $this->redirect('admin/users');
        }

        try {
            $stmt = $db->query("SELECT * FROM utilisateurs ORDER BY id DESC");
            $users = $stmt->fetchAll();
        } catch (Exception $e) {
            $users = []; 
        }
        $data = ['users' => $users];
        
        $this->view('admin/users', $data);
    }
    
    public function notifications() {
        $db = Database::getInstance()->getConnection();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../app/Services/MailService.php';
            $mail = new MailService();
            
            $target = $_POST['target_group'];
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            $sendEmail = isset($_POST['send_email']);
            
            // Determine recipients
            $recipients = [];
            if ($target === 'all') {
                $recipients = $db->query("SELECT id, email, nom FROM utilisateurs")->fetchAll();
            } elseif ($target === 'premium') {
                $recipients = $db->query("SELECT id, email, nom FROM utilisateurs WHERE role = 'premium'")->fetchAll();
            } elseif ($target === 'single') {
                $uid = $_POST['user_id'];
                $stmt = $db->prepare("SELECT id, email, nom FROM utilisateurs WHERE id = ?");
                $stmt->execute([$uid]);
                $recipients = $stmt->fetchAll();
            }
            
            // Loop send
            $count = 0;
            foreach ($recipients as $u) {
                // Internal Notif
                $stmt = $db->prepare("INSERT INTO notifications (utilisateur_id, message) VALUES (?, ?)");
                $stmt->execute([$u['id'], "[$subject] $message"]);
                
                // Email
                if ($sendEmail) {
                    $mail->sendEmail($u['email'], $subject, "<h3>Bonjour {$u['nom']},</h3><p>$message</p>");
                }
                $count++;
            }
            
            setFlashMessage('success', "Notification envoyée à $count utilisateurs.");
        }
        
        $users = $db->query("SELECT id, email, nom FROM utilisateurs")->fetchAll();
        $this->view('admin/notifications', ['users' => $users]);
    }
    
    public function taxes() {
        $db = Database::getInstance()->getConnection();
        
        // Handle Updates
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_taxes'])) {
             foreach ($_POST['taxe'] as $slug => $montant) {
                 $stmt = $db->prepare("UPDATE types_taxe SET montant = ? WHERE slug = ?");
                 $stmt->execute([$montant, $slug]);
             }
             setFlashMessage('success', "Taxes et Amendes mises à jour.");
             $this->redirect('admin/taxes');
        }

        // Fetch Configured Taxes (Fines only)
        $taxesConfig = $db->query("SELECT slug, nom, montant FROM types_taxe WHERE slug != 'tva_global'")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);
        
        $statsModel = $this->model('Statistics');
        $totalRevenue = $statsModel->getTotalRevenue();
        
        // Get recent transactions
        $transactions = $db->query("SELECT p.*, r.code_reservation FROM paiements p JOIN reservations r ON p.reservation_id = r.id WHERE p.statut = 'paye' ORDER BY p.date_paiement DESC LIMIT 20")->fetchAll();
        
        // Get Fines - Robust Query
        $amendes = [];
        try {
            $amendes = $db->query("
                SELECT a.*, u.nom as user_nom, p.numero as place_numero 
                FROM amendes a 
                LEFT JOIN reservations r ON a.reservation_id = r.id 
                LEFT JOIN utilisateurs u ON r.utilisateur_id = u.id 
                LEFT JOIN places p ON r.place_id = p.id 
                ORDER BY a.date_amende DESC
            ")->fetchAll();
        } catch (Exception $e) {}
        
        $data = [
            'totalRevenue' => $totalRevenue,
            'transactions' => $transactions,
            'amendes' => $amendes,
            'taxesConfig' => $taxesConfig
        ];
        
        $this->view('admin/taxes', $data);
    }
    
    public function tarifs() {
         $db = Database::getInstance()->getConnection();
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             foreach ($_POST['prix'] as $type => $price) {
                 $stmt = $db->prepare("UPDATE tarifs SET prix_heure = ? WHERE type_place = ?");
                 $stmt->execute([$price, $type]);
             }
             setFlashMessage('success', 'Tarifs mis à jour.');
             $this->redirect('admin/tarifs');
         }
         
         $tarifs = $db->query("SELECT type_place, prix_heure FROM tarifs")->fetchAll(PDO::FETCH_KEY_PAIR);
         $this->view('admin/tarifs', ['tarifs' => $tarifs]);
    }
    
    public function config() {
        $this->view('admin/config');
    }
}

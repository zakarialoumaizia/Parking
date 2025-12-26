<?php
require_once '../app/Services/ChargilyPayment.php';

class ReservationController extends Controller {

    public function create() {
        if (!isLoggedIn()) {
             $this->redirect('auth/login');
        }
        
        $data = [
            'error' => '',
            'availablePlaces' => [], // List instead of single
            'selectedPlace' => null,
            'price' => 0,
            'step' => 1 // 1: Search, 2: Select, 3: Confirm
        ];

        // Preservation of form data
        $start = $_POST['date_debut'] ?? '';
        $end = $_POST['date_fin'] ?? '';
        $type = $_POST['type'] ?? 'standard';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Step 1 -> 2: Search
            if (isset($_POST['search'])) {
                 if (empty($start) || empty($end)) {
                     $data['error'] = "Please fill all fields.";
                 } elseif (strtotime($start) < (time() - 300)) { // 5 mins buffer
                     $data['error'] = "La date de début ne peut pas être dans le passé.";
                 } elseif (strtotime($end) <= strtotime($start)) {
                     $data['error'] = "End date must be after start date.";
                 } else {
                     $start_dt = str_replace('T', ' ', $start);
                     $end_dt = str_replace('T', ' ', $end);
                     
                     $placeModel = $this->model('Place');
                     $data['availablePlaces'] = $placeModel->getAvailablePlaces($type, $start_dt, $end_dt);
                     
                     if (empty($data['availablePlaces'])) {
                         $data['error'] = "No places available for these dates.";
                     } else {
                         $data['step'] = 2; // Go to selection
                     }
                 }
            }
            
            // Step 2 -> 3: Select Place
            elseif (isset($_POST['select_place'])) {
                $placeId = $_POST['place_id'];
                $placeModel = $this->model('Place');
                $place = $placeModel->getPlaceById($placeId);
                
                if ($place) {
                     $start_dt = str_replace('T', ' ', $start);
                     $end_dt = str_replace('T', ' ', $end);
                     
                     $duration_hours = (strtotime($end_dt) - strtotime($start_dt)) / 3600;
                     $tarif = $placeModel->getTariff($type);
                     
                     if (!empty($place['prix_custom']) && $place['prix_custom'] > 0) {
                        $price = ceil($duration_hours) * $place['prix_custom'];
                     } elseif ($type === 'VIP') {
                         // VIP is per month. Calculate months started.
                         // 1 hour = 1 month charged if short? Or Minimum 1 month.
                         $days = $duration_hours / 24;
                         $months = ceil($days / 30);
                         if ($months < 1) $months = 1;
                         $price = $months * $tarif;
                     } else {
                        $price = ceil($duration_hours) * $tarif;
                     }
                     
                     $data['selectedPlace'] = $place;
                     $data['price'] = $price;
                     $data['step'] = 3; // Confirmation
                }
            }
            
            // Step 3 -> Final: Confirm & Pay
            elseif (isset($_POST['confirm'])) {
                 $placeId = $_POST['place_id'];
                 $paymentMethod = $_POST['payment_method'] ?? 'online'; 
                 
                 $start_dt = str_replace('T', ' ', $start);
                 $end_dt = str_replace('T', ' ', $end);
                 $placeModel = $this->model('Place');
                 $place = $placeModel->getPlaceById($placeId);
                 
                 // Recalculate Price
                 $duration_hours = (strtotime($end_dt) - strtotime($start_dt)) / 3600;
                 $tarif = $placeModel->getTariff($type);
                 
                 $price = (!empty($place['prix_custom']) && $place['prix_custom'] > 0) 
                          ? ceil($duration_hours) * $place['prix_custom'] 
                          : (($type === 'VIP') 
                                ? (ceil(($duration_hours/24)/30) > 0 ? ceil(($duration_hours/24)/30) : 1) * $tarif 
                                : ceil($duration_hours) * $tarif);

                 // CRITICAL: Double Check Availability before writing to DB
                 if (!$placeModel->isPlaceAvailable($placeId, $start_dt, $end_dt)) {
                     $data['error'] = "Désolé, cette place n'est plus disponible pour le créneau choisi (elle vient d'être réservée). Veuillez sélectionner une autre place.";
                     $data['step'] = 2; 
                     $data['availablePlaces'] = $placeModel->getAvailablePlaces($type, $start_dt, $end_dt);
                 } 
                 else {
                     $resModel = $this->model('Reservation');
                     
                     // Attempt Reservation Creation
                     if ($paymentMethod === 'group' && hasRole(['premium', 'vip'])) {
                          // VIP Group
                          $code = $resModel->createReservation($_SESSION['user_id'], $place['id'], $start_dt, $end_dt, $price, 'groupe', 'non_paye');
                          
                          if ($code && strpos($code, 'ERROR:') !== 0) {
                               setFlashMessage('success', "Réservation confirmée avec succès ! (Facturation Groupée). Code: $code");
                               $this->redirect('user/dashboard');
                          } else {
                               $data['error'] = "Erreur lors de l'enregistrement : " . $code;
                          }
                     } else {
                         // Online Payment
                         $code = $resModel->createReservation($_SESSION['user_id'], $place['id'], $start_dt, $end_dt, $price, 'en_ligne', 'non_paye');
                         
                         if ($code && strpos($code, 'ERROR:') !== 0) {
                             // Reservation Created in DB. Proceed to Payment Gateway.
                             $chargily = new ChargilyPayment();
                             $checkout = $chargily->createCheckout($price, 'DZD', $code, "Reservation $code - Place " . $place['numero']);
                             
                             if (isset($checkout['checkout_url'])) {
                                 // Redirect User to Payment Page
                                 header("Location: " . $checkout['checkout_url']);
                                 exit();
                             } else {
                                 // Payment Gateway Error
                                 $errorMsg = $checkout['error'] ?? 'Unknown Gateway Error';
                                 setFlashMessage('error', "Erreur de la passerelle de paiement ($errorMsg). Votre réservation est enregistrée, veuillez tenter le paiement depuis le tableau de bord.");
                                 $this->redirect('user/dashboard');
                             }
                         } else {
                             // DB Insertion Failed
                             $data['error'] = "Erreur critique Base de Données : " . $code;
                         }
                     }
                 }
            }
        }
        
        $this->view('reservation/create', $data);
    }
    
    public function payment_success() {
        // Chargily redirects here
        // Verify signature ? simpler: check order_id (code) status
        // Update payment status to 'paye'
        $code = $_GET['order_id'] ?? '';
        if ($code) {
            $resModel = $this->model('Reservation');
            $resModel->updatePaymentStatusByCode($code, 'paye');
            
            // Send Email
            $resDetails = $resModel->getReservationByCode($code); // Need to create helper maybe? Or just generic msg
            // Fetch user email if not session
            // For simplicity, let's assume session or fetch. 
            // Better to fetch from DB using code.
            
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT u.email, u.title as nom, r.date_debut, p.numero FROM reservations r JOIN utilisateurs u ON r.utilisateur_id = u.id JOIN places p ON r.place_id = p.id WHERE r.code_reservation = ?");
            $stmt->execute([$code]);
            $info = $stmt->fetch();
            
            if ($info) {
                 require_once dirname(__DIR__) . '/Services/MailService.php';
                 $mail = new MailService();
                 $subject = "Confirmation de Réservation - ParkingSmart";
                 $body = "<h2>Merci !</h2><p>Votre réservation (Code: <b>$code</b>) pour la place <b>{$info['numero']}</b> est confirmée.</p><p>Début: {$info['date_debut']}</p>";
                 $mail->sendEmail($info['email'], $subject, $body);
            }

            setFlashMessage('success', "Payment Successful! Reservation $code confirmed.");
        }
        $this->redirect('user/dashboard');
    }

    public function payment_cancel() {
        setFlashMessage('error', "Payment Cancelled.");
        $this->redirect('reservation/create');
    }
    public function retry_payment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
            $code = $_POST['code'];
            $resModel = $this->model('Reservation');
            $res = $resModel->getReservationByCode($code);
            
            if ($res && $res['paiement_statut'] !== 'paye') {
                 $chargily = new ChargilyPayment();
                 $checkout = $chargily->createCheckout($res['montant'], 'DZD', $code, "Paiement (Retry) - Code $code");
                 
                 if (isset($checkout['checkout_url'])) {
                     header("Location: " . $checkout['checkout_url']);
                     exit();
                 } else {
                     setFlashMessage('error', "Erreur passerelle: " . ($checkout['error'] ?? 'Unknown'));
                 }
            } else {
                setFlashMessage('error', "Réservation introuvable ou déjà payée.");
            }
        }
        $this->redirect('user/dashboard');
    }
}

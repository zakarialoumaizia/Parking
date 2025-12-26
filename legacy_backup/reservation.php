<?php
require_once 'includes/functions.php';
requireRole(['usager', 'premium']);

$error = '';
$success = '';
$availablePlace = null;
$price = 0;
$start_dt = '';
$end_dt = '';
$type = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = $_POST['date_debut']; // datetime-local format: YYYY-MM-DDTHH:MM
    $end = $_POST['date_fin'];
    $type = $_POST['type'];

    if (empty($start) || empty($end) || empty($type)) {
        $error = "Veuillez remplir tous les champs.";
    } elseif (strtotime($start) < time()) {
        $error = "La date de début ne peut pas être dans le passé.";
    } elseif (strtotime($end) <= strtotime($start)) {
        $error = "La date de fin doit être après la date de début.";
    } else {
        // Format for DB
        $start_dt = str_replace('T', ' ', $start);
        $end_dt = str_replace('T', ' ', $end);
        
        // Calculate duration and price
        $duration_hours = (strtotime($end_dt) - strtotime($start_dt)) / 3600;
        
        $stmtPrice = $pdo->prepare("SELECT prix_heure FROM tarifs WHERE type_place = ?");
        $stmtPrice->execute([$type]);
        $tarif = $stmtPrice->fetchColumn();
        
        // Even if no general tariff, custom price might exist, so we don't block yet unless specific logic requires it.
        // But usually we need a base price.
        
        // CHECK AVAILABILITY
        $sql = "
            SELECT p.id, p.numero, p.prix_custom 
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
            LIMIT 1
        ";
        // Simplified overlap logic: Request (S, E). Existing (rS, rE). Overlap if S < rE AND E > rS.
        // My previous query uses complex logic. Let's stick to standard:
        // p.id NOT IN (reservations WHERE NOT (res.end <= start OR res.start >= end))
        // Which matches my short logic: r.date_debut < request_end AND r.date_fin > request_start
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$type, $end_dt, $start_dt]);
        $place = $stmt->fetch();
        
        if ($place) {
            // Calculate Price
            if (!empty($place['prix_custom']) && $place['prix_custom'] > 0) {
                $price = ceil($duration_hours) * $place['prix_custom'];
            } elseif ($tarif) {
                $price = ceil($duration_hours) * $tarif;
            } else {
                $error = "Impossible de calculer le tarif.";
                $place = null; // invalid
            }

            if ($place) {
                 if (isset($_POST['confirm'])) {
                     $code = 'RES-' . strtoupper(uniqid());
                     
                     $pdo->beginTransaction();
                     try {
                         // Create Reservation
                         $stmtRes = $pdo->prepare("INSERT INTO reservations (code_reservation, utilisateur_id, place_id, date_debut, date_fin, statut) VALUES (?, ?, ?, ?, ?, 'active')");
                         $stmtRes->execute([$code, $_SESSION['user_id'], $place['id'], $start_dt, $end_dt]);
                         $resId = $pdo->lastInsertId();
                         
                         // Create Payment Record
                         $mode = 'en_ligne';
                         $stmtPay = $pdo->prepare("INSERT INTO paiements (reservation_id, montant, mode, statut, date_paiement) VALUES (?, ?, ?, 'paye', NOW())");
                         $stmtPay->execute([$resId, $price, $mode]);
                         
                         // Notification
                         addNotification($_SESSION['user_id'], "Réservation confirmée : Place " . $place['numero']);
                         
                         $pdo->commit();
                         setFlashMessage('success', "Réservation confirmée ! Code: $code. Place: " . $place['numero']);
                         header('Location: dashboard_user.php');
                         exit();
                         
                     } catch(Exception $e) {
                         $pdo->rollBack();
                         $error = "Erreur système: " . $e->getMessage();
                     }
                } else {
                    $availablePlace = $place; // Show confirmation screen
                    $start_dt = $start; // Keep for form
                    $end_dt = $end;
                }
            }
        } else {
            $error = "Aucune place disponible pour ces horaires.";
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container" style="max-width: 600px;">
    <h2 style="margin-bottom: 30px;">Réserver une place</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($availablePlace && !isset($_POST['confirm'])): ?>
        <!-- Confirmation Step -->
        <div class="card">
            <h3 style="color: var(--success); text-align: center;"><i class="fas fa-check-circle"></i> Place Disponible !</h3>
            <div style="margin: 20px 0; padding: 20px; background: var(--bg-body); border-radius: var(--radius-sm);">
                <p><strong>Place :</strong> <?php echo $availablePlace['numero']; ?></p>
                <p><strong>Type :</strong> <?php echo ucfirst($_POST['type']); ?></p>
                <p><strong>De :</strong> <?php echo str_replace('T', ' ', $_POST['date_debut']); ?></p>
                <p><strong>À :</strong> <?php echo str_replace('T', ' ', $_POST['date_fin']); ?></p>
                <p style="font-size: 18px; color: var(--primary); margin-top: 10px;"><strong>Total estimé : <?php echo formatCurrency($price); ?></strong></p>
                <?php if (!empty($availablePlace['prix_custom'])): ?>
                    <small style="color: var(--secondary-dark);">* Tarif spécifique appliqué à cette place.</small>
                <?php endif; ?>
            </div>
            
            <form method="POST">
                <input type="hidden" name="date_debut" value="<?php echo $_POST['date_debut']; ?>">
                <input type="hidden" name="date_fin" value="<?php echo $_POST['date_fin']; ?>">
                <input type="hidden" name="type" value="<?php echo $_POST['type']; ?>">
                <input type="hidden" name="confirm" value="1">
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Confirmer et Payer</button>
                <a href="reservation.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px; text-align: center;">Annuler</a>
            </form>
        </div>
    <?php else: ?>
        <!-- Search Step -->
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label>Type de place</label>
                    <select name="type">
                        <option value="standard">Standard</option>
                        <option value="PMR">PMR (Personne à Mobilité Réduite)</option>
                        <?php if (hasRole('premium') || hasRole('admin')): ?>
                            <option value="VIP">VIP</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <?php if (checkRole('premium')): ?>
                    <div class="alert alert-success" style="margin-bottom: 20px;">
                        <i class="fas fa-gem"></i> <strong>Mode Premium :</strong> Vous bénéficiez d'une priorité de réservation.
                    </div>
                <?php endif; ?>
                
                <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Date de début</label>
                        <input type="datetime-local" name="date_debut" required min="<?php echo date('Y-m-d\TH:i'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Date de fin</label>
                        <input type="datetime-local" name="date_fin" required min="<?php echo date('Y-m-d\TH:i'); ?>">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Vérifier Disponibilité</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

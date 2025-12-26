<?php
require_once 'includes/functions.php';
requireRole(['agent', 'admin']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $montant = $_POST['montant'];
    $motif = $_POST['motif'];
    
    if (empty($reservation_id) || empty($montant) || empty($motif)) {
        $error = "Tous les champs sont requis.";
    } else {
        // Create Amende
        $stmt = $pdo->prepare("INSERT INTO amendes (agent_id, reservation_id, montant, motif, statut) VALUES (?, ?, ?, ?, 'non_payee')");
        if ($stmt->execute([$_SESSION['user_id'], $reservation_id, $montant, $motif])) {
            setFlashMessage('success', "Amende enregistrée avec succès.");
            header('Location: amendes.php');
            exit();
        } else {
            $error = "Erreur lors de l'enregistrement.";
        }
    }
}

// Check if we came from dashboard with a place_id
$preselected_resid = '';
if (isset($_GET['place'])) {
    // Find active reservation for this place
    $stmt = $pdo->prepare("SELECT id FROM reservations WHERE place_id = ? AND statut = 'active' LIMIT 1");
    $stmt->execute([$_GET['place']]);
    $preselected_resid = $stmt->fetchColumn();
}

// Get recent amendes
$amendes = $pdo->query("
    SELECT a.*, r.code_reservation, u.nom as user_name 
    FROM amendes a 
    JOIN reservations r ON a.reservation_id = r.id 
    JOIN utilisateurs u ON r.utilisateur_id = u.id 
    ORDER BY a.date_amende DESC
")->fetchAll();

// Get active reservations for dropdown
$reservations = $pdo->query("SELECT r.id, r.code_reservation, u.nom, p.numero FROM reservations r JOIN utilisateurs u ON r.utilisateur_id = u.id JOIN places p ON r.place_id = p.id WHERE r.statut = 'active'")->fetchAll();

require_once 'includes/header.php';
?>

<div class="row" style="display: flex; gap: 30px;">
    <div style="flex: 1;">
        <div class="card">
            <h3>Émettre une Amende</h3>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Réservation Concernée</label>
                    <select name="reservation_id" required>
                        <option value="">Sélectionner...</option>
                        <?php foreach ($reservations as $r): ?>
                            <option value="<?php echo $r['id']; ?>" <?php echo ($preselected_resid == $r['id']) ? 'selected' : ''; ?>>
                                <?php echo $r['code_reservation'] . ' - ' . $r['nom'] . ' (Place ' . $r['numero'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Montant (DA)</label>
                    <input type="number" name="montant" required min="500" placeholder="2000">
                </div>
                
                <div class="form-group">
                    <label>Motif</label>
                    <textarea name="motif" rows="3" required placeholder="Dépassement horaire, Mauvais stationnement..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-danger" style="width: 100%;">Enregistrer l'amende</button>
            </form>
        </div>
    </div>
    
    <div style="flex: 2;">
        <h3>Historique des Amendes</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Usager</th>
                        <th>Réservation</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($amendes as $amende): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($amende['date_amende'])); ?></td>
                        <td><?php echo htmlspecialchars($amende['user_name']); ?></td>
                        <td><?php echo $amende['code_reservation']; ?></td>
                        <td><strong><?php echo formatCurrency($amende['montant']); ?></strong></td>
                        <td>
                            <span class="badge badge-<?php echo $amende['statut'] == 'payee' ? 'success' : 'error'; ?>">
                                <?php echo ucfirst($amende['statut']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($amendes)): ?>
                        <tr><td colspan="5" style="text-align: center;">Aucune amende enregistrée.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

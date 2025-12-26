<?php
require_once 'includes/functions.php';
requireRole(['usager', 'premium']);

$userId = $_SESSION['user_id'];

// Fetch active reservations
$stmt = $pdo->prepare("
    SELECT r.*, p.numero as place_numero 
    FROM reservations r 
    JOIN places p ON r.place_id = p.id 
    WHERE r.utilisateur_id = ? AND r.statut = 'active'
    ORDER BY r.date_debut ASC
");
$stmt->execute([$userId]);
$activeReservations = $stmt->fetchAll();

// Fetch history
$stmt = $pdo->prepare("
    SELECT r.*, p.numero as place_numero 
    FROM reservations r 
    JOIN places p ON r.place_id = p.id 
    WHERE r.utilisateur_id = ? AND r.statut != 'active'
    ORDER BY r.date_debut DESC LIMIT 5
");
$stmt->execute([$userId]);
$historyReservations = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2>Tableau de bord</h2>
    <a href="reservation.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle Réservation</a>
</div>

<?php if (checkRole('premium')): ?>
<div class="alert alert-success" style="background: linear-gradient(90deg, #F4F7FE 0%, #E9E3FF 100%); border: 1px solid var(--primary-light); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <i class="fas fa-crown" style="color: gold;"></i> <strong>Status Premium Actif</strong> - Priorité et Services Exclusifs.
    </div>
    <button class="btn btn-sm btn-primary" onclick="alert('Fonctionnalité de facturation groupée à venir !')"><i class="fas fa-file-invoice"></i> Facturation Groupée</button>
</div>
<?php endif; ?>

<h3 style="margin-top: 30px;">Réservations Actives</h3>
<?php if (empty($activeReservations)): ?>
    <div class="card" style="text-align: center; padding: 40px; color: var(--text-secondary);">
        <i class="fas fa-calendar-times" style="font-size: 40px; margin-bottom: 20px;"></i>
        <p>Aucune réservation en cours.</p>
    </div>
<?php else: ?>
    <div class="dashboard-grid">
        <?php foreach ($activeReservations as $res): ?>
            <div class="card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <span class="badge badge-success">Active</span>
                    <span style="font-weight: 700; color: var(--primary);">#<?php echo $res['code_reservation']; ?></span>
                </div>
                <h4 style="margin-bottom: 10px;">Place <?php echo $res['place_numero']; ?></h4>
                <p><i class="fas fa-clock"></i> Du: <?php echo date('d/m/Y H:i', strtotime($res['date_debut'])); ?></p>
                <p><i class="fas fa-flag-checkered"></i> Au: <?php echo date('d/m/Y H:i', strtotime($res['date_fin'])); ?></p>
                
                <div style="margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                     <!-- Logic to pay or cancel could go here -->
                     <button class="btn btn-sm btn-secondary" style="width: 100%;">Voir détails</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h3 style="margin-top: 40px;">Historique Récent</h3>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Place</th>
                <th>Date Début</th>
                <th>Date Fin</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($historyReservations as $res): ?>
            <tr>
                <td><?php echo $res['code_reservation']; ?></td>
                <td><?php echo $res['place_numero']; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($res['date_debut'])); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($res['date_fin'])); ?></td>
                <td>
                    <span class="badge badge-<?php echo $res['statut'] == 'terminee' ? 'info' : ($res['statut'] == 'annulee' ? 'error' : 'secondary'); ?>">
                        <?php echo ucfirst($res['statut']); ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($historyReservations)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Aucun historique.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>

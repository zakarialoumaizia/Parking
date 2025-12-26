<?php
require_once 'includes/functions.php';
requireRole('agent');

// Get all places and their current status
// We need to see if a place is occupied physically (statut db) vs reserved.
// Ideally, the system should know if it matches.
// For this simple app, we list places. If a place is 'occupee' or has an active reservation for NOW, we show it.

$stmt = $pdo->prepare("SELECT * FROM places ORDER BY numero");
$stmt->execute();
$places = $stmt->fetchAll();

// Get current active reservations to map to places
$now = date('Y-m-d H:i:s');
$stmtRes = $pdo->prepare("
    SELECT r.*, u.nom as user_name, u.email 
    FROM reservations r 
    JOIN utilisateurs u ON r.utilisateur_id = u.id 
    WHERE r.statut = 'active' 
    AND r.date_debut <= ? AND r.date_fin >= ?
");
$stmtRes->execute([$now, $now]);
$activeReservations = $stmtRes->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE); 
// Group by reservation ID is not useful here, we want Key as Place ID. But fetchAll limit.
// Let's iterate.
$reservationsByPlace = [];
foreach ($pdo->query("SELECT r.*, u.nom as user_name FROM reservations r JOIN utilisateurs u ON r.utilisateur_id = u.id WHERE r.statut='active' AND '$now' BETWEEN r.date_debut AND r.date_fin")->fetchAll() as $res) {
    $reservationsByPlace[$res['place_id']] = $res;
}

require_once 'includes/header.php';
?>

<h2>Contrôle du Parking</h2>

<div class="dashboard-grid">
    <?php foreach ($places as $place): ?>
        <?php 
            $res = isset($reservationsByPlace[$place['id']]) ? $reservationsByPlace[$place['id']] : null;
            $statusColor = 'success'; // Libre
            $statusText = 'Libre';
            
            if ($res) {
                if (strpos($res['code_reservation'], 'ADMIN-') === 0) {
                     $statusColor = 'warning'; // Admin Reserved
                     $statusText = 'RÉSERVÉE ADMIN (' . htmlspecialchars($res['user_name']) . ')';
                } else {
                     $statusColor = 'primary'; // Normal User
                     $statusText = 'Réservée par ' . htmlspecialchars($res['user_name']);
                }
            } elseif ($place['statut'] === 'indisponible') {
                $statusColor = 'warning'; // Bloquée Admin
                $statusText = 'Bloquée (Admin)';
            } elseif ($place['statut'] === 'occupee') {
                $statusColor = 'error'; // Occupée sans resa (Anomalie)
                $statusText = 'Map Occupée (Anomalie ?)';
            }
        ?>
        <div class="card" style="border-left: 5px solid var(--<?php echo $statusColor; ?>);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <h3 style="margin: 0;">Place <?php echo $place['numero']; ?></h3>
                <span class="badge badge-<?php echo $statusColor; ?>"><?php echo $place['type']; ?></span>
            </div>
            
            <p style="margin-top: 10px; color: var(--text-main);">
                <strong>Statut : </strong> <?php echo $statusText; ?>
            </p>
            
            <?php if ($res): ?>
                <p style="font-size: 12px; color: var(--text-secondary);">
                    Fin : <?php echo date('H:i', strtotime($res['date_fin'])); ?>
                </p>
            <?php endif; ?>
            
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <?php if ($res): ?>
                     <button class="btn btn-sm btn-secondary">Vérifier</button>
                <?php else: ?>
                    <!-- If no reservation but car is there, issue fine -->
                     <a href="amendes.php?place=<?php echo $place['id']; ?>" class="btn btn-sm btn-danger">Infraction</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

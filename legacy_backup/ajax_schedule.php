<?php
require_once 'includes/functions.php';
requireRole(['admin', 'agent']);

if (!isset($_GET['id'])) {
    die("ID manquant");
}

$placeId = $_GET['id'];

// Fetch Place Details
$stmt = $pdo->prepare("SELECT * FROM places WHERE id = ?");
$stmt->execute([$placeId]);
$place = $stmt->fetch();

if (!$place) {
    die("Place introuvable");
}

// Fetch Future Reservations
$stmtRes = $pdo->prepare("
    SELECT r.*, u.nom 
    FROM reservations r 
    JOIN utilisateurs u ON r.utilisateur_id = u.id 
    WHERE r.place_id = ? 
    AND r.date_fin >= NOW() 
    AND r.statut = 'active'
    ORDER BY r.date_debut ASC
");
$stmtRes->execute([$placeId]);
$reservations = $stmtRes->fetchAll();

?>
<div style="padding: 20px;">
    <h3 style="margin-bottom: 20px; color: var(--primary);">Planning : Place <?php echo htmlspecialchars($place['numero']); ?></h3>
    
    <?php if (empty($reservations)): ?>
        <p>Aucune réservation future.</p>
    <?php else: ?>
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f4f7fe; text-align: left;">
                    <th style="padding: 10px;">Client</th>
                    <th style="padding: 10px;">Début</th>
                    <th style="padding: 10px;">Fin</th>
                    <th style="padding: 10px;">Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <?php 
                        $isAdmin = strpos($res['code_reservation'], 'ADMIN-') === 0;
                        $bg = $isAdmin ? '#fff3cd' : '#fff'; // Yellow for admin
                    ?>
                    <tr style="border-bottom: 1px solid #eee; background: <?php echo $bg; ?>">
                        <td style="padding: 10px;">
                            <?php echo $isAdmin ? '<strong>ADMIN</strong>' : htmlspecialchars($res['nom']); ?>
                        </td>
                        <td style="padding: 10px;"><?php echo date('d/m/Y H:i', strtotime($res['date_debut'])); ?></td>
                        <td style="padding: 10px;"><?php echo date('d/m/Y H:i', strtotime($res['date_fin'])); ?></td>
                        <td style="padding: 10px;">
                            <?php echo $isAdmin ? 'Blocage' : 'Réservation'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

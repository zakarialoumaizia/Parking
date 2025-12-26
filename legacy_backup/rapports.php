<?php
require_once 'includes/functions.php';
requireRole('admin');

// Revenue by Type
$revByType = $pdo->query("
    SELECT p.type, SUM(pa.montant) as total 
    FROM paiements pa 
    JOIN reservations r ON pa.reservation_id = r.id 
    JOIN places p ON r.place_id = p.id 
    WHERE pa.statut = 'paye' 
    GROUP BY p.type
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Revenue this Month
$revMonth = $pdo->query("
    SELECT DATE_FORMAT(date_paiement, '%Y-%m') as mois, SUM(montant) as total 
    FROM paiements 
    WHERE statut = 'paye' 
    GROUP BY mois 
    ORDER BY mois DESC LIMIT 6
")->fetchAll();

require_once 'includes/header.php';
?>

<h2>Rapports et Statistiques</h2>

<div class="dashboard-grid">
    <div class="card">
        <h3>Revenus par Type de Place</h3>
        <table class="table" style="margin-top: 20px;">
            <?php foreach ($revByType as $type => $amount): ?>
            <tr>
                <td><?php echo ucfirst($type); ?></td>
                <td style="text-align: right; font-weight: bold;"><?php echo formatCurrency($amount); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($revByType)): ?>
                <tr><td colspan="2">Pas de données.</td></tr>
            <?php endif; ?>
        </table>
    </div>
    
    <div class="card">
        <h3>Évolution Mensuelle</h3>
         <table class="table" style="margin-top: 20px;">
            <?php foreach ($revMonth as $row): ?>
            <tr>
                <td><?php echo $row['mois']; ?></td>
                <td style="text-align: right; font-weight: bold;"><?php echo formatCurrency($row['total']); ?></td>
            </tr>
            <?php endforeach; ?>
             <?php if (empty($revMonth)): ?>
                <tr><td colspan="2">Pas de données.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<div class="card" style="margin-top: 30px;">
    <h3>Détail des Paiements</h3>
    <?php
    $paiements = $pdo->query("
        SELECT pa.*, r.code_reservation, u.nom 
        FROM paiements pa 
        JOIN reservations r ON pa.reservation_id = r.id 
        JOIN utilisateurs u ON r.utilisateur_id = u.id 
        ORDER BY pa.date_paiement DESC LIMIT 20
    ")->fetchAll();
    ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Code Réservation</th>
                    <th>Utilisateur</th>
                    <th>Mode</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paiements as $p): ?>
                <tr>
                    <td><?php echo $p['date_paiement']; ?></td>
                    <td><?php echo $p['code_reservation']; ?></td>
                    <td><?php echo htmlspecialchars($p['nom']); ?></td>
                    <td><?php echo $p['mode']; ?></td>
                    <td><?php echo formatCurrency($p['montant']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

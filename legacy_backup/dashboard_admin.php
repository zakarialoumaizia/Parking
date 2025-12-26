<?php
require_once 'includes/functions.php';
requireRole('admin');

// Stats
// 1. Total Users
$nbUsers = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role IN ('usager', 'premium')")->fetchColumn();

// 2. Today's Reservations
$nbResToday = $pdo->query("SELECT COUNT(*) FROM reservations WHERE DATE(date_debut) = CURDATE()")->fetchColumn();

// 3. Occupancy Rate (Active Reservations / Total Places)
$totalPlaces = $pdo->query("SELECT COUNT(*) FROM places")->fetchColumn();
$activeRes = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'active'")->fetchColumn();
$occupancy = $totalPlaces > 0 ? round(($activeRes / $totalPlaces) * 100) : 0;

// 4. Revenue (Total Paid)
$revenue = $pdo->query("SELECT SUM(montant) FROM paiements WHERE statut = 'paye'")->fetchColumn();

require_once 'includes/header.php';
?>

<div class="row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2>Administration</h2>
    <div style="display: flex; gap: 10px;">
        <a href="gestion_places.php" class="btn btn-secondary">Gérer Places</a>
        <a href="rapports.php" class="btn btn-primary">Rapports Complets</a>
    </div>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-details">
            <h4>Utilisateurs</h4>
            <p><?php echo $nbUsers; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-details">
            <h4>Réservations (24h)</h4>
            <p><?php echo $nbResToday; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-parking"></i></div>
        <div class="stat-details">
            <h4>Occupation</h4>
            <p><?php echo $occupancy; ?>%</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-coins"></i></div>
        <div class="stat-details">
            <h4>Revenus</h4>
            <p><?php echo formatCurrency($revenue ?? 0); ?></p>
        </div>
    </div>
</div>

<div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
    <div class="card">
        <h3>Dernières Activités</h3>
        <!-- List last 5 reservations -->
        <?php
        $lastRes = $pdo->query("SELECT r.*, u.nom FROM reservations r JOIN utilisateurs u ON r.utilisateur_id = u.id ORDER BY r.id DESC LIMIT 5")->fetchAll();
        ?>
        <table style="margin-top: 15px;">
             <thead>
                 <tr>
                     <th>Utilisateur</th>
                     <th>Date</th>
                     <th>Statut</th>
                 </tr>
             </thead>
             <tbody>
                 <?php foreach ($lastRes as $r): ?>
                 <tr>
                     <td><?php echo htmlspecialchars($r['nom']); ?></td>
                     <td><?php echo date('d/m H:i', strtotime($r['date_debut'])); ?></td>
                     <td><span class="badge badge-primary"><?php echo $r['statut']; ?></span></td>
                 </tr>
                 <?php endforeach; ?>
             </tbody>
        </table>
    </div>
    
    <div class="card">
        <h3>Actions Rapides</h3>
        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
            <a href="tarifs.php" class="btn btn-secondary" style="justify-content: start;"><i class="fas fa-tags"></i> Modifier les Tarifs</a>
            <a href="gestion_utilisateurs.php" class="btn btn-secondary" style="justify-content: start;"><i class="fas fa-user-cog"></i> Gérer Utilisateurs</a>
            <a href="configuration.php" class="btn btn-secondary" style="justify-content: start;"><i class="fas fa-cogs"></i> Configuration Système</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

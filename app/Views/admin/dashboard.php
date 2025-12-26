<?php require_once '../app/Views/includes/header.php'; ?>

<div class="page-header">
    <h2>Administration</h2>
    <div class="header-actions">
        <a href="<?php echo url('admin/places'); ?>" class="btn btn-secondary">Gérer Places</a>
        <a href="<?php echo url('admin/rapports'); ?>" class="btn btn-primary">Rapports Complets</a>
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

<div class="content-grid">
    <div class="card">
        <h3>Dernières Activités</h3>
        <div class="table-container">
            <table>
                 <thead>
                     <tr>
                         <th>Utilisateur</th>
                         <th>Date</th>
                         <th>Statut</th>
                     </tr>
                 </thead>
                 <tbody>
                     <?php foreach ($lastActivity as $r): ?>
                     <tr>
                         <td><?php echo htmlspecialchars($r['nom']); ?></td>
                         <td><?php echo date('d/m H:i', strtotime($r['date_debut'])); ?></td>
                         <td><span class="badge badge-primary"><?php echo $r['statut']; ?></span></td>
                     </tr>
                     <?php endforeach; ?>
                 </tbody>
            </table>
        </div>
    </div>
    
    <div class="card">
        <h3>Actions Rapides</h3>
        <div class="action-list">
            <a href="<?php echo url('admin/tarifs'); ?>" class="btn btn-secondary" style="justify-content: start;"><i class="fas fa-tags"></i> Modifier les Tarifs</a>
            <a href="<?php echo url('admin/users'); ?>" class="btn btn-secondary" style="justify-content: start;"><i class="fas fa-user-cog"></i> Gérer Utilisateurs</a>
            <a href="<?php echo url('admin/config'); ?>" class="btn btn-secondary" style="justify-content: start;"><i class="fas fa-cogs"></i> Configuration Système</a>
        </div>
    </div>
</div>

<?php require_once '../app/Views/includes/footer.php'; ?>

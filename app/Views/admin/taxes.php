<?php require_once '../app/Views/includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-gavel"></i> Gestion des Amendes & Pénalités</h2>
</div>

<div class="content-grid">
    <div class="card">
        <div class="card-header-flex">
            <h3 class="section-title mb-0">Configuration des Tarifs</h3>
        </div>
        <form method="POST">
             <div class="dashboard-grid" style="margin-bottom: 0;">
                <?php foreach ($taxesConfig as $slug => $tax): ?>
                     <div class="form-group">
                         <label><?php echo htmlspecialchars($tax['nom']); ?> (DA)</label>
                         <input type="number" step="0.01" name="taxe[<?php echo $slug; ?>]" value="<?php echo $tax['montant']; ?>" class="form-control">
                     </div>
                <?php endforeach; ?>
             </div>
             <div class="card-footer" style="text-align: right; border-top: none; padding-top: 0;">
                 <button type="submit" name="update_taxes" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
             </div>
        </form>
    </div>

    <div class="stat-card">
         <div class="stat-icon">
             <i class="fas fa-coins"></i>
         </div>
         <div class="stat-details">
             <h4>Revenu Total</h4>
             <p class="text-success"><?php echo formatCurrency($totalRevenue); ?></p>
         </div>
    </div>
</div>

<!-- Fines Registry -->
<div class="card" style="margin-bottom: 30px;">
    <h3><i class="fas fa-gavel"></i> Registre des Amendes</h3>
    <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Historique des pénalités appliquées.</p>
    
    <?php if (empty($amendes)): ?>
        <div style="padding: 20px; text-align: center; background: #f9f9f9; border-radius: 8px;">Aucune amende enregistrée.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Utilisateur / Place</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Motif</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($amendes as $amende): ?>
                <tr>
                    <td>
                        <?php 
                            if (!empty($amende['user_nom'])) {
                                echo "<strong>" . htmlspecialchars($amende['user_nom']) . "</strong>";
                            } else {
                                echo "<span style='color: #888;'>Inconnu (Place ".$amende['place_numero'].")</span>";
                            }
                        ?>
                    </td>
                    <td style="color: var(--error); font-weight: bold;"><?php echo formatCurrency($amende['montant']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($amende['date_amende'])); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $amende['statut'] == 'payee' ? 'success' : 'error'; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $amende['statut'])); ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="showMotif('<?php echo addslashes($amende['motif']); ?>')">
                            <i class="fas fa-eye"></i> Lire
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Recent Transactions (Simplified) -->
<div class="card">
    <h3>Derniers Paiements</h3>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Montant</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?php echo $t['code_reservation']; ?></td>
                <td><?php echo formatCurrency($t['montant']); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($t['date_paiement'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="motifModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; width: 90%; text-align: center;">
        <h3 style="margin-bottom: 20px;">Motif de l'amende</h3>
        <p id="motifText" style="font-size: 16px; line-height: 1.5; color: #333; margin-bottom: 30px;"></p>
        <button onclick="closeMotif()" class="btn btn-primary">Fermer</button>
    </div>
</div>

<script>
function showMotif(text) {
    document.getElementById('motifText').innerText = text;
    document.getElementById('motifModal').style.display = 'flex';
}
function closeMotif() {
    document.getElementById('motifModal').style.display = 'none';
}
</script>

<?php require_once '../app/Views/includes/footer.php'; ?>

<?php require_once '../app/Views/includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-tags"></i> Configuration des Tarifs</h2>
</div>

<div class="content-grid">
    <div class="card">
        <div class="card-header-flex">
            <h3 class="section-title mb-0">Tarification Horaire</h3>
        </div>
        
        <form method="POST">
            <div class="dashboard-grid" style="margin-bottom: 0;">
                <div class="form-group">
                    <label><i class="fas fa-car"></i> Standard (DA/h)</label>
                    <input type="number" name="prix[standard]" step="0.01" class="form-control" value="<?php echo $tarifs['standard'] ?? 0; ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-wheelchair"></i> PMR (DA/h)</label>
                    <input type="number" name="prix[PMR]" step="0.01" class="form-control" value="<?php echo $tarifs['PMR'] ?? 0; ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-crown"></i> VIP (DA/h)</label>
                    <input type="number" name="prix[VIP]" step="0.01" class="form-control" value="<?php echo $tarifs['VIP'] ?? 0; ?>">
                </div>
            </div>
            
            <div class="card-footer" style="text-align: right; border-top: none; padding-top: 0;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer les modifications</button>
            </div>
        </form>
    </div>
    
    <div class="card">
        <h3 class="section-title"><i class="fas fa-info-circle"></i> Information</h3>
        <p class="text-secondary">Ces tarifs s'appliquent automatiquement à toutes les nouvelles réservations. </p>
        <ul style="padding-left: 1.5rem; color: var(--text-secondary); line-height: 1.8;">
             <li><strong>Standard</strong>: Places régulières.</li>
             <li><strong>PMR</strong>: Places réservées aux personnes à mobilité réduite.</li>
             <li><strong>VIP</strong>: Places larges avec surveillance dédiée.</li>
        </ul>
    </div>
</div>

<?php require_once '../app/Views/includes/footer.php'; ?>

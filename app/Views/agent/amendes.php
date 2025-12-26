<?php require_once '../app/Views/includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-exclamation-triangle"></i> Émettre une Amende</h2>
    <a href="<?php echo url('agent/dashboard'); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<div class="content-grid">
    <div class="card">
        <div class="card-header-flex">
            <h3 class="section-title mb-0">Détails de l'infraction</h3>
            <?php if (!empty($currentReservation['place_num'])): ?>
                <span class="badge badge-primary" style="font-size: 1rem;">Place <?php echo htmlspecialchars($currentReservation['place_num']); ?></span>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($currentReservation)): ?>
            <div class="alert alert-info mb-4" style="border-left: 4px solid var(--info); background: rgba(17, 205, 239, 0.05);">
                <h4 style="color: var(--info); margin-bottom: 0.5rem; font-size: 0.9rem;"><i class="fas fa-user-circle"></i> Occupant Actuel</h4>
                <div style="display: flex; gap: 2rem; flex-wrap: wrap; font-size: 0.9rem;">
                    <div><strong>Nom:</strong> <?php echo htmlspecialchars($currentReservation['nom']); ?></div>
                    <div><strong>Email:</strong> <?php echo htmlspecialchars($currentReservation['email']); ?></div>
                    <div><strong>Début:</strong> <?php echo date('d/m H:i', strtotime($currentReservation['date_debut'])); ?></div>
                </div>
            </div>
        <?php else: ?>
             <div class="alert alert-warning mb-4" style="font-size: 0.9rem;">
                 <i class="fas fa-exclamation-triangle"></i> Attention: Aucune réservation active détectée.
             </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="place_id" value="<?php echo htmlspecialchars($_GET['place'] ?? ''); ?>">
            
            <div class="dashboard-grid" style="margin-bottom: 0;">
                <div class="form-group">
                    <label>Place Concernée</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($_GET['place'] ?? 'Non spécifiée'); ?>" readonly style="background: var(--bg-body);">
                </div>
                
                <div class="form-group">
                    <label>Type d'infraction</label>
                    <select name="type_amende" class="form-control" required onchange="updateMontant(this)">
                        <option value="">Sélectionner...</option>
                        <?php foreach ($types_amende as $tax): ?>
                            <option value="<?php echo $tax['slug']; ?>" data-prix="<?php echo $tax['montant']; ?>">
                                <?php echo htmlspecialchars($tax['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="autre" data-prix="0">Autre / Personnalisé</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Montant (DA)</label>
                    <input type="number" name="montant" id="montantInput" step="0.01" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group mt-4">
                <label>Description / Motif détaillé</label>
                <textarea name="motif" class="form-control" rows="4" placeholder="Description de l'infraction..."></textarea>
            </div>
            
            <div class="card-footer text-right">
                <button type="submit" name="submit_amende" class="btn btn-danger btn-block">
                    <i class="fas fa-gavel"></i> Confirmer l'amende
                </button>
            </div>
        </form>
    </div>
    
    <div class="card">
        <h3 class="section-title"><i class="fas fa-info-circle"></i> Procédure</h3>
        <ul style="padding-left: 1.5rem; line-height: 1.8; color: var(--text-secondary);">
            <li>Vérifiez le numéro de place avant de valider.</li>
            <li>Si le véhicule a dépassé le temps, sélectionnez <strong>"Dépassement de temps"</strong>.</li>
            <li>Pour stationnement gênant ou non autorisé, utilisez les options appropriées.</li>
            <li>Une notification sera envoyée au propriétaire si identifié.</li>
        </ul>
    </div>
</div>

<script>
function updateMontant(select) {
    const option = select.options[select.selectedIndex];
    const prix = option.getAttribute('data-prix');
    const input = document.getElementById('montantInput');
    
    if (prix && prix !== '0') {
        input.value = prix;
        // input.readOnly = true; 
    } else {
        input.value = '';
        input.readOnly = false;
    }
}
</script>

<?php require_once '../app/Views/includes/footer.php'; ?>

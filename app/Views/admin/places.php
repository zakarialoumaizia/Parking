<?php require_once '../app/Views/includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-parking"></i> Gestion des Places</h2>
    <a href="javascript:void(0)" onclick="openModal('addPlaceModal')" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter Place</a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Prix Spécifique</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($places as $place): ?>
                <tr>
                    <td><strong><?php echo $place['numero']; ?></strong></td>
                    <td><span class="badge badge-info"><?php echo $place['type']; ?></span></td>
                    <td>
                        <?php 
                            $color = 'success';
                            if ($place['statut'] == 'occupee') $color = 'error';
                            if ($place['statut'] == 'indisponible') $color = 'warning';
                        ?>
                        <span class="badge badge-<?php echo $color; ?>"><?php echo ucfirst($place['statut']); ?></span>
                    </td>
                    <td>
                        <?php echo $place['prix_custom'] ? formatCurrency($place['prix_custom']) : '-'; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter Place -->
<div id="addPlaceModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 400px; width: 90%;">
        <h3 class="section-title">Ajouter une Place</h3>
        <form method="POST">
             <input type="hidden" name="add_place" value="1">
             <div class="form-group">
                 <label>Numéro de Place</label>
                 <input type="text" name="numero" required placeholder="Ex: A12" class="form-control">
             </div>
             <div class="form-group">
                 <label>Type de Place</label>
                 <select name="type" class="form-control">
                     <option value="standard">Standard</option>
                     <option value="PMR">PMR</option>
                     <option value="VIP">VIP</option>
                 </select>
             </div>
             <div class="d-flex justify-between mt-4">
                 <button type="button" class="btn btn-secondary" onclick="closeModal('addPlaceModal')">Annuler</button>
                 <button type="submit" class="btn btn-primary">Ajouter</button>
             </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = 'none';
    }
}
</script>

<?php require_once '../app/Views/includes/footer.php'; ?>

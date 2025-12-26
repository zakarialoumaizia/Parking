<?php require_once '../app/Views/includes/header.php'; ?>

<div class="page-header">
    <h2><i class="fas fa-users"></i> Gestion des Utilisateurs</h2>
    <a href="#" class="btn btn-primary"><i class="fas fa-user-plus"></i> Créer Utilisateur</a>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
         <div style="flex: 1; min-width: 200px; position: relative;">
             <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-secondary);"></i>
             <input type="text" placeholder="Rechercher nom, email..." style="padding-left: 2.5rem; width: 100%;">
         </div>
         <div style="min-width: 150px;">
             <select>
                 <option value="">Tous les rôles</option>
                 <option value="usager">Usager</option>
                 <option value="premium">Premium</option>
                 <option value="agent">Agent</option>
                 <option value="admin">Administrateur</option>
             </select>
         </div>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Date Création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['nom']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo ($user['role'] == 'admin' ? 'error' : ($user['role'] == 'agent' ? 'warning' : 'primary')); ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td><?php echo isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '-'; ?></td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="openEditUserModal('<?php echo $user['id']; ?>', '<?php echo $user['role']; ?>', '<?php echo addslashes($user['nom']); ?>')"><i class="fas fa-edit"></i></button>
                        <!-- Don't allow deleting self strictly -->
                        <?php if (isset($_SESSION['user_email']) && $user['email'] !== $_SESSION['user_email']): ?>
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Modifier Utilisateur -->
<div id="editUserModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 400px; width: 90%;">
        <h3 class="section-title">Modifier Rôle Utilisateur</h3>
        <form method="POST">
             <input type="hidden" name="update_role" value="1">
             <input type="hidden" name="user_id" id="edit_user_id">
             
             <p style="margin-bottom: 1rem;">Utilisateur: <strong id="edit_user_name"></strong></p>
             
             <div class="form-group">
                 <label>Rôle</label>
                 <select name="role" id="edit_user_role" class="form-control">
                     <option value="usager">Usager</option>
                     <option value="premium">Premium</option>
                 </select>
             </div>
             
             <div class="d-flex justify-between mt-4">
                 <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Annuler</button>
                 <button type="submit" class="btn btn-primary">Enregistrer</button>
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
function openEditUserModal(id, role, name) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_user_role').value = role;
    document.getElementById('edit_user_name').innerText = name;
    openModal('editUserModal');
}
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = 'none';
    }
}
</script>

<?php require_once '../app/Views/includes/footer.php'; ?>

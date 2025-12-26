<?php require_once '../app/Views/includes/header.php'; ?>

<h2>Envoyer des Notifications</h2>

<div class="row">
    <div class="card" style="margin-top: 20px;">
        <form method="POST">
            <div class="form-group">
                <label>Groupe Cible</label>
                <select name="target_group" id="target" onchange="toggleUsers()">
                    <option value="all">Tous les utilisateurs</option>
                    <option value="premium">Clients Premium uniquement</option>
                    <option value="single">Utilisateur Spécifique</option>
                </select>
            </div>
            
            <div class="form-group" id="user_select" style="display: none;">
                <label>Sélectionner l'utilisateur</label>
                <select name="user_id">
                    <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo $u['nom'] . ' (' . $u['email'] . ')'; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Sujet</label>
                <input type="text" name="subject" required placeholder="Ex: Maintenance Parking...">
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="5" required placeholder="Votre message ici..."></textarea>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="send_email" value="1" checked> Envoyer aussi par Email
                </label>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer Notification</button>
        </form>
    </div>
</div>

<script>
function toggleUsers() {
    var val = document.getElementById('target').value;
    var div = document.getElementById('user_select');
    if (val === 'single') {
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}
</script>

<?php require_once '../app/Views/includes/footer.php'; ?>

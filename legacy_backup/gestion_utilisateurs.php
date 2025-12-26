<?php
require_once 'includes/functions.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        // Prevent deleting self
        if ($id != $_SESSION['user_id']) {
            $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$id]);
            setFlashMessage('success', "Utilisateur supprimé.");
        }
    } else {
        // Update Role
        $id = $_POST['id'];
        $role = $_POST['role'];
        if ($id != $_SESSION['user_id']) {
            $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?")->execute([$role, $id]);
            setFlashMessage('success', "Rôle mis à jour.");
        }
    }
    header('Location: gestion_utilisateurs.php');
    exit();
}

$users = $pdo->query("SELECT * FROM utilisateurs ORDER BY date_creation DESC")->fetchAll();

require_once 'includes/header.php';
?>

<h2>Gestion des Utilisateurs</h2>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle Actuel</th>
                <th>Modifier Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo htmlspecialchars($u['nom']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td>
                    <span class="badge badge-<?php echo $u['role'] == 'admin' ? 'primary' : ($u['role'] == 'agent' ? 'info' : ($u['role'] == 'premium' ? 'warning' : 'secondary')); ?>">
                        <?php echo ucfirst($u['role']); ?>
                    </span>
                </td>
                <td>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                    <form method="POST" style="display: flex; gap: 5px;">
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                        <select name="role" style="padding: 5px; font-size: 12px; width: auto;">
                            <option value="usager" <?php echo $u['role'] == 'usager' ? 'selected' : ''; ?>>Usager</option>
                            <option value="premium" <?php echo $u['role'] == 'premium' ? 'selected' : ''; ?>>Premium</option>
                            <option value="agent" <?php echo $u['role'] == 'agent' ? 'selected' : ''; ?>>Agent</option>
                            <option value="admin" <?php echo $u['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-secondary"><i class="fas fa-save"></i></button>
                    </form>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                    <form method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>

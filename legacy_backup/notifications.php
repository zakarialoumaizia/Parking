<?php
require_once 'includes/functions.php';
requireLogin();

if (isset($_POST['mark_read'])) {
    markNotificationsRead($_SESSION['user_id']);
    header('Location: notifications.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE utilisateur_id = ? ORDER BY date_notification DESC LIMIT 50");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Mes Notifications</h2>
    <?php if (getUnreadNotificationsCount($_SESSION['user_id']) > 0): ?>
        <form method="POST">
            <button type="submit" name="mark_read" class="btn btn-sm btn-secondary">Tout marquer comme lu</button>
        </form>
    <?php endif; ?>
</div>

<div class="dashboard-grid" style="grid-template-columns: 1fr;">
    <?php foreach ($notifications as $notif): ?>
        <div class="card" style="border-left: 4px solid <?php echo $notif['lu'] ? 'var(--border-color)' : 'var(--primary)'; ?>; opacity: <?php echo $notif['lu'] ? '0.7' : '1'; ?>;">
            <div style="display: flex; justify-content: space-between;">
                <p style="margin: 0; color: var(--text-main); font-weight: <?php echo $notif['lu'] ? 'normal' : 'bold'; ?>;">
                    <?php echo htmlspecialchars($notif['message']); ?>
                </p>
                <small style="color: var(--text-secondary);">
                    <?php echo date('d/m/Y H:i', strtotime($notif['date_notification'])); ?>
                </small>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($notifications)): ?>
        <div class="card" style="text-align: center;">
            <p>Aucune notification.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
require_once 'includes/functions.php';
requireRole('admin');

$error = '';
$success = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'delete') {
            $id = $_POST['id'];
            $pdo->prepare("DELETE FROM places WHERE id = ?")->execute([$id]);
            setFlashMessage('success', "Place supprimée.");
        } elseif ($_POST['action'] === 'toggle_status') {
            $id = $_POST['id'];
            $current = $_POST['current_status'];
            
            if ($current === 'indisponible') {
                // UNBLOCK
                $pdo->prepare("UPDATE places SET statut = 'libre' WHERE id = ?")->execute([$id]);
                // Cancel Admin Reservation
                $pdo->prepare("UPDATE reservations SET statut = 'terminee' WHERE place_id = ? AND code_reservation LIKE 'ADMIN-%'")->execute([$id]);
                setFlashMessage('success', "Place débloquée (Libre).");
            } else {
                // BLOCK (Reserve for Admin)
                // 1. Update Status
                $pdo->prepare("UPDATE places SET statut = 'indisponible' WHERE id = ?")->execute([$id]);
                
                // 2. Create Reservation Record
                $code = 'ADMIN-' . strtoupper(uniqid());
                $start = date('Y-m-d H:i:s');
                $end = date('Y-m-d H:i:s', strtotime('+10 years')); // Long term block
                
                $stmt = $pdo->prepare("INSERT INTO reservations (code_reservation, utilisateur_id, place_id, date_debut, date_fin, statut) VALUES (?, ?, ?, ?, ?, 'active')");
                $stmt->execute([$code, $_SESSION['user_id'], $id, $start, $end]);
                
                setFlashMessage('success', "Place réservée pour l'administrateur.");
            }
        } elseif ($_POST['action'] === 'update_price') {
             $id = $_POST['id'];
             $price = !empty($_POST['price']) ? $_POST['price'] : null;
             $pdo->prepare("UPDATE places SET prix_custom = ? WHERE id = ?")->execute([$price, $id]);
             setFlashMessage('success', "Prix spécifique mis à jour.");
        }
        header('Location: gestion_places.php');
        exit();
    } elseif (isset($_POST['add'])) {
        $numero = sanitize($_POST['numero']);
        $type = $_POST['type'];
        $prix = !empty($_POST['prix']) ? $_POST['prix'] : null;
        
        $check = $pdo->prepare("SELECT id FROM places WHERE numero = ?");
        $check->execute([$numero]);
        if ($check->fetch()) {
            $error = "Ce numéro existe déjà.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO places (numero, type, statut, prix_custom) VALUES (?, ?, 'libre', ?)");
            $stmt->execute([$numero, $type, $prix]);
            setFlashMessage('success', "Place ajoutée.");
            header('Location: gestion_places.php');
            exit();
        }
    }
}

$sql = "
    SELECT p.*, 
           r.id as res_id, r.date_debut, r.date_fin, u.nom as client_name
    FROM places p 
    LEFT JOIN reservations r ON p.id = r.place_id 
        AND r.statut = 'active' 
        AND NOW() BETWEEN r.date_debut AND r.date_fin
    LEFT JOIN utilisateurs u ON r.utilisateur_id = u.id
    ORDER BY p.numero
";
$places = $pdo->query($sql)->fetchAll();

require_once 'includes/header.php';
?>

<h2>Gestion des Places</h2>

<div class="row" style="display: flex; gap: 30px;">
    <div style="flex: 1; max-width: 350px;">
        <div class="card">
            <h3>Ajouter une Place</h3>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="add" value="1">
                <div class="form-group">
                    <label>Numéro de Place</label>
                    <input type="text" name="numero" required placeholder="A-01">
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type">
                        <option value="standard">Standard</option>
                        <option value="PMR">PMR</option>
                        <option value="VIP">VIP</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Prix Spécifique (Optionnel)</label>
                    <input type="number" name="prix" step="0.01" placeholder="Ex: 500 (DA)">
                    <small>Laisser vide pour utiliser le tarif par type.</small>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Ajouter</button>
            </form>
        </div>
    </div>
    
    <div style="flex: 2;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Type</th>
                        <th>Prix Space</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($places as $p): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($p['numero']); ?></strong></td>
                        <td><?php echo $p['type']; ?></td>
                        <td>
                            <form method="POST" style="display: flex; gap: 5px;">
                                <input type="hidden" name="action" value="update_price">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <input type="number" name="price" value="<?php echo $p['prix_custom']; ?>" step="0.01" style="width: 80px; padding: 5px; font-size: 12px;" placeholder="Auto">
                                <button class="btn btn-sm btn-secondary"><i class="fas fa-save"></i></button>
                            </form>
                        </td>
                        <td>
                            <?php if ($p['statut'] === 'indisponible'): ?>
                                <span class="badge badge-error">Réservée par Admin</span>
                            <?php elseif ($p['res_id']): ?>
                                <span class="badge badge-primary">Occupée</span>
                            <?php else: ?>
                                <span class="badge badge-<?php echo $p['statut'] == 'libre' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($p['statut']); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button type="button" class="btn btn-sm btn-info" onclick="openSchedule(<?php echo $p['id']; ?>)" title="Voir Planning">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                                
                                <form method="POST">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                    <input type="hidden" name="current_status" value="<?php echo $p['statut']; ?>">
                                    <?php if ($p['statut'] === 'indisponible'): ?>
                                        <button class="btn btn-sm btn-success" title="Débloquer"><i class="fas fa-unlock"></i></button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-warning" title="Bloquer (Réserver Admin)"><i class="fas fa-lock"></i></button>
                                    <?php endif; ?>
                                </form>
                                
                                <form method="POST" onsubmit="return confirm('Confirmer la suppression ?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Structure -->
<div id="scheduleModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; width: 500px; max-width: 90%; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
        <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">Planning</h3>
            <button onclick="document.getElementById('scheduleModal').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        </div>
        <div id="modalContent" style="max-height: 400px; overflow-y: auto;">
            <p style="padding: 20px;">Chargement...</p>
        </div>
    </div>
</div>

<script>
function openSchedule(id) {
    const modal = document.getElementById('scheduleModal');
    const content = document.getElementById('modalContent');
    
    modal.style.display = 'flex';
    content.innerHTML = '<p style="padding: 20px; text-align: center;">Chargement...</p>';
    
    fetch('ajax_schedule.php?id=' + id)
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(err => {
            content.innerHTML = '<p style="padding: 20px; color: red;">Erreur de chargement.</p>';
        });
}

// Close on outside click
window.onclick = function(event) {
    const modal = document.getElementById('scheduleModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>

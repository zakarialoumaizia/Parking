<?php
require_once 'includes/functions.php';

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hasRole('admin')) {
    // Single general price
    $generalPrice = floatval($_POST['prix_general']);
    $vipPrice = floatval($_POST['prix_vip']);

    // Update Standard and PMR to general price
    foreach (['standard', 'PMR'] as $type) {
        $stmt = $pdo->prepare("SELECT id FROM tarifs WHERE type_place = ?");
        $stmt->execute([$type]);
        if ($stmt->fetch()) {
            $update = $pdo->prepare("UPDATE tarifs SET prix_heure = ? WHERE type_place = ?");
            $update->execute([$generalPrice, $type]);
        } else {
            $insert = $pdo->prepare("INSERT INTO tarifs (type_place, prix_heure) VALUES (?, ?)");
            $insert->execute([$type, $generalPrice]);
        }
    }
    
    // Update VIP
    $stmt = $pdo->prepare("SELECT id FROM tarifs WHERE type_place = 'VIP'");
    $stmt->execute();
    if ($stmt->fetch()) {
        $update = $pdo->prepare("UPDATE tarifs SET prix_heure = ? WHERE type_place = 'VIP'");
        $update->execute([$vipPrice]);
    } else {
        $insert = $pdo->prepare("INSERT INTO tarifs (type_place, prix_heure) VALUES ('VIP', ?)");
        $insert->execute([$vipPrice]);
    }
    
    setFlashMessage('success', "Tarifs mis à jour avec succès (Standard = Unique, VIP = Spécifique).");
    header('Location: tarifs.php');
    exit();
}

// Fetch Tarifs
$tarifs = [];
$stmt = $pdo->query("SELECT * FROM tarifs");
while ($row = $stmt->fetch()) {
    $tarifs[$row['type_place']] = $row['prix_heure'];
}

// Ensure defaults
if (!isset($tarifs['standard'])) $tarifs['standard'] = 100;
if (!isset($tarifs['PMR'])) $tarifs['PMR'] = 100; // Same as standard
if (!isset($tarifs['VIP'])) $tarifs['VIP'] = 200;

require_once 'includes/header.php';
?>

<div class="row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2>Tarifs Horaires</h2>
    <?php if (hasRole('admin')): ?>
        <span class="badge badge-primary">Mode Édition</span>
    <?php endif; ?>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <?php if (hasRole('admin')): ?>
        <form method="POST">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Le prix "Standard" s'applique automatiquement aux places <strong>Standard</strong> et <strong>PMR</strong>.
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Prix / Heure (DA)</th>
                        <th>Types Concernés</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Tarif Général</strong></td>
                        <td>
                            <input type="number" step="0.01" name="prix_general" value="<?php echo $tarifs['standard']; ?>" required>
                        </td>
                        <td>Standard, PMR</td>
                    </tr>
                    <tr>
                        <td><strong>Tarif VIP</strong> <i class="fas fa-crown" style="color: gold;"></i></td>
                        <td>
                            <input type="number" step="0.01" name="prix_vip" value="<?php echo $tarifs['VIP']; ?>" required>
                        </td>
                        <td>VIP uniquement</td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn btn-primary">Enregistrer les tarifs</button>
            </div>
        </form>
    <?php else: ?>
        <div class="dashboard-grid">
            <div class="card" style="text-align: center; border-top: 4px solid var(--text-secondary);">
                <i class="fas fa-car" style="font-size: 40px; margin-bottom: 15px; color: var(--text-secondary);"></i>
                <h3>Standard & PMR</h3>
                <p style="font-size: 24px; font-weight: 700; color: var(--primary); margin: 10px 0;">
                    <?php echo formatCurrency($tarifs['standard']); ?> <span style="font-size: 14px; color: var(--text-secondary);">/ heure</span>
                </p>
                <p>Pour tout usager standard.</p>
            </div>

            <div class="card" style="text-align: center; border-top: 4px solid gold;">
                <i class="fas fa-crown" style="font-size: 40px; margin-bottom: 15px; color: gold;"></i>
                <h3>VIP</h3>
                <p style="font-size: 24px; font-weight: 700; color: var(--primary); margin: 10px 0;">
                    <?php echo formatCurrency($tarifs['VIP']); ?> <span style="font-size: 14px; color: var(--text-secondary);">/ heure</span>
                </p>
                <p>Service premium et surveillance dédiée.</p>
            </div>
        </div>
        
        <?php if (!isLoggedIn()): ?>
            <div style="text-align: center; margin-top: 30px;">
                <a href="register.php" class="btn btn-primary">Créer un compte pour réserver</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

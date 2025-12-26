<?php require_once '../app/Views/includes/header.php'; ?>

<div class="row" style="text-align: center; margin-bottom: 50px;">
    <h2>Nos Tarifs</h2>
    <p style="color: var(--text-secondary);">Des prix transparents adaptés à tous les besoins.</p>
</div>

<div class="row" style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap;">
    <?php foreach ($tarifs as $tarif): ?>
    <div class="card" style="flex: 1; min-width: 250px; max-width: 350px; text-align: center; padding: 40px 20px; transition: transform 0.3s ease;">
        <div style="font-size: 50px; color: var(--primary); margin-bottom: 20px;">
            <?php 
                $icon = 'car';
                if ($tarif['type_place'] == 'PMR') $icon = 'wheelchair';
                if ($tarif['type_place'] == 'VIP') $icon = 'crown';
            ?>
            <i class="fas fa-<?php echo $icon; ?>"></i>
        </div>
        <h3 style="margin-bottom: 10px;"><?php echo ucfirst($tarif['type_place']); ?></h3>
        <p style="font-size: 32px; font-weight: bold; color: var(--text-color); margin-bottom: 20px;">
            <?php echo number_format($tarif['prix_heure'], 0); ?> <span style="font-size: 16px; color: var(--text-secondary);">DA / <?php echo $tarif['type_place'] === 'VIP' ? 'mois' : 'heure'; ?></span>
        </p>
        <ul style="text-align: left; margin-bottom: 30px; list-style: none; padding: 0;">
            <li style="margin-bottom: 10px;"><i class="fas fa-check" style="color: var(--success); margin-right: 10px;"></i> Surveillance 24/7</li>
            <li style="margin-bottom: 10px;"><i class="fas fa-check" style="color: var(--success); margin-right: 10px;"></i> Accès facile</li>
            <?php if ($tarif['type_place'] == 'VIP'): ?>
                <li style="margin-bottom: 10px;"><i class="fas fa-check" style="color: var(--success); margin-right: 10px;"></i> Service voiturier</li>
                <li style="margin-bottom: 10px;"><i class="fas fa-check" style="color: var(--success); margin-right: 10px;"></i> Lavage inclus</li>
            <?php elseif ($tarif['type_place'] == 'PMR'): ?>
                <li style="margin-bottom: 10px;"><i class="fas fa-check" style="color: var(--success); margin-right: 10px;"></i> Proche ascenseur</li>
            <?php endif; ?>
        </ul>
        <a href="<?php echo url('reservation/create'); ?>" class="btn btn-primary" style="width: 100%;">Réserver</a>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once '../app/Views/includes/footer.php'; ?>

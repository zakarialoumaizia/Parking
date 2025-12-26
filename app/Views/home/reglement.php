<?php require_once '../app/Views/includes/header.php'; ?>

<div class="row" style="text-align: center; margin-bottom: 50px;">
    <h2>Règlement Intérieur & Amendes</h2>
    <p style="color: var(--text-secondary);">Pour garantir la sécurité et le confort de tous, merci de respecter les règles suivantes.</p>
</div>

<div class="row" style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <h3><i class="fas fa-exclamation-triangle" style="color: var(--warning);"></i> Infractions & Sanctions</h3>
        <p style="margin-bottom: 20px;">Tout manquement aux règles ci-dessous entraînera l'application immédiate des pénalités correspondantes.</p>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee;">Type d'infraction</th>
                    <th style="padding: 15px; text-align: right; border-bottom: 2px solid #eee;">Montant à payer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($amendes as $amende): ?>
                <tr>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">
                        <strong><?php echo htmlspecialchars($amende['nom']); ?></strong>
                    </td>
                    <td style="padding: 15px; text-align: right; border-bottom: 1px solid #eee; font-weight: bold; color: var(--error);">
                        <?php echo number_format($amende['montant'], 0, ',', ' '); ?> DA
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 8px; color: #856404;">
            <strong>Note Importante:</strong><br>
            Le paiement des amendes est obligatoire avant toute nouvelle réservation ou sortie du véhicule.
            En cas de récidive, votre compte pourrait être suspendu.
        </div>
    </div>
</div>

<?php require_once '../app/Views/includes/footer.php'; ?>

<?php require_once '../app/Views/includes/header.php'; ?>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-area, .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white;
            color: black;
        }
        .no-print {
            display: none !important;
        }
        header, footer {
            display: none;
        }
    }
    
    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .report-table th, .report-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    .report-table th {
        background-color: #f4f4f4;
        color: #333;
    }
    
    .kpi-card {
        border: 1px solid #eee;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        background: #fff;
    }
    .kpi-value {
        font-size: 24px;
        font-weight: bold;
        color: var(--primary);
        margin: 10px 0;
    }
</style>

<div class="row no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2>Rapports & Statistiques</h2>
    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Imprimer Rapport</button>
</div>

<div class="print-area">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1>ParkingSmart - Rapport Mensuel</h1>
        <p>Généré le <?php echo date('d/m/Y à H:i'); ?></p>
    </div>

    <div class="row" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <div class="kpi-card">
            <h4>Chiffre d'Affaires Total</h4>
            <div class="kpi-value"><?php echo formatCurrency($totalRevenue ?? 0); ?></div>
        </div>
        <div class="kpi-card">
            <h4>Utilisateurs Inscrits</h4>
            <div class="kpi-value"><?php echo $totalUsers ?? 0; ?></div>
        </div>
        <div class="kpi-card">
            <h4>Taux d'Occupation Actuel</h4>
            <div class="kpi-value"><?php echo $occupancy ?? 0; ?>%</div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 30px;">
        <h3><i class="fas fa-chart-line"></i> Revenus par Mois (12 derniers mois)</h3>
        <?php if (!empty($revenueMonth)): ?>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th>Revenu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($revenueMonth as $row): ?>
                        <tr>
                            <td><?php echo $row['mois']; ?></td>
                            <td><?php echo formatCurrency($row['total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="padding: 20px; color: #666;">Aucune donnée disponible.</p>
        <?php endif; ?>
    </div>

    <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <div class="card">
            <h3><i class="fas fa-car"></i> Utilisation par Type de Place</h3>
            <?php if (!empty($usageType)): ?>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Réservations Totales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usageType as $row): ?>
                            <tr>
                                <td><?php echo ucfirst($row['type']); ?></td>
                                <td><?php echo $row['count']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune donnée.</p>
            <?php endif; ?>
        </div>
        
        <div class="card">
             <h3><i class="fas fa-info-circle"></i> Notes</h3>
             <ul style="margin-top: 15px; padding-left: 20px; line-height: 1.6;">
                 <li>Ce rapport inclut uniquement les transactions terminées et payées.</li>
                 <li>Les réservations annulées ne sont pas comptabilisées dans l'utilisation.</li>
                 <li>Le taux d'occupation est une capture instantanée au moment de la génération.</li>
             </ul>
        </div>
    </div>
    
    <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 20px;">
        &copy; <?php echo date('Y'); ?> ParkingSmart Enterprise. Document Confidentiel.
    </div>
</div>

<?php require_once '../app/Views/includes/footer.php'; ?>

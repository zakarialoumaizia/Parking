<?php require_once '../app/Views/includes/header.php'; ?>

<div class="page-header">
    <h2>Tableau de bord</h2>
    <a href="<?php echo url('reservation/create'); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle Réservation</a>
</div>

<?php if (checkRole('premium') || hasRole('vip')): ?>
    <div class="alert alert-success d-flex justify-between align-center mb-4">
        <div>
            <i class="fas fa-crown text-warning mr-2"></i> <strong>Statut VIP Actif</strong> - Services Prioritaires & Exclusifs.
        </div>
        <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-file-invoice"></i> Facturation Groupée</a>
    </div>
<?php endif; ?>

<h3 class="section-title">Réservations Actives</h3>
<?php if (empty($activeReservations)): ?>
    <!-- Subscription Booking Form -->
    <div class="card">
        <div class="card-header-flex">
            <h3 class="section-title"><i class="fas fa-calendar-check"></i> Réserver un Abonnement</h3>
        </div>
        <form method="POST" class="mt-4">
             <input type="hidden" name="new_subscription" value="1">
             <div class="dashboard-grid">
                  <div class="form-group">
                      <label>Durée (Mois)</label>
                      <input type="number" name="duration_months" id="monthsInput" min="1" max="12" value="1" class="form-control" onchange="updateTotal()" onkeyup="updateTotal()">
                  </div>
                  <div class="form-group">
                      <label>Prix Mensuel</label>
                      <input type="text" value="<?php echo formatCurrency($subscriptionPrice); ?>" readonly class="form-control" style="background: #f8f9fa;">
                  </div>
                  <div class="form-group">
                      <label>Total Estimé</label>
                      <h3 class="text-primary mb-0" id="totalDisplay"><?php echo formatCurrency($subscriptionPrice); ?></h3>
                  </div>
             </div>
             
             <div class="form-group">
                <label class="mb-3 d-block font-weight-bold">Mode de Paiement</label>
                <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <label class="card p-3" style="border: 1px solid var(--border-color); cursor: pointer; display: flex; align-items: flex-start; margin-bottom: 0;">
                        <input type="radio" name="billing_mode" value="now" checked style="width: auto; margin-top: 5px;">
                        <div class="ml-3">
                            <span class="d-block font-weight-bold">Payer Maintenant</span>
                            <span class="text-sm text-secondary">Carte Bancaire / CIB</span>
                        </div>
                    </label>
                    <label class="card p-3" style="border: 1px solid var(--border-color); cursor: pointer; display: flex; align-items: flex-start; margin-bottom: 0;">
                        <input type="radio" name="billing_mode" value="monthly" style="width: auto; margin-top: 5px;">
                        <div class="ml-3">
                            <span class="d-block font-weight-bold">Facturation Mensuelle</span>
                            <span class="text-sm text-secondary">Paiement groupé en fin de mois</span>
                        </div>
                    </label>
                </div>
             </div>
             
             <div class="card-footer text-right" style="padding-top: 0; border: none;">
                 <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-check"></i> Confirmer l'Abonnement</button>
             </div>
        </form>
    </div>
    <script>
        function updateTotal() {
            const price = <?php echo $subscriptionPrice; ?>;
            const months = Math.max(1, document.getElementById('monthsInput').value);
            const total = price * months;
            // Simple formatter
            const formatted = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ".00 DA";
            document.getElementById('totalDisplay').innerText = formatted;
        }
    </script>

<?php else: ?>
    <!-- Active Subscription Details -->
        <?php foreach ($activeReservations as $res): 
            $start = new DateTime($res['date_debut']);
            $end = new DateTime($res['date_fin']);
            $diff = $start->diff($end);
            
            // Smart Duration Display
            if ($diff->y > 0 || $diff->m > 0) {
                $durationStr = ($diff->y * 12 + $diff->m) . " Mois " . ($diff->d > 0 ? "+ {$diff->d} J" : "");
            } elseif ($diff->d > 0) {
                $durationStr = $diff->d . " Jours " . $diff->h . " H";
            } else {
                $durationStr = $diff->h . " Heures " . ($diff->i > 0 ? $diff->i . " min" : "");
            }
            
            $isPaid = ($res['paiement_statut'] === 'paye');
            // Assuming 'groupe' mode shouldn't pay online. DB fetch needed for mode, but let's assume if non_paye and not subscription (user logic), show button.
            // Actually, safer to show button always if non_paye, unless it's strictly flagged otherwise.
            $needsPayment = ($res['paiement_statut'] === 'non_paye');
        ?>
        <div class="card reservation-card" style="border-left: 5px solid <?php echo $isPaid ? 'var(--success)' : 'var(--warning)'; ?>;">
            <div class="card-header-flex">
                <div class="reservation-code" style="font-family: monospace; font-size: 1.1rem;"><?php echo $res['code_reservation']; ?></div>
                <div class="badge badge-<?php echo $isPaid ? 'success' : 'warning'; ?>">
                    <?php echo $isPaid ? 'Actif' : 'En Attente'; ?>
                </div>
            </div>
            
            <div class="reservation-details mt-4">
                 <div class="d-flex justify-between align-center mb-3 p-3 bg-light rounded">
                     <span class="text-secondary font-weight-bold"><i class="fas fa-map-marker-alt"></i> Place</span>
                     <span class="badge badge-primary" style="font-size: 1.25rem;"><?php echo $res['place_numero']; ?></span>
                 </div>
                 
                 <div class="dashboard-grid" style="gap: 1rem; grid-template-columns: 1fr 1fr; margin-bottom: 1rem;">
                     <div>
                         <div class="text-sm text-secondary">Durée</div>
                         <div class="font-bold" style="font-size: 1.1rem;"><?php echo $durationStr; ?></div>
                     </div>
                     <div>
                         <div class="text-sm text-secondary">Total</div>
                         <div class="font-bold text-primary" style="font-size: 1.2rem;"><?php echo formatCurrency($res['montant']); ?></div>
                     </div>
                 </div>
                 
                 <div class="mt-3 border-top pt-3">
                     <div class="row" style="display:flex; justify-content: space-between; font-size: 0.9rem; color: var(--text-secondary);">
                        <span><i class="fas fa-calendar-alt"></i> <?php echo $start->format('d/m H:i'); ?></span>
                        <span><i class="fas fa-flag-checkered"></i> <?php echo $end->format('d/m H:i'); ?></span>
                     </div>
                 </div>

                 <?php if ($needsPayment): ?>
                     <div class="mt-4">
                        <form method="POST" action="<?php echo url('reservation/retry_payment'); ?>">
                            <input type="hidden" name="code" value="<?php echo $res['code_reservation']; ?>">
                            <button type="submit" class="btn btn-warning btn-block" style="color: #fff;">
                                <i class="fas fa-credit-card"></i> Payer Maintenant
                            </button>
                        </form>
                     </div>
                 <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
<?php endif; ?>

<h3 class="section-title mt-4">Historique Récent</h3>
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Place</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Paiement</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historyReservations as $res): ?>
                <tr>
                    <td><strong><?php echo $res['code_reservation']; ?></strong></td>
                    <td><?php echo $res['place_numero']; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($res['date_debut'])); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($res['date_fin'])); ?></td>
                    <td>
                        <?php if ($res['paiement_statut'] === 'paye'): ?>
                            <span class="badge badge-success">Payé</span>
                        <?php else: ?>
                             <span class="badge badge-warning"><?php echo ucfirst($res['paiement_statut'] ?? 'N/A'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $res['statut'] == 'terminee' ? 'info' : ($res['statut'] == 'annulee' ? 'error' : 'secondary'); ?>">
                            <?php echo ucfirst($res['statut']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($historyReservations)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">Aucun historique récent.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../app/Views/includes/footer.php'; ?>

<?php require_once '../app/Views/includes/header.php'; ?>

<div class="container" style="max-width: 800px;">
    <h2 style="margin-bottom: 30px;">Reserve a Spot</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Step 1: Search -->
    <?php if ($step === 1): ?>
        <div class="card">
            <h3>Step 1: Choose Dates & Type</h3>
            <form method="POST" action="<?php echo url('reservation/create'); ?>">
                <div class="form-group">
                    <label>Spot Type</label>
                    <select name="type">
                        <option value="standard">Standard</option>
                        <option value="PMR">Data (PMR)</option>
                        <?php if (hasRole('premium') || hasRole('admin')): ?>
                            <option value="VIP">VIP</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                 <?php if (checkRole('premium')): ?>
                    <div class="alert alert-success" style="margin-bottom: 20px;">
                        <i class="fas fa-gem"></i> <strong>Premium Mode:</strong> You have priority access.
                    </div>
                <?php endif; ?>

                <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="datetime-local" name="date_debut" required min="<?php echo date('Y-m-d\TH:i'); ?>" value="<?php echo isset($_POST['date_debut']) ? $_POST['date_debut'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="datetime-local" name="date_fin" required min="<?php echo date('Y-m-d\TH:i'); ?>" value="<?php echo isset($_POST['date_fin']) ? $_POST['date_fin'] : ''; ?>">
                    </div>
                </div>
                
                <button type="submit" name="search" class="btn btn-primary" style="width: 100%;">Search Availability</button>
            </form>
        </div>
    
    <!-- Step 2: Select Place -->
    <?php elseif ($step === 2): ?>
        <div class="card">
            <h3>Step 2: Select a Spot</h3>
            <p style="margin-bottom: 20px;">Available spots for <strong><?php echo ucfirst($_POST['type']); ?></strong> from <?php echo str_replace('T', ' ', $_POST['date_debut']); ?> to <?php echo str_replace('T', ' ', $_POST['date_fin']); ?>.</p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px;">
                <?php foreach ($availablePlaces as $place): ?>
                    <form method="POST">
                        <input type="hidden" name="date_debut" value="<?php echo $_POST['date_debut']; ?>">
                        <input type="hidden" name="date_fin" value="<?php echo $_POST['date_fin']; ?>">
                        <input type="hidden" name="type" value="<?php echo $_POST['type']; ?>">
                        <input type="hidden" name="place_id" value="<?php echo $place['id']; ?>">
                        <input type="hidden" name="select_place" value="1">
                        
                        <button type="submit" style="
                            width: 100%; 
                            padding: 15px; 
                            background: var(--bg-body); 
                            border: 2px solid var(--success); 
                            border-radius: 8px; 
                            cursor: pointer;
                            display: flex; flex-direction: column; align-items: center; gap: 5px;
                        ">
                            <i class="fas fa-parking" style="font-size: 24px; color: var(--success);"></i>
                            <strong><?php echo $place['numero']; ?></strong>
                            <?php if (!empty($place['prix_custom'])): ?>
                                <span style="font-size: 10px; color: var(--text-secondary);">Custom Price</span>
                            <?php endif; ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
             <a href="<?php echo url('reservation/create'); ?>" class="btn btn-secondary" style="margin-top: 20px;">Back</a>
        </div>
        
    <!-- Step 3: Confirm -->
    <?php elseif ($step === 3 && $selectedPlace): ?>
        <div class="card">
             <h3 style="text-align: center;">Step 3: Confirm & Pay</h3>
             
             <div style="margin: 20px 0; padding: 20px; background: var(--bg-body); border-radius: 8px;">
                <p><strong>Spot:</strong> <?php echo $selectedPlace['numero']; ?></p>
                <p><strong>From:</strong> <?php echo str_replace('T', ' ', $_POST['date_debut']); ?></p>
                <p><strong>To:</strong> <?php echo str_replace('T', ' ', $_POST['date_fin']); ?></p>
                <p style="font-size: 20px; color: var(--primary); margin-top: 15px;"><strong>Total: <?php echo formatCurrency($price); ?></strong></p>
             </div>
             
             <form method="POST">
                <input type="hidden" name="date_debut" value="<?php echo $_POST['date_debut']; ?>">
                <input type="hidden" name="date_fin" value="<?php echo $_POST['date_fin']; ?>">
                <input type="hidden" name="type" value="<?php echo $_POST['type']; ?>">
                <input type="hidden" name="place_id" value="<?php echo $selectedPlace['id']; ?>">
                
                <?php if (hasRole(['premium', 'vip'])): ?>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Payment Mode</label>
                        <select name="payment_method">
                            <option value="online">Pay Now (Chargily)</option>
                            <option value="group">Add to Monthly Bill (Grouped)</option>
                        </select>
                    </div>
                <?php endif; ?>
                
                <button type="submit" name="confirm" class="btn btn-primary" style="width: 100%;">Confirm & Pay</button>
                <a href="<?php echo url('reservation/create'); ?>" class="btn btn-secondary" style="width: 100%; margin-top: 10px; text-align: center;">Cancel</a>
             </form>
        </div>
    <?php endif; ?>
    
</div>

<?php require_once '../app/Views/includes/footer.php'; ?>

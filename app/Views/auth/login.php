<?php require_once '../app/Views/includes/header.php'; ?>

<div style="max-width: 400px; margin: 40px auto;">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 30px; color: var(--primary);">Connexion</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo url('auth/login'); ?>">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="votre@email.com">
            </div>
            
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            Pas encore de compte ? <a href="<?php echo url('auth/register'); ?>">S'inscrire</a>
        </p>
    </div>
</div>

<?php require_once '../app/Views/includes/footer.php'; ?>

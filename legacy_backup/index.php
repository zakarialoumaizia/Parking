<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<div class="hero">
    <h1>Stationnement Intelligent <br>Gestion Simplifiée</h1>
    <p>Réservez votre place de parking en toute simplicité, gérez vos abonnements et gagnez du temps au quotidien.</p>
    
    <?php if (!isLoggedIn()): ?>
        <a href="register.php" class="btn btn-primary" style="padding: 15px 30px; font-size: 16px;">
            Commencer maintenant <i class="fas fa-arrow-right"></i>
        </a>
    <?php else: ?>
        <a href="reservation.php" class="btn btn-primary" style="padding: 15px 30px; font-size: 16px;">
            Réserver une place <i class="fas fa-calendar-check"></i>
        </a>
    <?php endif; ?>
</div>

<div class="dashboard-grid" style="margin-top: 50px;">
    <div class="card" style="text-align: center;">
        <div style="background: var(--primary-light); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <i class="fas fa-check-circle" style="font-size: 32px; color: var(--primary);"></i>
        </div>
        <h3>Disponibilité en temps réel</h3>
        <p>Vérifiez instantanément les places disponibles dans notre parking sécurisé.</p>
    </div>

    <div class="card" style="text-align: center;">
        <div style="background: var(--info); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; opacity: 0.1; color: var(--info);">
            <i class="fas fa-shield-alt" style="font-size: 32px; opacity: 1;"></i>
        </div>
        <h3>Sécurité 24/7</h3>
        <p>Votre véhicule est surveillé en permanence par nos agents et caméras.</p>
    </div>

    <div class="card" style="text-align: center;">
        <div style="background: var(--warning); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; opacity: 0.1; color: var(--warning);">
            <i class="fas fa-mobile-alt" style="font-size: 32px; opacity: 1;"></i>
        </div>
        <h3>Paiement Mobile</h3>
        <p>Payez facilement depuis votre smartphone avec nos solutions sécurisées.</p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

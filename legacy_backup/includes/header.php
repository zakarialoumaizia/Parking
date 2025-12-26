<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Smart - Gestion Intelligente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <i class="fas fa-parking"></i>
                <span>ParkingSmart</span>
            </a>
            
            <nav class="nav-links">
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Accueil</a>
                <a href="tarifs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'tarifs.php' ? 'active' : ''; ?>">Tarifs</a>
                
                <?php if (isLoggedIn()): ?>
                    <?php if (hasRole(['usager', 'premium'])): ?>
                        <a href="dashboard_user.php">Mon Compte</a>
                        <a href="reservation.php">Réserver</a>
                    <?php elseif (hasRole('agent')): ?>
                        <a href="dashboard_agent.php">Contrôle</a>
                        <a href="amendes.php">Amendes</a>
                    <?php elseif (hasRole('admin')): ?>
                        <a href="dashboard_admin.php">Admin</a>
                        <a href="gestion_places.php">Places</a>
                        <a href="rapports.php">Rapports</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <div class="user-actions">
                <?php if (isLoggedIn()): ?>
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <?php 
                        $notifCount = getUnreadNotificationsCount($_SESSION['user_id']);
                        ?>
                        <a href="notifications.php" style="position: relative; color: var(--text-secondary);">
                            <i class="fas fa-bell" style="font-size: 18px;"></i>
                            <?php if ($notifCount > 0): ?>
                                <span style="position: absolute; top: -5px; right: -5px; background: var(--error); color: white; border-radius: 50%; width: 15px; height: 15px; font-size: 10px; display: flex; align-items: center; justify-content: center;"><?php echo $notifCount; ?></span>
                            <?php endif; ?>
                        </a>
                        <span style="font-size: 14px; font-weight: 500;">
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?>
                        </span>
                        <a href="logout.php" class="btn btn-sm btn-danger">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div style="display: flex; gap: 10px;">
                        <a href="login.php" class="btn btn-sm btn-secondary">Connexion</a>
                        <a href="register.php" class="btn btn-sm btn-primary">Inscription</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <main>
        <div class="container">
            <?php 
            $flash = getFlashMessage();
            if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type'] == 'error' ? 'error' : 'success'; ?>">
                    <i class="fas fa-<?php echo $flash['type'] == 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                    <?php echo $flash['message']; ?>
                </div>
            <?php endif; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Parking Smart - Gestion Intelligente de Parking">
    <title>Parking Smart - Gestion Intelligente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
</head>
<body>
    <?php 
    // Check if current page is Home (Landing Page)
    $url = $_GET['url'] ?? '';
    $isHome = $url === '';
    
    // Check if current page is Auth (Login/Register)
    $isAuth = strpos($url, 'auth/') === 0;
    
    // Show Sidebar only if NOT Home AND NOT Auth
    $showSidebar = !$isHome && !$isAuth;
    
    if ($showSidebar) {
        require_once 'sidebar.php';
    }
    ?>

    <!-- Main Content Area -->
    <div class="main-wrapper <?php echo !$showSidebar ? 'no-sidebar' : ''; ?>">
        <header class="top-header" role="banner">
            <div class="container-fluid">
                <div class="header-left">
                    <?php if ($showSidebar): ?>
                        <!-- Menu Toggle for Dashboard -->
                        <button class="menu-toggle" onclick="toggleMenu()" aria-label="Ouvrir le menu latéral" aria-expanded="false">
                            <i class="fas fa-bars"></i>
                        </button>
                        <!-- Dashboard Logo -->
                        <a href="<?php echo url('user/dashboard'); ?>" class="logo" aria-label="Dashboard ParkingSmart">
                            <i class="fas fa-parking"></i>
                            <span>ParkingSmart</span>
                        </a>
                    <?php else: ?>
                        <!-- Menu Toggle for Homepage/Auth (Mobile only) -->
                        <?php if ($isHome): ?>
                        <button class="menu-toggle" onclick="toggleHomeMenu()" aria-label="Ouvrir le menu mobile" aria-expanded="false">
                            <i class="fas fa-bars"></i>
                        </button>
                        <?php endif; ?>
                        
                        <!-- Landing/Auth Page Logo -->
                        <a href="<?php echo url(''); ?>" class="logo" aria-label="Accueil ParkingSmart">
                            <i class="fas fa-parking"></i>
                            <span>ParkingSmart</span>
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="header-right">
                    <?php if ($isHome): ?>
                        <!-- Desktop Home Navigation -->
                        <nav class="home-desktop-nav desktop-only" aria-label="Navigation principale">
                             <a href="#tarifs" class="nav-link">Nos Tarifs</a>
                             <a href="#reglement" class="nav-link">Règlement</a>
                        </nav>
                    <?php endif; ?>

                    <?php if (isLoggedIn()): ?>
                        <div class="user-profile" role="group" aria-label="Profil utilisateur">
                            <?php 
                            $notifCount = getUnreadNotificationsCount($_SESSION['user_id']);
                            ?>
                            <a href="<?php echo url('notifications'); ?>" class="notif-icon" aria-label="Notifications" title="Notifications">
                                <i class="fas fa-bell" aria-hidden="true"></i>
                                <?php if ($notifCount > 0): ?>
                                    <span class="badge-count" aria-label="<?php echo $notifCount; ?> notifications non lues">
                                        <?php echo $notifCount; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?></span>
                                <span class="user-role"><?php echo ucfirst($_SESSION['user_role'] ?? ''); ?></span>
                            </div>
                            <a href="<?php echo url('auth/logout'); ?>" class="btn-logout" title="Déconnexion" aria-label="Déconnexion">
                                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons desktop-only">
                            <a href="<?php echo url('auth/login'); ?>" class="btn btn-sm btn-secondary" aria-label="Se connecter">
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                                Connexion
                            </a>
                            <a href="<?php echo url('auth/register'); ?>" class="btn btn-sm btn-primary" aria-label="S'inscrire">
                                <i class="fas fa-user-plus" aria-hidden="true"></i>
                                Inscription
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        
        <main role="main">
            <?php if (!$isHome): ?>
            <div class="container">
            <?php endif; ?>
                <?php 
                $flash = getFlashMessage();
                if ($flash): ?>
                    <div class="<?php echo !$isHome ? 'alert' : 'container alert'; ?> alert-<?php echo $flash['type'] == 'error' ? 'error' : 'success'; ?>" 
                         role="alert" 
                         aria-live="polite"
                         style="<?php echo $isHome ? 'margin-top: 20px;' : ''; ?>">
                        <i class="fas fa-<?php echo $flash['type'] == 'error' ? 'exclamation-circle' : 'check-circle'; ?>" aria-hidden="true"></i>
                        <span><?php echo $flash['message']; ?></span>
                    </div>
                <?php endif; ?>
            
            <!-- Mobile Menu for Homepage -->
            <aside class="home-mobile-menu" id="homeMobileMenu" role="navigation" aria-label="Menu mobile">
                <div class="menu-header">
                    <span class="menu-title">Menu</span>
                    <button class="menu-close" onclick="toggleHomeMenu()" aria-label="Fermer le menu">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <nav class="mobile-nav-links">
                    <a href="#tarifs" onclick="toggleHomeMenu()" class="mobile-nav-link">
                        <i class="fas fa-tag" aria-hidden="true"></i>
                        Nos Tarifs
                    </a>
                    <a href="#reglement" onclick="toggleHomeMenu()" class="mobile-nav-link">
                        <i class="fas fa-file-alt" aria-hidden="true"></i>
                        Règlement
                    </a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?php echo url('auth/login'); ?>" class="mobile-nav-link auth-link">
                            <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                            Connexion
                        </a>
                        <a href="<?php echo url('auth/register'); ?>" class="mobile-nav-link auth-link primary">
                            <i class="fas fa-user-plus" aria-hidden="true"></i>
                            Inscription
                        </a>
                    <?php else: ?>
                        <a href="<?php echo url('user/dashboard'); ?>" class="mobile-nav-link dashboard-link">
                            <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                            Mon Dashboard
                        </a>
                        <a href="<?php echo url('user/profile'); ?>" class="mobile-nav-link">
                            <i class="fas fa-user" aria-hidden="true"></i>
                            Mon Profil
                        </a>
                        <a href="<?php echo url('auth/logout'); ?>" class="mobile-nav-link logout-link">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                            Déconnexion
                        </a>
                    <?php endif; ?>
                </nav>
            </aside>
            
            <div class="nav-overlay" id="homeOverlay" onclick="toggleHomeMenu()" role="button" aria-label="Fermer le menu" tabindex="0"></div>
        </main>

    <script>
        // Menu Toggle Functions
        let isMenuOpen = false;
        let isHomeMenuOpen = false;

        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.querySelector('.main-wrapper');
            const overlay = document.getElementById('navOverlay');
            const toggleButton = document.querySelector('.menu-toggle');
            
            if(sidebar) {
                if (window.innerWidth > 992) {
                    // Desktop Behavior: Collapse/Expand Sidebar
                    sidebar.classList.toggle('collapsed');
                    if (mainWrapper) mainWrapper.classList.toggle('sidebar-collapsed');
                } else {
                    // Mobile Behavior: Slide Out Sidebar
                    sidebar.classList.toggle('active');
                    isMenuOpen = sidebar.classList.contains('active');
                    
                    if(toggleButton) {
                        toggleButton.setAttribute('aria-expanded', isMenuOpen);
                    }
                    if(overlay) {
                        overlay.classList.toggle('active');
                    }
                    
                    // Prevent body scroll only on mobile when menu is open
                    document.body.style.overflow = isMenuOpen ? 'hidden' : '';
                }
            }
        }

        function toggleHomeMenu() {
            const menu = document.getElementById('homeMobileMenu');
            const overlay = document.getElementById('homeOverlay');
            const toggleButton = document.querySelector('.menu-toggle');
            
            menu.classList.toggle('active');
            isHomeMenuOpen = !isHomeMenuOpen;
            
            if(toggleButton) {
                toggleButton.setAttribute('aria-expanded', isHomeMenuOpen);
            }
            
            overlay.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            document.body.style.overflow = isHomeMenuOpen ? 'hidden' : '';
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('homeMobileMenu');
            const overlay = document.getElementById('homeOverlay');
            const toggleButton = document.querySelector('.menu-toggle');
            
            if (isHomeMenuOpen && menu && !menu.contains(event.target) && 
                !event.target.closest('.menu-toggle')) {
                toggleHomeMenu();
            }
        });

        // Close menu with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                if (isMenuOpen) toggleMenu();
                if (isHomeMenuOpen) toggleHomeMenu();
            }
        });

        // Close menu on resize (for responsive behavior)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                if (isMenuOpen) toggleMenu();
                if (isHomeMenuOpen) toggleHomeMenu();
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Touch event for better mobile experience
        let touchStartX = 0;
        let touchEndX = 0;

        document.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, false);

        document.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);

        function handleSwipe() {
            const swipeThreshold = 50;
            
            if (touchStartX - touchEndX > swipeThreshold) {
                // Swipe left - close menu if open
                if (isHomeMenuOpen) toggleHomeMenu();
                if (isMenuOpen) toggleMenu();
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading class for progressive enhancement
            document.body.classList.add('loaded');
            
            // Handle focus for accessibility
            const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
            const firstFocusableElement = document.querySelectorAll(focusableElements)[0];
            const lastFocusableElement = document.querySelectorAll(focusableElements)[document.querySelectorAll(focusableElements).length - 1];
            
            // Trap focus in mobile menu
            document.getElementById('homeMobileMenu')?.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === firstFocusableElement) {
                            e.preventDefault();
                            lastFocusableElement.focus();
                        }
                    } else {
                        if (document.activeElement === lastFocusableElement) {
                            e.preventDefault();
                            firstFocusableElement.focus();
                        }
                    }
                }
            });
        });
    </script>

    <!-- Add CSS for new elements -->
    <style>
        /* Mobile Menu Styles */
        .home-mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            max-width: 85vw;
            background: white;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            z-index: 10002;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .home-mobile-menu.active {
            transform: translateX(0);
        }

        .menu-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-card);
        }

        .menu-title {
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--text-main);
        }

        .menu-close {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.25rem;
            color: var(--text-secondary);
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .menu-close:hover {
            background: var(--bg-body);
            color: var(--error);
        }

        .mobile-nav-links {
            display: flex;
            flex-direction: column;
            padding: 1rem 0;
            flex: 1;
            overflow-y: auto;
        }

        .mobile-nav-link {
            padding: 1rem 1.5rem;
            color: var(--text-main);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: var(--transition);
            border-left: 4px solid transparent;
            text-decoration: none;
        }

        .mobile-nav-link:hover,
        .mobile-nav-link:focus {
            background: var(--primary-light);
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .mobile-nav-link i {
            width: 1.25rem;
            text-align: center;
            font-size: 1rem;
        }

        .auth-link {
            color: var(--text-secondary);
        }

        .auth-link.primary {
            color: var(--primary);
            font-weight: 600;
        }

        .dashboard-link {
            color: var(--primary);
            font-weight: 600;
        }

        .logout-link {
            color: var(--error);
            margin-top: auto;
        }

        .nav-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10001;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(2px);
        }

        .nav-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .home-mobile-menu {
                width: 100%;
                max-width: 320px;
            }
            
            .mobile-nav-link {
                padding: 0.875rem 1.25rem;
                font-size: 0.9375rem;
            }
            
            .menu-header {
                padding: 1rem;
            }
            
            .menu-title {
                font-size: 1rem;
            }
        }

        /* Accessibility focus styles */
        .menu-toggle:focus,
        .menu-close:focus,
        .mobile-nav-link:focus,
        .btn:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* Loading state */
        body.loaded * {
            transition: all 0.3s ease;
        }

        /* High contrast mode */
        @media (prefers-contrast: high) {
            .home-mobile-menu {
                border: 2px solid #000;
            }
            
            .mobile-nav-link:hover,
            .mobile-nav-link:focus {
                border: 2px solid #000;
            }
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .home-mobile-menu,
            .nav-overlay,
            .mobile-nav-link,
            .menu-toggle,
            .menu-close {
                transition: none;
            }
            
            .home-mobile-menu.active {
                transform: translateX(0);
            }
        }
    </style>
</body>
</html>
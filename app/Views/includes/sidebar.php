<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo url(''); ?>" class="logo">
            <i class="fas fa-parking"></i>
            <span>ParkingSmart</span>
        </a>
        <div class="close-sidebar" onclick="toggleMenu()">
            <i class="fas fa-times"></i>
        </div>
    </div>
    
    <nav class="sidebar-nav">
            <a href="<?php echo hasRole('admin') ? url('admin/dashboard') : url('user/dashboard'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'dashboard') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> <span>Accueil</span>
            </a>
            <?php if (!hasRole('admin')): ?>
            <a href="<?php echo url('#tarifs'); ?>" class="">
                <i class="fas fa-tags"></i> <span>Tarifs</span>
            </a>
            <a href="<?php echo url('#reglement'); ?>" class="">
                <i class="fas fa-file-alt"></i> <span>Règlement</span>
            </a>
            <?php endif; ?>
            
            <?php if (isLoggedIn()): ?>
                
                <?php if (hasRole(['usager', 'premium', 'admin', 'agent'])): ?>
                    <div class="sidebar-dropdown">
                        <a href="javascript:void(0)" class="sidebar-dropdown-toggle <?php echo (strpos($_GET['url'] ?? '', 'user/') === 0 || strpos($_GET['url'] ?? '', 'reservation/') === 0) ? 'active' : ''; ?>" onclick="toggleSidebarSubmenu(this)">
                            <div style="display:flex; align-items:center;">
                                <i class="fas fa-user-circle"></i> <span>Mon Compte</span>
                            </div>
                            <i class="fas fa-chevron-down chevron"></i>
                        </a>
                        <div class="sidebar-submenu <?php echo (strpos($_GET['url'] ?? '', 'user/') === 0 || strpos($_GET['url'] ?? '', 'reservation/') === 0) ? 'active' : ''; ?>">
                            <a href="<?php echo url('user/dashboard'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'user/dashboard') === 0) ? 'active' : ''; ?>">
                                Tableau de bord
                            </a>
                            <a href="<?php echo url('user/profile'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'user/profile') === 0) ? 'active' : ''; ?>">
                                Mon Profil
                            </a>
                            <?php if (!hasRole('admin')): ?>
                                <a href="<?php echo url('reservation/create'); ?>" class="<?php echo (strpos($_GET['url'] ?? '', 'reservation/create') === 0) ? 'active' : ''; ?>">
                                    Réserver
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (hasRole('agent')): ?>
                    <div class="nav-divider">Zone Agent</div>
                    <a href="<?php echo url('agent/dashboard'); ?>"><i class="fas fa-eye"></i> <span>Contrôle</span></a>
                    <a href="<?php echo url('agent/amendes'); ?>"><i class="fas fa-exclamation-triangle"></i> <span>Amendes</span></a>
                <?php elseif (hasRole('admin')): ?>
                    <div class="nav-divider">Administration</div>
                    <a href="<?php echo url('admin/places'); ?>">
                        <i class="fas fa-parking"></i> <span>Places</span>
                    </a>
                    <a href="<?php echo url('admin/rapports'); ?>">
                        <i class="fas fa-chart-line"></i> <span>Rapports</span>
                    </a>
                    <a href="<?php echo url('admin/taxes'); ?>">
                        <i class="fas fa-gavel"></i> <span>Taxes</span>
                    </a>
                    <a href="<?php echo url('admin/notifications'); ?>">
                        <i class="fas fa-bell"></i> <span>Notifications</span>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <div class="nav-divider">Compte</div>
                 <a href="<?php echo url('auth/login'); ?>"><i class="fas fa-sign-in-alt"></i> <span>Connexion</span></a>
                 <a href="<?php echo url('auth/register'); ?>"><i class="fas fa-user-plus"></i> <span>Inscription</span></a>
            <?php endif; ?>
    </nav>
</aside>

<!-- Overlay for Mobile -->
<div class="nav-overlay" id="navOverlay" onclick="toggleMenu()"></div>

<script>
    function toggleSidebarSubmenu(element) {
        element.classList.toggle('active');
        const submenu = element.nextElementSibling;
        if (submenu) {
            submenu.classList.toggle('active');
        }
    }
</script>

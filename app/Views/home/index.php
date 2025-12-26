<?php require_once '../app/Views/includes/header.php'; ?>

<div class="hero">
    <div class="container">
        <h1>Stationnement Intelligent <br><span class="highlight">Gestion Simplifiée</span></h1>
        <p class="hero-description">Réservez votre place de parking en toute simplicité, gérez vos abonnements et gagnez du temps au quotidien.</p>
        
        <div class="hero-buttons">
            <?php if (!isLoggedIn()): ?>
                <a href="<?php echo url('auth/register'); ?>" class="btn btn-primary btn-lg btn-hero">
                    <i class="fas fa-rocket"></i>
                    Commencer maintenant
                </a>
                <a href="#tarifs" class="btn btn-secondary btn-lg">
                    <i class="fas fa-tag"></i>
                    Voir nos tarifs
                </a>
            <?php else: ?>
                <a href="<?php echo url('reservation/create'); ?>" class="btn btn-primary btn-lg btn-hero">
                    <i class="fas fa-calendar-check"></i>
                    Réserver une place
                </a>
                <a href="<?php echo url('user/dashboard'); ?>" class="btn btn-secondary btn-lg">
                    <i class="fas fa-tachometer-alt"></i>
                    Mon Dashboard
                </a>
            <?php endif; ?>
        </div>
        
        <div class="hero-stats">
            <div class="stat-item">
                <i class="fas fa-car"></i>
                <div>
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Places disponibles</span>
                </div>
            </div>
            <div class="stat-item">
                <i class="fas fa-users"></i>
                <div>
                    <span class="stat-number">1,200+</span>
                    <span class="stat-label">Clients satisfaits</span>
                </div>
            </div>
            <div class="stat-item">
                <i class="fas fa-clock"></i>
                <div>
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Service continu</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tarifs Section -->
<section id="tarifs" class="tarifs-section">
    <div class="container">
        <div class="section-header">
            <h2>Nos Formules d'Abonnement</h2>
            <p class="section-description">Choisissez la formule qui correspond à vos besoins</p>
        </div>
        
        <div class="pricing-grid">
            <?php foreach ($tarifs as $tarif): ?>
            <div class="pricing-card <?php echo $tarif['type_place'] === 'VIP' ? 'featured' : ''; ?>">
                <?php if ($tarif['type_place'] === 'VIP'): ?>
                    <div class="pricing-badge">Recommandé</div>
                <?php endif; ?>
                
                <div class="pricing-header">
                    <h3><?php echo htmlspecialchars($tarif['type_place']); ?></h3>
                    <div class="pricing-price">
                        <span class="price"><?php echo number_format($tarif['prix_heure'], 0, ',', ' '); ?> DA</span>
                        <span class="period">/<?php echo $tarif['type_place'] === 'VIP' ? 'mois' : 'heure'; ?></span>
                    </div>
                </div>
                
                <ul class="pricing-features">
                    <?php 
                    $features = [
                        'VIP' => [
                            ['text' => 'Place réservée 24/7', 'icon' => 'fa-lock'],
                            ['text' => 'Accès prioritaire', 'icon' => 'fa-star'],
                            ['text' => 'Surveillance dédiée', 'icon' => 'fa-video'],
                            ['text' => 'Service nettoyage', 'icon' => 'fa-spray-can']
                        ],
                        'PMR' => [
                            ['text' => 'Proche de l\'entrée', 'icon' => 'fa-wheelchair'],
                            ['text' => 'Largeur adaptée', 'icon' => 'fa-expand'],
                            ['text' => 'Assistance disponible', 'icon' => 'fa-hands-helping'],
                            ['text' => 'Sécurité renforcée', 'icon' => 'fa-shield-alt']
                        ],
                        'Standard' => [
                            ['text' => 'Stationnement sécurisé', 'icon' => 'fa-shield'],
                            ['text' => 'Accès 24/7', 'icon' => 'fa-clock'],
                            ['text' => 'Vidéosurveillance', 'icon' => 'fa-camera'],
                            ['text' => 'Éclairage optimisé', 'icon' => 'fa-lightbulb']
                        ]
                    ];
                    $feats = $features[$tarif['type_place']] ?? [['text' => 'Stationnement sécurisé', 'icon' => 'fa-check']];
                    foreach ($feats as $f): ?>
                        <li>
                            <i class="fas <?php echo $f['icon']; ?>"></i>
                            <span><?php echo $f['text']; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <div class="pricing-footer">
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?php echo url('auth/register'); ?>" class="btn <?php echo $tarif['type_place'] === 'VIP' ? 'btn-primary' : 'btn-secondary'; ?> btn-block">
                            <i class="fas fa-user-plus"></i>
                            S'inscrire
                        </a>
                    <?php else: ?>
                        <a href="<?php echo url('reservation/create?type=' . $tarif['type_place']); ?>" class="btn <?php echo $tarif['type_place'] === 'VIP' ? 'btn-primary' : 'btn-secondary'; ?> btn-block">
                            <i class="fas fa-calendar-plus"></i>
                            Réserver
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Reglement Section -->
<section id="reglement" class="reglement-section">
    <div class="container">
        <div class="section-header">
            <h2>Règlement & Pénalités</h2>
            <p class="section-description">Pour garantir la sécurité et le confort de tous</p>
        </div>
        
        <div class="reglement-card">
            <div class="table-responsive">
                <table class="reglement-table">
                    <thead>
                        <tr>
                            <th scope="col">Infraction</th>
                            <th scope="col" class="text-right">Sanction</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($amendes as $amende): ?>
                        <tr>
                            <td>
                                <div class="infraction-item">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <div>
                                        <strong><?php echo htmlspecialchars($amende['nom']); ?></strong>
                                        <p class="infraction-desc"><?php echo htmlspecialchars($amende['description'] ?? 'Non respect du règlement'); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">
                                <span class="sanction-amount"><?php echo number_format($amende['montant'], 0, ',', ' '); ?> DA</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="reglement-footer">
                <div class="reglement-note">
                    <i class="fas fa-info-circle"></i>
                    <p>Les sanctions sont appliquées automatiquement en cas de non-respect du règlement.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Prêt à simplifier votre stationnement ?</h2>
            <p>Rejoignez nos milliers d'utilisateurs satisfaits</p>
            <div class="cta-buttons">
                <?php if (!isLoggedIn()): ?>
                    <a href="<?php echo url('auth/register'); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus"></i>
                        Créer un compte gratuit
                    </a>
                <?php else: ?>
                    <a href="<?php echo url('reservation/create'); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-plus"></i>
                        Nouvelle réservation
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once '../app/Views/includes/footer.php'; ?>
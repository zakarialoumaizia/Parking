<?php 
$isHomeFooter = !isset($_GET['url']) || $_GET['url'] === '';
if (!$isHomeFooter): ?>
    </div>
<?php endif; ?>
    </main>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-copyright">
                    &copy; <?php echo date('Y'); ?> Parking Smart. Tous droits réservés.
                </div>
                <div class="nav-links" style="gap: 20px; font-size: 13px;">
                    <a href="<?php echo url('page/conditions'); ?>">Conditions</a>
                    <a href="<?php echo url('page/confidentialite'); ?>">Confidentialité</a>
                    <a href="<?php echo url('page/help'); ?>">Aide</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

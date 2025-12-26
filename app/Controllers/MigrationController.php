<?php
class MigrationController extends Controller {
    public function index() {
        if (!hasRole('admin')) {
             die('Access Denied');
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // 1. Amendes
            $db->exec("CREATE TABLE IF NOT EXISTS `amendes` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `agent_id` int(11) DEFAULT NULL,
              `reservation_id` int(11) DEFAULT NULL,
              `place_id` int(11) DEFAULT NULL,
              `montant` decimal(8,2) NOT NULL,
              `motif` text NOT NULL,
              `statut` enum('non_payee','payee') DEFAULT 'non_payee',
              `date_amende` timestamp NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
            echo "Table 'amendes' checked.<br>";

            // 2. Notifications
            $db->exec("CREATE TABLE IF NOT EXISTS `notifications` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `utilisateur_id` int(11) NOT NULL,
              `message` text NOT NULL,
              `lu` tinyint(1) DEFAULT 0,
              `date_notification` timestamp NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
             echo "Table 'notifications' checked.<br>";

            // 3. Alter Places
            try {
                $db->exec("ALTER TABLE `places` ADD COLUMN `prix_custom` decimal(8,2) DEFAULT NULL");
                echo "Column 'prix_custom' added.<br>";
            } catch (Exception $e) {}

            // 4. Alter Amendes
            try {
                 $db->exec("ALTER TABLE `amendes` ADD COLUMN `place_id` int(11) DEFAULT NULL");
            } catch (Exception $e) {}

            echo "Migration Completed.";
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

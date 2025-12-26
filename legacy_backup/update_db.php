<?php
require_once 'includes/db_config.php';

try {
    // 1. Modify places table for custom price and admin status
    // Check if column exists first to avoid errors if run multiple times (simple way: try/catch or just run)
    
    // Add custom price
    $pdo->exec("ALTER TABLE places ADD COLUMN prix_custom DECIMAL(6,2) DEFAULT NULL");
    
    // Modify status enum to include 'indisponible'
    // Note: In MariaDB/MySQL, modifying ENUM requires listing all old values + new ones
    $pdo->exec("ALTER TABLE places MODIFY COLUMN statut ENUM('libre','reservee','occupee','indisponible') DEFAULT 'libre'");

    echo "Table 'places' updated successfully.<br>";

} catch (PDOException $e) {
    echo "Error updating 'places': " . $e->getMessage() . "<br> (Ignored if columns already exist)<br>";
}

echo "Database update script finished.";
?>

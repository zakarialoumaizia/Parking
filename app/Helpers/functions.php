<?php
// app/Helpers/functions.php

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function checkRole($role) {
    if (!isLoggedIn()) return false;
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

function hasRole($roles) {
    if (!isLoggedIn()) return false;
    if (!is_array($roles)) $roles = [$roles];
    return in_array($_SESSION['user_role'], $roles);
}

function formatCurrency($amount) {
    return number_format($amount, 2, ',', ' ') . ' DA';
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Helper to get DB connection inside functions if absolutely necessary
function getDb() {
    return Database::getInstance()->getConnection();
}

function getUnreadNotificationsCount($user_id) {
    $db = getDb();
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE utilisateur_id = ? AND lu = 0");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

// URL Helper
function url($path) {
    // 1. Priority: Manual Configuration (app/Config/config.php)
    if (defined('BASE_URL')) {
        return BASE_URL . ltrim($path, '/');
    }

    // 2. Fallback: Dynamic Detection
    $root = dirname($_SERVER['SCRIPT_NAME']);
    $root = rtrim($root, '/') . '/';
    
    return $root . ltrim($path, '/');
}

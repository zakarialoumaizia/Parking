<?php
// includes/functions.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_config.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function checkRole($role) {
    if (!isLoggedIn()) return false;
    
    // Admin has access to everything effectively, but explicit checks are better
    // Hierarchy: Admin > Agent > Premium > User
    // But for strict role checks:
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

// Check if user has at least this role (simple hierarchy)
function hasRole($roles) {
    if (!isLoggedIn()) return false;
    if (!is_array($roles)) $roles = [$roles];
    return in_array($_SESSION['user_role'], $roles);
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Redirect if not correct role
function requireRole($roles) {
    requireLogin();
    if (!hasRole($roles)) {
        header('Location: index.php');
        exit();
    }
}

// Format currency
function formatCurrency($amount) {
    return number_format($amount, 2, ',', ' ') . ' DA';
}

// Sanitization
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Flash messages
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

// Notifications
function addNotification($user_id, $message) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, message) VALUES (?, ?)");
    return $stmt->execute([$user_id, $message]);
}

function getUnreadNotificationsCount($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE utilisateur_id = ? AND lu = 0");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

function markNotificationsRead($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE notifications SET lu = 1 WHERE utilisateur_id = ?");
    $stmt->execute([$user_id]);
}
?>

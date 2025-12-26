<?php
// Start Session
session_start();

// Load Core Libraries
// Path Detection for 'app' folder
$appDir = __DIR__ . '/../app';
if (!is_dir($appDir)) {
    $appDir = __DIR__ . '/app'; // Fallback if index.php is in root alongside app
}

require_once $appDir . '/Core/App.php';
require_once $appDir . '/Core/Controller.php';
require_once $appDir . '/Core/Model.php';
require_once $appDir . '/Config/Database.php';
require_once $appDir . '/Config/config.php';

// Helper functions
require_once $appDir . '/Helpers/functions.php';

// Init Core App
$init = new App();

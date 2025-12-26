<?php
echo "<h1>Diagnostic Config</h1>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current Dir: " . __DIR__ . "<br>";
echo "App Dir Check: " . (is_dir(__DIR__ . '/../app') ? "Found ../app" : "NOT Found ../app") . "<br>";
echo "App Dir Check (Root): " . (is_dir(__DIR__ . '/app') ? "Found ./app" : "NOT Found ./app") . "<br>";
?>

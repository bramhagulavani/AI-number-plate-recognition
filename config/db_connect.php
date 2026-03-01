<?php
// Load local settings if present (config/settings.php)
if (file_exists(__DIR__ . '/settings.php')) {
    include __DIR__ . '/settings.php';
}

// DB connection configuration. Priority:
// 1. explicit variables from settings.php ($DB_HOST etc.)
// 2. environment variables
// 3. sensible defaults
$host = isset($DB_HOST) && $DB_HOST !== '' ? $DB_HOST : (getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost');
$user = isset($DB_USER) && $DB_USER !== '' ? $DB_USER : (getenv('DB_USER') !== false ? getenv('DB_USER') : 'root');
$password = isset($DB_PASS) ? $DB_PASS : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
$dbname = isset($DB_NAME) && $DB_NAME !== '' ? $DB_NAME : (getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'number_plate_db');

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Do not leak credentials in the error output. Provide actionable guidance instead.
    http_response_code(500);
    $msg = "Database connection failed: " . $conn->connect_error . ".\n";
    $msg .= "Verify credentials in config/settings.php or set DB_HOST/DB_USER/DB_PASS/DB_NAME environment variables.";
    die($msg);
}

// Optional: set charset
$conn->set_charset('utf8mb4');
?>

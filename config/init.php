<?php
// init.php - run this script once to create the database and all required tables
// It uses the same connection settings seen elsewhere in the project.

$host     = "localhost";
$user     = "root";
$password = "";
$dbname   = "number_plate_db";

// connect to server (not yet to a specific database)
$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// create database if it doesn't already exist
if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
    die("Database creation failed: " . $conn->error);
}

// select the database for the remaining operations
$conn->select_db($dbname);

// list of table‑creation statements inferred from the codebase
$tables = [
    // users table used by registration, login, profile, etc.
    "CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        mobile VARCHAR(20) DEFAULT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        address TEXT DEFAULT NULL,
        pincode VARCHAR(10) DEFAULT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // vehicle_records table referenced in display.php
    "CREATE TABLE IF NOT EXISTS vehicle_records (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        number_plate VARCHAR(50) NOT NULL,
        entry_time DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($tables as $sql) {
    if (!$conn->query($sql)) {
        echo "Error creating table: " . $conn->error . "\n";
    }
}

echo "Initialization complete. Database ('$dbname') and tables are ready.\n";

$conn->close();
?>
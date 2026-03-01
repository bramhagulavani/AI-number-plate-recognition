<?php
require __DIR__ . '/../config/db_connect.php';

if ($conn && $conn->ping()) {
    echo "DB connection OK\n";
    echo "Connected to: " . $conn->host_info . "\n";
} else {
    echo "DB connection failed.\n";
}

// Optional: list tables to verify
$res = $conn->query("SHOW TABLES");
if ($res) {
    echo "Tables in '" . $conn->real_escape_string($dbname) . "':\n";
    while ($row = $res->fetch_row()) {
        echo " - " . $row[0] . "\n";
    }
} else {
    echo "Unable to list tables: " . $conn->error . "\n";
}

?>
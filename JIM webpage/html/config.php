<?php
// Database connection settings
define('DB_HOST', '192.168.1.10');  // db-jim IP
define('DB_USER', 'web_user');
define('DB_PASS', 'webpass321');    // Intentionally weak
define('DB_NAME', 'jim_telecom');

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Start session for authentication
session_start();
?>
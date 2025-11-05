<?php
$conn = new mysqli('192.168.1.10', 'web_user', 'webpass321', 'jim_telecom');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully!";
echo "<br>Testing query...";

$result = $conn->query("SELECT COUNT(*) as count FROM customers");
$row = $result->fetch_assoc();
echo "<br>Customer count: " . $row['count'];

$conn->close();
?>
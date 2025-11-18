<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $conn = getDBConnection();
    
    // INTENTIONALLY VULNERABLE - SQL Injection opportunity
    $query = "SELECT * FROM customers WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION['user_id'] = $user['customer_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        
        // Redirect to dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        // Redirect back with error
        header('Location: index.php?error=invalid');
        exit();
    }
    
    $conn->close();
}
?>


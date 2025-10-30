<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?? '';
    $inquiry_type = $_POST['inquiry_type'];
    $message = $_POST['message'];  // INTENTIONALLY NO SANITIZATION - XSS vulnerability
    
    $conn = getDBConnection();
    
    // Store contact form submissions
    $query = "INSERT INTO contact_submissions (name, email, phone, inquiry_type, message, submitted_date) 
              VALUES ('$name', '$email', '$phone', '$inquiry_type', '$message', NOW())";
    
    if ($conn->query($query)) {
        $success = true;
    } else {
        $success = false;
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You - JIM Telecommunications</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Open Sans', sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .message-container { background: white; padding: 3rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; max-width: 600px; }
        h1 { color: #172d5c; margin-bottom: 1rem; }
        p { color: #555; line-height: 1.8; margin-bottom: 2rem; }
        .btn { display: inline-block; background: #172d5c; color: white; padding: 0.75rem 2rem; border-radius: 4px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="message-container">
        <?php if (isset($success) && $success): ?>
            <h1>Thank You!</h1>
            <p>Your message has been received. A member of our team will contact you within 24 hours.</p>
            <p>If you need immediate assistance, please call our support line at (802) 555-2000.</p>
        <?php else: ?>
            <h1>Oops!</h1>
            <p>There was an error submitting your message. Please try again or call us directly.</p>
        <?php endif; ?>
        <a href="index.html" class="btn">Return to Homepage</a>
    </div>
</body>
</html>

<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.html');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Handle new ticket submission (INTENTIONALLY VULNERABLE to XSS)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_ticket'])) {
    $subject = $_POST['subject'];
    $message = $_POST['message']; // NO SANITIZATION - XSS vulnerability
    
    $query = "INSERT INTO support_tickets (customer_id, subject, message, status, created_date) 
              VALUES ($user_id, '$subject', '$message', 'open', NOW())";
    $conn->query($query);
}

// Get customer tickets
$query = "SELECT * FROM support_tickets WHERE customer_id = $user_id ORDER BY created_date DESC";
$tickets = $conn->query($query);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support - JIM Telecommunications</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Open Sans', sans-serif; background: #f5f5f5; }
        .header { background: rgb(46,60,106); color: white; padding: 1.5rem; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 2rem; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #172d5c; margin-bottom: 1.5rem; }
        form div { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #555; }
        input, textarea { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; }
        textarea { min-height: 150px; }
        .btn { background: #172d5c; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; }
        .ticket { padding: 1rem; background: #f8f8f8; margin-bottom: 1rem; border-radius: 4px; }
        .ticket-status { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.9rem; }
        .status-open { background: #3498db; color: white; }
        .status-closed { background: #95a5a6; color: white; }
        .nav { margin-bottom: 2rem; }
        .nav a { color: white; text-decoration: none; margin-right: 1rem; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>JIM Telecommunications</h1>
            <div class="nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="billing.php">Billing</a>
                <a href="support.php">Support</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h1>Submit Support Ticket</h1>
            <form method="POST">
                <div>
                    <label>Subject:</label>
                    <input type="text" name="subject" required>
                </div>
                <div>
                    <label>Message:</label>
                    <textarea name="message" required></textarea>
                </div>
                <button type="submit" name="submit_ticket" class="btn">Submit Ticket</button>
            </form>
        </div>

        <div class="card">
            <h1>Your Support Tickets</h1>
            <?php while ($ticket = $tickets->fetch_assoc()): ?>
            <div class="ticket">
                <div>
                    <strong><?php echo $ticket['subject']; ?></strong>
                    <span class="ticket-status status-<?php echo $ticket['status']; ?>">
                        <?php echo ucfirst($ticket['status']); ?>
                    </span>
                </div>
                <div style="margin-top: 0.5rem; color: #666;">
                    <?php echo $ticket['message']; ?>
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.9rem; color: #999;">
                    Created: <?php echo date('M d, Y g:i A', strtotime($ticket['created_date'])); ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
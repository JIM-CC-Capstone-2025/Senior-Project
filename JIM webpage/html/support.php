<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get customer info for header
$query = "SELECT first_name FROM customers WHERE customer_id = $user_id";
$result = $conn->query($query);
$customer = $result->fetch_assoc();

// Handle new ticket submission (INTENTIONALLY VULNERABLE to XSS)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_ticket'])) {
    $subject = $_POST['subject'];
    $message = $_POST['message']; // NO SANITIZATION - XSS vulnerability
    
    $query = "INSERT INTO support_tickets (customer_id, subject, message, status, created_date) 
              VALUES ($user_id, '$subject', '$message', 'open', NOW())";
    $conn->query($query);
    $ticket_submitted = true;
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
        .header {
            background: rgb(46,60,106);
            color: white;
            padding: 1rem 0;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo img { height: 50px; }
        .nav {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.3s;
        }
        .nav a:hover { opacity: 0.8; }
        .logout-btn {
            background: #ea632a;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
        }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #172d5c;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #ea632a;
            padding-bottom: 0.5rem;
        }
        .form-group { margin-bottom: 1.5rem; }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }
        input, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }
        textarea { min-height: 150px; }
        .btn {
            background: #172d5c;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn:hover { background: #0f1f3d; }
        .ticket {
            padding: 1.5rem;
            background: #f8f8f8;
            margin-bottom: 1rem;
            border-radius: 8px;
            border-left: 4px solid #172d5c;
        }
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .ticket-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .status-open { background: #d1ecf1; color: #0c5460; }
        .status-in_progress { background: #fff3cd; color: #856404; }
        .status-closed { background: #e2e3e5; color: #383d41; }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <img src="images/logodark.svg" alt="JIM Telecom Logo">
            </div>
            <div class="nav">
                <span>Welcome, <?php echo htmlspecialchars($customer['first_name']); ?>!</span>
                <a href="dashboard.php">Dashboard</a>
                <a href="support.php">Support</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h1>Submit Support Ticket</h1>
            
            <?php if (isset($ticket_submitted)): ?>
            <div class="success-message">
                ✓ Your support ticket has been submitted successfully! Our team will respond within 24 hours.
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Subject:</label>
                    <input type="text" name="subject" required>
                </div>
                <div class="form-group">
                    <label>Message:</label>
                    <textarea name="message" required></textarea>
                </div>
                <button type="submit" name="submit_ticket" class="btn">Submit Ticket</button>
            </form>
        </div>

        <div class="card">
            <h1>Your Support Tickets</h1>
            <?php if ($tickets->num_rows > 0): ?>
                <?php while ($ticket = $tickets->fetch_assoc()): ?>
                <div class="ticket">
                    <div class="ticket-header">
                        <strong style="font-size: 1.1rem;"><?php echo htmlspecialchars($ticket['subject']); ?></strong>
                        <span class="ticket-status status-<?php echo $ticket['status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                        </span>
                    </div>
                    <div style="margin-bottom: 1rem; color: #666;">
                        <?php echo htmlspecialchars($ticket['message']); ?>
                    </div>
                    <div style="font-size: 0.9rem; color: #999;">
                        Created: <?php echo date('M d, Y g:i A', strtotime($ticket['created_date'])); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>You haven't submitted any support tickets yet.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h1>Contact Information</h1>
            <p style="margin-bottom: 1rem;"><strong>Need immediate assistance?</strong></p>
            <p><strong>Phone:</strong> (802) 555-2000 (24/7 Technical Support)</p>
            <p><strong>Email:</strong> support@jimtelecom.com</p>
            <p><strong>Business Hours:</strong> Monday-Friday 8:00 AM - 6:00 PM</p>
        </div>
    </div>
</body>
</html>



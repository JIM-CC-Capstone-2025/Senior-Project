<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get customer information
$query = "SELECT * FROM customers WHERE customer_id = $user_id";
$result = $conn->query($query);
$customer = $result->fetch_assoc();

// Get service plan information
$query = "SELECT * FROM service_plans WHERE customer_id = $user_id";
$plans_result = $conn->query($query);

// Get recent bills
$query = "SELECT * FROM invoices WHERE customer_id = $user_id ORDER BY invoice_date DESC LIMIT 5";
$invoices_result = $conn->query($query);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard - JIM Telecommunications</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Open Sans', sans-serif; background: #f5f5f5; }
        .header {
            background: rgb(46,60,106);
            color: white;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .welcome-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #172d5c;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child { border-bottom: none; }
        .label { font-weight: 600; color: #555; }
        .value { color: #333; }
        .btn {
            display: inline-block;
            background: #172d5c;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 1rem;
        }
        .btn:hover { background: #0f1f3d; }
        .logout { color: white; text-decoration: none; padding: 0.5rem 1rem; background: #ea632a; border-radius: 4px; }
        .nav a { color: white; text-decoration: none; margin-right: 1.5rem; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>JIM Telecommunications</h1>
            <div>
                <span>Welcome, <?php echo htmlspecialchars($customer['first_name']); ?>!</span>
                <a href="logout.php" class="logout">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome to Your Account Dashboard</h1>
            <p>Manage your services, view bills, and update your account information.</p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h2>Account Information</h2>
                <div class="info-row">
                    <span class="label">Name:</span>
                    <span class="value"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo htmlspecialchars($customer['email']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Phone:</span>
                    <span class="value"><?php echo htmlspecialchars($customer['phone']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Customer Since:</span>
                    <span class="value"><?php echo date('F Y', strtotime($customer['created_date'])); ?></span>
                </div>
            </div>

            <div class="card">
                <h2>Current Services</h2>
                <?php while ($plan = $plans_result->fetch_assoc()): ?>
                <div class="info-row">
                    <span class="label"><?php echo htmlspecialchars($plan['service_type']); ?>:</span>
                    <span class="value">$<?php echo number_format($plan['monthly_cost'], 2); ?>/mo</span>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="card">
                <h2>Recent Bills</h2>
                <?php while ($invoice = $invoices_result->fetch_assoc()): ?>
                <div class="info-row">
                    <span class="label"><?php echo date('M d, Y', strtotime($invoice['invoice_date'])); ?>:</span>
                    <span class="value">$<?php echo number_format($invoice['amount'], 2); ?></span>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>

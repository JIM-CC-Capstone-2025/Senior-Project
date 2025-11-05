<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.html');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get all invoices
$query = "SELECT * FROM invoices WHERE customer_id = $user_id ORDER BY invoice_date DESC";
$invoices = $conn->query($query);

// Get payment methods
$query = "SELECT * FROM payment_methods WHERE customer_id = $user_id";
$payment_methods = $conn->query($query);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing - JIM Telecommunications</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Open Sans', sans-serif; background: #f5f5f5; }
        .header {
            background: rgb(46,60,106);
            color: white;
            padding: 1.5rem;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #172d5c; margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f8f8; font-weight: 600; color: #172d5c; }
        .status-paid { color: #27ae60; font-weight: 600; }
        .status-pending { color: #f39c12; font-weight: 600; }
        .btn { display: inline-block; background: #172d5c; color: white; padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; margin-top: 1rem; }
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
            <h1>Billing & Payment History</h1>
            <table>
                <thead>
                    <tr>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($invoice = $invoices->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($invoice['invoice_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($invoice['due_date'])); ?></td>
                        <td>$<?php echo number_format($invoice['amount'], 2); ?></td>
                        <td class="<?php echo ($invoice['status'] == 'paid') ? 'status-paid' : 'status-pending'; ?>">
                            <?php echo ucfirst($invoice['status']); ?>
                        </td>
                        <td><a href="invoice.php?id=<?php echo $invoice['invoice_id']; ?>">View</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h1>Payment Methods</h1>
            <?php while ($method = $payment_methods->fetch_assoc()): ?>
            <div style="padding: 1rem; background: #f8f8f8; margin-bottom: 1rem; border-radius: 4px;">
                <strong>Card ending in <?php echo substr($method['card_number'], -4); ?></strong><br>
                Expires: <?php echo $method['expiry_date']; ?>
            </div>
            <?php endwhile; ?>
            <a href="add-payment.php" class="btn">Add Payment Method</a>
        </div>
    </div>
</body>
</html>

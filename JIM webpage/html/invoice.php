<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$invoice_id = $_GET['id'] ?? 0;

// Get customer info
$query = "SELECT * FROM customers WHERE customer_id = $user_id";
$result = $conn->query($query);
$customer = $result->fetch_assoc();

// Get invoice (VULNERABLE - no validation that invoice belongs to user)
$query = "SELECT * FROM invoices WHERE invoice_id = $invoice_id";
$invoice_result = $conn->query($query);

if ($invoice_result->num_rows == 0) {
    header('Location: dashboard.php');
    exit();
}

$invoice = $invoice_result->fetch_assoc();

// Get service plans for this customer
$query = "SELECT * FROM service_plans WHERE customer_id = $user_id";
$plans_result = $conn->query($query);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $invoice_id; ?> - JIM Telecommunications</title>
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
        .nav { display: flex; align-items: center; gap: 2rem; }
        .nav a { color: white; text-decoration: none; font-weight: 600; }
        .logout-btn { background: #ea632a; padding: 0.5rem 1.5rem; border-radius: 4px; }
        .container { max-width: 900px; margin: 2rem auto; padding: 0 2rem; }
        .invoice-card {
            background: white;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 3px solid #172d5c;
        }
        .company-info h1 { color: #172d5c; margin-bottom: 0.5rem; }
        .invoice-number {
            text-align: right;
        }
        .invoice-number h2 {
            color: #172d5c;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
        .status-paid { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        .detail-section h3 {
            color: #172d5c;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        .detail-section p {
            margin-bottom: 0.5rem;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f8f8;
            font-weight: 600;
            color: #172d5c;
        }
        .totals {
            text-align: right;
            margin-top: 2rem;
        }
        .totals-row {
            display: flex;
            justify-content: flex-end;
            padding: 0.5rem 0;
        }
        .totals-label {
            width: 150px;
            font-weight: 600;
            color: #555;
        }
        .totals-value {
            width: 150px;
            text-align: right;
            color: #333;
        }
        .total-amount {
            font-size: 1.5rem;
            color: #172d5c;
            font-weight: 700;
            border-top: 2px solid #172d5c;
            padding-top: 1rem;
            margin-top: 0.5rem;
        }
        .btn {
            background: #172d5c;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-right: 1rem;
        }
        .btn:hover { background: #0f1f3d; }
        .btn-secondary { background: #6c757d; }
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
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="invoice-card">
            <div class="invoice-header">
                <div class="company-info">
                    <h1>JIM Telecommunications</h1>
                    <p>123 Network Drive</p>
                    <p>Rutland, VT 05701</p>
                    <p>Phone: (802) 555-1000</p>
                </div>
                <div class="invoice-number">
                    <h2>INVOICE #<?php echo str_pad($invoice['invoice_id'], 6, '0', STR_PAD_LEFT); ?></h2>
                    <span class="status-badge status-<?php echo $invoice['status']; ?>">
                        <?php echo ucfirst($invoice['status']); ?>
                    </span>
                </div>
            </div>

            <div class="details-grid">
                <div class="detail-section">
                    <h3>Bill To:</h3>
                    <p><strong><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></strong></p>
                    <p><?php echo htmlspecialchars($customer['address']); ?></p>
                    <p><?php echo htmlspecialchars($customer['city'] . ', ' . $customer['state'] . ' ' . $customer['zip_code']); ?></p>
                    <p><?php echo htmlspecialchars($customer['email']); ?></p>
                    <p><?php echo htmlspecialchars($customer['phone']); ?></p>
                </div>
                <div class="detail-section">
                    <h3>Invoice Details:</h3>
                    <p><strong>Invoice Date:</strong> <?php echo date('F d, Y', strtotime($invoice['invoice_date'])); ?></p>
                    <p><strong>Due Date:</strong> <?php echo date('F d, Y', strtotime($invoice['due_date'])); ?></p>
                    <?php if ($invoice['payment_date']): ?>
                    <p><strong>Payment Date:</strong> <?php echo date('F d, Y', strtotime($invoice['payment_date'])); ?></p>
                    <?php endif; ?>
                    <p><strong>Customer ID:</strong> #<?php echo str_pad($customer['customer_id'], 6, '0', STR_PAD_LEFT); ?></p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Service Description</th>
                        <th>Service Period</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($plan = $plans_result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($plan['service_type']); ?></strong><br>
                            <small><?php echo htmlspecialchars($plan['plan_name']); ?></small>
                        </td>
                        <td><?php echo date('M Y', strtotime($invoice['invoice_date'])); ?></td>
                        <td style="text-align: right;">$<?php echo number_format($plan['monthly_cost'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="totals">
                <div class="totals-row">
                    <div class="totals-label">Subtotal:</div>
                    <div class="totals-value">$<?php echo number_format($invoice['amount'], 2); ?></div>
                </div>
                <div class="totals-row">
                    <div class="totals-label">Tax (0%):</div>
                    <div class="totals-value">$0.00</div>
                </div>
                <div class="totals-row total-amount">
                    <div class="totals-label">Total Amount Due:</div>
                    <div class="totals-value">$<?php echo number_format($invoice['amount'], 2); ?></div>
                </div>
            </div>

            <div style="margin-top: 3rem;">
                <?php if ($invoice['status'] != 'paid'): ?>
                <button class="btn" onclick="window.print()">Pay Now</button>
                <?php endif; ?>
                <button class="btn-secondary btn" onclick="window.print()">Print Invoice</button>
                <a href="dashboard.php" class="btn-secondary btn">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>



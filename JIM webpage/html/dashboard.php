<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: index.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Handle account information updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    
    // INTENTIONALLY VULNERABLE - SQL Injection
    $query = "UPDATE customers SET email='$email', phone='$phone', address='$address', 
              city='$city', state='$state', zip_code='$zip_code' WHERE customer_id=$user_id";
    
    if ($conn->query($query)) {
        $update_success = true;
    }
}

// Get customer information
$query = "SELECT * FROM customers WHERE customer_id = $user_id";
$result = $conn->query($query);
$customer = $result->fetch_assoc();

// Get service plans
$query = "SELECT * FROM service_plans WHERE customer_id = $user_id";
$plans_result = $conn->query($query);

// Get recent invoices
$query = "SELECT * FROM invoices WHERE customer_id = $user_id ORDER BY invoice_date DESC LIMIT 5";
$invoices_result = $conn->query($query);

// Get payment methods
$query = "SELECT * FROM payment_methods WHERE customer_id = $user_id";
$payment_result = $conn->query($query);

// Get recent support tickets
$query = "SELECT * FROM support_tickets WHERE customer_id = $user_id ORDER BY created_date DESC LIMIT 3";
$tickets_result = $conn->query($query);

// Get data usage
$query = "SELECT * FROM data_usage WHERE customer_id = $user_id ORDER BY billing_period DESC LIMIT 1";
$usage_result = $conn->query($query);
$usage = $usage_result->fetch_assoc();

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
        
        /* Header */
        .header {
            background: rgb(46,60,106);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
        .user-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .user-name { font-weight: 600; }
        
        /* Navigation Tabs */
        .nav-tabs {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .nav-tabs-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            gap: 0;
        }
        .nav-tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            font-weight: 600;
            color: #555;
        }
        .nav-tab:hover { background: #f8f8f8; }
        .nav-tab.active {
            color: #172d5c;
            border-bottom-color: #ea632a;
        }
        
        /* Container */
        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        /* Tab Content */
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        /* Cards */
        .card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .card h2 {
            color: #172d5c;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            border-bottom: 2px solid #ea632a;
            padding-bottom: 0.5rem;
        }
        
        /* Grid Layouts */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }
        
        /* Info Rows */
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child { border-bottom: none; }
        .label { font-weight: 600; color: #555; }
        .value { color: #333; }
        
        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            background: #172d5c;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover { background: #0f1f3d; transform: translateY(-2px); }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover { background: #c82333; }
        .logout-btn {
            background: #ea632a;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            color: white;
        }
        .logout-btn:hover { background: #d45521; }
        
        /* Success Messages */
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            border: 1px solid #c3e6cb;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-active { background: #d4edda; color: #155724; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .status-open { background: #d1ecf1; color: #0c5460; }
        .status-closed { background: #e2e3e5; color: #383d41; }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
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
        tr:hover { background: #f8f8f8; }
        
        /* Usage Bar */
        .usage-bar {
            width: 100%;
            height: 30px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            margin: 1rem 0;
        }
        .usage-fill {
            height: 100%;
            background: linear-gradient(90deg, #172d5c, #ea632a);
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .nav-tabs-content { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <img src="images/logodark.svg" alt="JIM Telecom Logo">
            </div>
            <div class="user-info">
                <span class="user-name">Welcome, <?php echo htmlspecialchars($customer['first_name']); ?>!</span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="nav-tabs">
        <div class="nav-tabs-content">
            <div class="nav-tab active" onclick="switchTab('overview')">Overview</div>
            <div class="nav-tab" onclick="switchTab('services')">My Services</div>
            <div class="nav-tab" onclick="switchTab('billing')">Billing</div>
            <div class="nav-tab" onclick="switchTab('support')">Support</div>
            <div class="nav-tab" onclick="switchTab('account')">Account Settings</div>
        </div>
    </div>

    <div class="container">
        <!-- Overview Tab -->
        <div id="overview" class="tab-content active">
            <div class="card">
                <h2>Account Overview</h2>
                <div class="grid-2">
                    <div>
                        <div class="info-row">
                            <span class="label">Account Number:</span>
                            <span class="value">#<?php echo str_pad($customer['customer_id'], 6, '0', STR_PAD_LEFT); ?></span>
                        </div>
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
                    </div>
                    <div>
                        <div class="info-row">
                            <span class="label">Address:</span>
                            <span class="value"><?php echo htmlspecialchars($customer['address']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">City, State:</span>
                            <span class="value"><?php echo htmlspecialchars($customer['city'] . ', ' . $customer['state']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">ZIP Code:</span>
                            <span class="value"><?php echo htmlspecialchars($customer['zip_code']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Customer Since:</span>
                            <span class="value"><?php echo date('F Y', strtotime($customer['created_date'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($usage): ?>
            <div class="card">
                <h2>Current Data Usage</h2>
                <p><strong>Billing Period:</strong> <?php echo htmlspecialchars($usage['billing_period']); ?></p>
                <?php 
                $percentage = min(($usage['data_consumed'] / 1000) * 100, 100);
                ?>
                <div class="usage-bar">
                    <div class="usage-fill" style="width: <?php echo $percentage; ?>%">
                        <?php echo number_format($usage['data_consumed'], 2); ?> GB
                    </div>
                </div>
                <?php if ($usage['overage_charges'] > 0): ?>
                <p style="color: #dc3545; font-weight: 600;">
                    Overage Charges: $<?php echo number_format($usage['overage_charges'], 2); ?>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="card">
                <h2>Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button class="btn" onclick="switchTab('billing')">Pay Bill</button>
                    <button class="btn-secondary btn" onclick="switchTab('support')">Contact Support</button>
                    <button class="btn-secondary btn" onclick="switchTab('account')">Update Information</button>
                </div>
            </div>
        </div>

        <!-- Services Tab -->
        <div id="services" class="tab-content">
            <div class="card">
                <h2>Active Services</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Service Type</th>
                            <th>Plan Name</th>
                            <th>Monthly Cost</th>
                            <th>Activation Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $plans_result->data_seek(0);
                        while ($plan = $plans_result->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($plan['service_type']); ?></td>
                            <td><?php echo htmlspecialchars($plan['plan_name']); ?></td>
                            <td>$<?php echo number_format($plan['monthly_cost'], 2); ?>/month</td>
                            <td><?php echo date('M d, Y', strtotime($plan['activation_date'])); ?></td>
                            <td><span class="status-badge status-<?php echo $plan['status']; ?>"><?php echo ucfirst($plan['status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div style="margin-top: 2rem;">
                    <a href="services.html" class="btn">Upgrade Service</a>
                    <a href="contact.html" class="btn-secondary btn">Request Changes</a>
                </div>
            </div>
        </div>

        <!-- Billing Tab -->
        <div id="billing" class="tab-content">
            <div class="card">
                <h2>Recent Invoices</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $invoices_result->data_seek(0);
                        while ($invoice = $invoices_result->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($invoice['invoice_date'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($invoice['due_date'])); ?></td>
                            <td><strong>$<?php echo number_format($invoice['amount'], 2); ?></strong></td>
                            <td><span class="status-badge status-<?php echo $invoice['status']; ?>"><?php echo ucfirst($invoice['status']); ?></span></td>
                            <td><?php echo $invoice['payment_date'] ? date('M d, Y', strtotime($invoice['payment_date'])) : '-'; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Payment Methods</h2>
                <?php 
                $payment_result->data_seek(0);
                while ($payment = $payment_result->fetch_assoc()): 
                ?>
                <div style="background: #f8f8f8; padding: 1.5rem; margin-bottom: 1rem; border-radius: 8px;">
                    <strong>Card ending in <?php echo substr($payment['card_number'], -4); ?></strong><br>
                    Expires: <?php echo htmlspecialchars($payment['expiry_date']); ?><br>
                    <small><?php echo htmlspecialchars($payment['billing_address']); ?></small>
                </div>
                <?php endwhile; ?>
                <a href="add-payment.php" class="btn">Add Payment Method</a>
            </div>
        </div>

        <!-- Support Tab -->
        <div id="support" class="tab-content">
            <div class="card">
                <h2>Recent Support Tickets</h2>
                <?php 
                $tickets_result->data_seek(0);
                if ($tickets_result->num_rows > 0):
                    while ($ticket = $tickets_result->fetch_assoc()): 
                ?>
                <div style="background: #f8f8f8; padding: 1.5rem; margin-bottom: 1rem; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <strong><?php echo htmlspecialchars($ticket['subject']); ?></strong>
                        <span class="status-badge status-<?php echo $ticket['status']; ?>"><?php echo ucfirst($ticket['status']); ?></span>
                    </div>
                    <p style="color: #666; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($ticket['message']); ?></p>
                    <small style="color: #999;">Created: <?php echo date('M d, Y g:i A', strtotime($ticket['created_date'])); ?></small>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <p>No support tickets found.</p>
                <?php endif; ?>
                <a href="support.php" class="btn">Create New Ticket</a>
            </div>

            <div class="card">
                <h2>Contact Support</h2>
                <div class="info-row">
                    <span class="label">Phone Support:</span>
                    <span class="value">(802) 555-2000</span>
                </div>
                <div class="info-row">
                    <span class="label">Email Support:</span>
                    <span class="value">support@jimtelecom.com</span>
                </div>
                <div class="info-row">
                    <span class="label">Hours:</span>
                    <span class="value">24/7 Technical Support</span>
                </div>
            </div>
        </div>

        <!-- Account Settings Tab -->
        <div id="account" class="tab-content">
            <div class="card">
                <h2>Update Account Information</h2>
                
                <?php if (isset($update_success)): ?>
                <div class="success-message">
                    ✓ Account information updated successfully!
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name:</label>
                            <input type="text" value="<?php echo htmlspecialchars($customer['first_name']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Last Name:</label>
                            <input type="text" value="<?php echo htmlspecialchars($customer['last_name']); ?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address:</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number:</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Street Address:</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>City:</label>
                            <input type="text" name="city" value="<?php echo htmlspecialchars($customer['city']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>State:</label>
                            <input type="text" name="state" value="<?php echo htmlspecialchars($customer['state']); ?>" maxlength="2" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>ZIP Code:</label>
                        <input type="text" name="zip_code" value="<?php echo htmlspecialchars($customer['zip_code']); ?>" required>
                    </div>

                    <button type="submit" name="update_info" class="btn">Save Changes</button>
                </form>
            </div>

            <div class="card">
                <h2>Security Settings</h2>
                <a href="change-password.php" class="btn">Change Password</a>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }
    </script>
</body>
</html>





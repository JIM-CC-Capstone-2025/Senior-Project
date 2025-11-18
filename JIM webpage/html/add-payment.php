<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get customer info
$query = "SELECT first_name FROM customers WHERE customer_id = $user_id";
$result = $conn->query($query);
$customer = $result->fetch_assoc();

// Handle form submission (INTENTIONALLY VULNERABLE - stores plaintext card data)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    $billing_address = $_POST['billing_address'];
    
    // VULNERABLE - No encryption, SQL injection possible
    $query = "INSERT INTO payment_methods (customer_id, card_number, expiry_date, cvv, billing_address) 
              VALUES ($user_id, '$card_number', '$expiry_date', '$cvv', '$billing_address')";
    
    if ($conn->query($query)) {
        $success = true;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Payment Method - JIM Telecommunications</title>
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
        .container { max-width: 800px; margin: 2rem auto; padding: 0 2rem; }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #172d5c;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #ea632a;
            padding-bottom: 0.5rem;
        }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #555; }
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }
        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
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
        }
        .btn:hover { background: #0f1f3d; }
        .btn-secondary {
            background: #6c757d;
            margin-left: 1rem;
        }
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
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h1>Add Payment Method</h1>

            <?php if (isset($success)): ?>
            <div class="success-message">
                ✓ Payment method added successfully!
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Card Number:</label>
                    <input type="text" name="card_number" placeholder="1234 5678 9012 3456" maxlength="16" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Expiry Date (MM/YYYY):</label>
                        <input type="text" name="expiry_date" placeholder="12/2026" required>
                    </div>
                    <div class="form-group">
                        <label>CVV:</label>
                        <input type="text" name="cvv" placeholder="123" maxlength="4" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Billing Address:</label>
                    <input type="text" name="billing_address" placeholder="123 Main St, City, State ZIP" required>
                </div>

                <button type="submit" class="btn">Add Payment Method</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>



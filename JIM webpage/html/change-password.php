<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Get customer info
$query = "SELECT first_name, password FROM customers WHERE customer_id = $user_id";
$result = $conn->query($query);
$customer = $result->fetch_assoc();

// Handle password change (INTENTIONALLY VULNERABLE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check current password (plaintext comparison - vulnerable)
    if ($current_password !== $customer['password']) {
        $error = "Current password is incorrect";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        // Update password in plaintext (INTENTIONALLY INSECURE)
        $query = "UPDATE customers SET password = '$new_password' WHERE customer_id = $user_id";
        
        if ($conn->query($query)) {
            $success = true;
        } else {
            $error = "Failed to update password";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - JIM Telecommunications</title>
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
        .container { max-width: 600px; margin: 2rem auto; padding: 0 2rem; }
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
        .error-message {
            background: #f8d7da;
            color: #721c24;
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
            <h1>Change Password</h1>

            <?php if (isset($success)): ?>
            <div class="success-message">
                ✓ Password changed successfully!
            </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <div class="error-message">
                ✗ <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Current Password:</label>
                    <input type="password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label>New Password:</label>
                    <input type="password" name="new_password" required>
                    <small style="color: #666;">Password must be at least 6 characters</small>
                </div>

                <div class="form-group">
                    <label>Confirm New Password:</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn">Change Password</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>



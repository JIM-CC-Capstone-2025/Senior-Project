<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.html');
    exit();
}

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Handle profile picture upload (INTENTIONALLY VULNERABLE)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    $file_name = basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . $file_name;
    
    // INTENTIONALLY NO FILE TYPE VALIDATION - allows PHP file uploads
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        $query = "UPDATE customers SET profile_picture = '$target_file' WHERE customer_id = $user_id";
        $conn->query($query);
        $upload_success = true;
    }
}

// Handle account updates (INTENTIONALLY VULNERABLE to SQL injection)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_account'])) {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    // VULNERABLE SQL - no parameterization
    $query = "UPDATE customers SET email = '$email', phone = '$phone', address = '$address' WHERE customer_id = $user_id";
    $conn->query($query);
    $update_success = true;
}

// Get customer information
$query = "SELECT * FROM customers WHERE customer_id = $user_id";
$result = $conn->query($query);
$customer = $result->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Settings - JIM Telecommunications</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Open Sans', sans-serif; background: #f5f5f5; }
        .header { background: rgb(46,60,106); color: white; padding: 1.5rem; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 2rem; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #172d5c; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #555; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; }
        .btn { background: #172d5c; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0f1f3d; }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
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
            <h1>Account Settings</h1>
            
            <?php if (isset($update_success)): ?>
            <div class="success">Account information updated successfully!</div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" value="<?php echo htmlspecialchars($customer['username']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Email Address:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Phone Number:</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
                </div>

                <div class="form-group">
                    <label>Address:</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>">
                </div>

                <button type="submit" name="update_account" class="btn">Update Information</button>
            </form>
        </div>

        <div class="card">
            <h1>Profile Picture</h1>
            
            <?php if (isset($upload_success)): ?>
            <div class="success">Profile picture uploaded successfully!</div>
            <?php endif; ?>

            <?php if (!empty($customer['profile_picture'])): ?>
            <img src="<?php echo htmlspecialchars($customer['profile_picture']); ?>" style="max-width: 200px; margin-bottom: 1rem;">
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Upload New Picture:</label>
                    <input type="file" name="profile_picture" required>
                </div>
                <button type="submit" class="btn">Upload</button>
            </form>
        </div>
    </div>
</body>
</html>
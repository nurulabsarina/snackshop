<?php
require 'conn/db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $message = "Username already exists!";
        } else {
            // Insert new admin
            $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $password]);
            $message = "Registration successful!";
            $message_type = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SNACK SHOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #f19035ff, #ffaa00);
            font-family: "Segoe UI", sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .register-header {
            background: #1a1a1a;
            color: #ff7b00;
            padding: 20px;
            text-align: center;
        }
        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }
        .register-body {
            padding: 30px;
        }
        .btn-orange { 
            background: #ff7b00; 
            border: none;
            color: white;
            font-weight: bold;
            padding: 12px;
        }
        .btn-orange:hover { 
            background: #1a1a1a; 
            color: #ff7b00;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <img src="images/snackshop.jpg" alt="Snack Shop Logo" class="logo">
            <h3>SNACK SHOP</h3>
            <p class="mb-0">Admin Registration</p>
        </div>
        <div class="register-body">
            <?php if ($message != ""): ?>
                <div class="alert alert-<?php echo $message_type; ?> text-center"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Enter username">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Enter password">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="Confirm password">
                </div>
                <button type="submit" class="btn btn-orange w-100">Register</button>
            </form>
            <div class="text-center mt-3 small text-muted">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>

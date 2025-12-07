<?php
session_start();
require 'conn/db.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Query the admins table
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SNACK SHOP</title>
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
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
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
        .login-body {
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
    <div class="login-container">
        <div class="login-header">
            <img src="images/snackshop.jpg" alt="Snack Shop Logo" class="logo">
            <h3>SNACK SHOP</h3>
            <p class="mb-0">Admin Panel Login</p>
        </div>
        <div class="login-body">
            <?php if ($error != ""): ?>
                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
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
                <button type="submit" class="btn btn-orange w-100">Login</button>
            </form>
            <div class="text-center mt-3 small text-muted">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>

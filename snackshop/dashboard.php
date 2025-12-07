<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}
require 'conn/db.php';

// Get real data from database
$total_products = $pdo->query("SELECT COUNT(*) as total FROM products")->fetch()['total'];
$total_orders = $pdo->query("SELECT COUNT(*) as total FROM orders")->fetch()['total'];
$top_products = $pdo->query("SELECT product_name,
    COALESCE((SELECT SUM(total) FROM orders WHERE orders.product_name = products.product_name), 0) AS revenue
    FROM products ORDER BY revenue DESC")->fetchAll();
    
$current_page = 'dashboard'; // For active sidebar

$labels = [];
$data = [];
foreach($top_products as $p) {
    $labels[] = $p['product_name'];
    $data[] = $p['revenue'];
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Snack Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="sidebar.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header text-center">
            <img src="images/snackshop.jpg" width="50" class="mb-2" alt="Snack Shop Logo">
            <h3>SNACK SHOP</h3>
            <p>Admin Panel</p>
        </div>
        <div class="sidebar-nav">
            <a href="dashboard.php" class="<?= $current_page == 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="vieworders.php" class="<?= $current_page == 'orders' ? 'active' : '' ?>">View Orders</a>
            <a href="products.php" class="<?= $current_page == 'products' ? 'active' : '' ?>">Manage Products</a>
            <a href="viewproducts.php" class="<?= $current_page == 'viewproducts' ? 'active' : '' ?>">View Products</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard Overview</h2>
            <span class="text-muted">Welcome, Admin!</span>
        </div>

        <!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card orange-card">
            <div class="card-body text-center">
                <h5>Total Products</h5>
                <div class="kpi-number"><?php echo $total_products; ?></div>
                <small>Available snacks</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card orange-card">
            <div class="card-body text-center">
                <h5>Total Customers</h5>
                <div class="kpi-number"><?php echo $total_orders; ?></div>
                <small>All time</small>
            </div>
        </div>
    </div>
</div>
         <!-- Chart -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Top Revenue Products</h5>
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>
    </div>

    <?php include 'footer.php'; ?>

<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Revenue (RM)',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: '#ff7b00',
                borderColor: '#1a1a1a',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 5
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            },
            elements: {
                bar: {
                    minBarLength: 5
                }
            }
        }
    });
</script>

</body>

</html>

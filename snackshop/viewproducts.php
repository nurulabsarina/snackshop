<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}
require 'conn/db.php';

// Simplified query: Calculate current_stock from database
$products = $pdo->query("
    SELECT DISTINCT p.*,
        COALESCE(o.total_sold, 0) AS sold
    FROM products p
    LEFT JOIN (
        SELECT product_name, SUM(quantity) AS total_sold
        FROM orders
        GROUP BY product_name
    ) o ON p.product_name = o.product_name
    ORDER BY p.product_id
")->fetchAll();

// Calculate current_stock as original_stock minus sold
foreach ($products as &$p) {
    $p['current_stock'] = $p['original_stock'] - $p['sold'];
}

$current_page = 'viewproducts';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products - Snack Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="sidebar.css">
    <script src="https://kit.fontawesome.com/2d3e3f1c54.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
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
        <h2>View Products</h2>
        <p class="text-muted">Browse all available snack products (Read Only)</p>

        <!-- Products Table -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">All Products</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Price (RM)</th>
                            <th>Original Stock</th>
                            <th>Sold</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if (count($products) == 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No products available.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= $product['product_id'] ?></td>
                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                    <td>RM <?= number_format($product['price'], 2) ?></td>
                                    <td><?= $product['original_stock'] ?></td>
                                    <td><?= $product['sold'] ?></td>
                                    <td>
                                        <?= $product['current_stock'] ?>  <!-- Should now show decreased stock -->
                                        <?php if ($product['current_stock'] < 10 && $product['current_stock'] > 0): ?>
                                            <span class="badge bg-warning ms-1">Low Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['current_stock'] > 0): ?>
                                            <span class="badge bg-success">In Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Products Summary</h5>
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="text-orange"><?= count($products) ?></h4>
                        <p class="text-muted">Total Products</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-success"><?= array_sum(array_column($products, 'current_stock')) ?></h4>
                        <p class="text-muted">Total Stock</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-primary"><?= array_sum(array_column($products, 'sold')) ?></h4>
                        <p class="text-muted">Total Sold</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

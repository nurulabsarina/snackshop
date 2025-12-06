<?php
session_start();
require 'conn/db.php';

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $customer_name = $_POST['customer_name'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Get product details with calculated current_stock
    $product = $pdo->prepare("
        SELECT p.*,
            (p.original_stock - COALESCE(o.total_sold, 0)) AS current_stock
        FROM products p
        LEFT JOIN (
            SELECT product_name, SUM(quantity) AS total_sold
            FROM orders
            GROUP BY product_name
        ) o ON p.product_name = o.product_name
        WHERE p.product_id = ?
    ");
    $product->execute([$product_id]);
    $product = $product->fetch();

    if ($product && $product['current_stock'] >= $quantity) {
        $total = $product['price'] * $quantity;

        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, product_name, quantity, total, order_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$customer_name, $product['product_name'], $quantity, $total, $_POST['order_date']]);

        header("Location: vieworders.php?success=1");
        exit;
    } else {
        $_SESSION['error'] = 'out_of_stock';
        $_SESSION['error_product'] = $product['product_name'];
        header("Location: vieworders.php");
        exit;
    }
}

// Get products for dropdown with calculated current_stock
$products = $pdo->query("
    SELECT p.product_id, p.product_name,
        (p.original_stock - COALESCE(o.total_sold, 0)) AS current_stock
    FROM products p
    LEFT JOIN (
        SELECT product_name, SUM(quantity) AS total_sold
        FROM orders
        GROUP BY product_name
    ) o ON p.product_name = o.product_name
    ORDER BY p.product_name
")->fetchAll();

// Get orders from database
$orders = $pdo->query("SELECT * FROM orders ORDER BY order_id ASC")->fetchAll();

// Calculate statistics from database
$total_revenue = $pdo->query("SELECT SUM(total) as revenue FROM orders")->fetch()['revenue'] ?? 0;

$current_page = 'orders';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders - SNACK SHOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="sidebar.css">
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
        <h2>Order History</h2>
        <p class="text-muted">View all customer orders</p>

        <!-- Add Order Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add New Order</h5>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">Order placed successfully!</div>
                <?php elseif (isset($_SESSION['error']) && $_SESSION['error'] == 'out_of_stock'): ?>
                    <div class="alert alert-danger">Insufficient stock for <?php echo htmlspecialchars($_SESSION['error_product'] ?? 'this product'); ?>.</div>
                    <?php unset($_SESSION['error'], $_SESSION['error_product']); ?>
                <?php endif; ?>
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Customer Name:</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Product:</label>
                            <select name="product_id" class="form-select" required>
                                <option value="">Select Product</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['product_id'] ?>" data-stock="<?= $p['current_stock'] ?>" <?= $p['current_stock'] <= 0 ? 'disabled style="color: red;"' : '' ?>>
                                        <?= htmlspecialchars($p['product_name']) ?> (Current stock: <?= $p['current_stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantity:</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Order Date:</label>
                            <input type="date" name="order_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" name="place_order" class="btn btn-orange w-100">Place Order</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Recent Orders</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Total (RM)</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($orders) == 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No orders found. Orders will appear here when customers make purchases.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td>RM <?php echo number_format($order['total'], 2); ?></td>
                                    <td><?php echo $order['order_date']; ?></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Statistics -->
        <div class="row mt-4 justify-content-center">
            <div class="col-md-5">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Total Orders</h5>
                        <h3 class="text-orange"><?php echo count($orders); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Total Revenue</h5>
                        <h3 class="text-success">RM <?php echo number_format($total_revenue ?? 0, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

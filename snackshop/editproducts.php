<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}
require 'conn/db.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = $_GET['id'];

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}

// Calculate current stock
$sold = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as sold FROM orders WHERE product_name = ?");
$sold->execute([$product['product_name']]);
$sold_count = $sold->fetch()['sold'];
$current_stock = $product['original_stock'] - $sold_count;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $product_name = trim($_POST['product_name']);
    $price = $_POST['price'];
    $original_stock = $_POST['original_stock'];
    $image_url = trim($_POST['image_url']);

    // Check if product name already exists (excluding current product)
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_name = ? AND product_id != ?");
    $check->execute([$product_name, $product_id]);
    if ($check->fetchColumn() > 0) {
        $error = "Product name already exists";
    } else {
        // Update product
        $update_stmt = $pdo->prepare("UPDATE products SET product_name = ?, price = ?, original_stock = ?, image_url = ? WHERE product_id = ?");
        $update_stmt->execute([$product_name, $price, $original_stock, $image_url, $product_id]);
        header("Location: products.php");
        exit;
    }
}

$current_page = 'products';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Snack Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="sidebar.css">
    <style>
        .btn-brown {
            background-color: #231709;
            border-color: #2c1c0aff;
            color: white;
        }
        .btn-brown:hover {
            background-color: #3d2414;
            border-color: #3d2414;
        }
        .btn-orange {
            background-color: #ff7b00;
            border-color: #ff7b00;
            color: white;
        }
        .btn-orange:hover {
            background-color: #e66a00;
            border-color: #e66a00;
        }
    </style>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Product</h2>
            <a href="products.php" class="btn btn-secondary">Back to Products</a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Product Details</h5>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="product_name" class="form-label">Product Name</label>
                                    <input type="text" id="product_name" name="product_name" class="form-control"
                                           value="<?php echo htmlspecialchars($product['product_name']); ?>" required minlength="2" maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label for="price" class="form-label">Price (RM)</label>
                                    <input type="number" id="price" step="0.01" name="price" class="form-control"
                                           value="<?php echo $product['price']; ?>" required min="0.01" max="9999.99">
                                </div>
                                <div class="col-md-6">
                                    <label for="original_stock" class="form-label">Original Stock</label>
                                    <input type="number" id="original_stock" name="original_stock" class="form-control"
                                           value="<?php echo $product['original_stock']; ?>" required min="0" max="99999">
                                </div>
                                <div class="col-md-6">
                                    <label for="current_stock" class="form-label">Current Stock</label>
                                    <input type="number" id="current_stock" class="form-control" value="<?php echo $current_stock; ?>" readonly>
                                    <small class="text-muted">Calculated: Original - Sold</small>
                                </div>
                                <div class="col-12">
                                    <label for="image_url" class="form-label">Image URL</label>
                                    <input type="text" id="image_url" name="image_url" class="form-control"
                                           value="<?php echo htmlspecialchars($product['image_url']); ?>">
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="update_product" class="btn btn-orange">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        Update Product
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Current Product Image</h6>
                        <img src="images/<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid mt-3" alt="Product Image" style="max-height: 200px; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show loading state on form submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = e.target.querySelector('button[type="submit"]');
            const spinner = btn.querySelector('.spinner-border');
            btn.disabled = true;
            spinner.classList.remove('d-none');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
        });
    </script>
</body>
</html>


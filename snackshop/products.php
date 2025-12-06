<?php
require 'conn/db.php';

// Add product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_name = ?");
    $check->execute([$_POST['product_name']]);
    if ($check->fetchColumn() > 0) {
        header("Location: products.php?error=Product name already exists");
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO products (product_name, price, original_stock, image_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['product_name'], $_POST['price'], $_POST['original_stock'], $_POST['image_url']]);
    header("Location: products.php");
    exit;
}



// Delete product
if (isset($_GET['delete_id'])) {
    $pdo->prepare("DELETE FROM products WHERE product_id = ?")->execute([$_GET['delete_id']]);
    header("Location: products.php");
    exit;
}

// Get products with current stock
$products = $pdo->query("
    SELECT p.*, COALESCE(SUM(o.quantity), 0) as sold, (p.original_stock - COALESCE(SUM(o.quantity), 0)) as current_stock
    FROM products p
    LEFT JOIN orders o ON p.product_name = o.product_name
    GROUP BY p.product_id
    ORDER BY p.product_id
")->fetchAll();

$current_page = 'products';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Snack Shop</title>
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
        .btn-tawny {
            background-color: #80471C;
            border-color: #80471C;
            color: white;
        }
        .btn-tawny:hover {
            background-color: #a05a25;
            border-color: #a05a25;
        }
        .btn-soft-orange {
            background-color: #ffcc80;
            border-color: #ffcc80;
            color: #333;
        }
        .btn-soft-orange:hover {
            background-color: #ffb74d;
            border-color: #ffb74d;
        }
        .stock-badge {
            font-size: 0.8em;
            padding: 0.2em 0.5em;
        }
        .add-stock-form {
            display: inline-block;
            margin-top: 0.5em;
        }
        .add-stock-form input {
            width: 60px;
            display: inline-block;
            margin-right: 0.5em;
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
        <h2>Manage Products</h2>
        <p class="text-muted">Add, edit, or remove snack products</p>

        <!-- Add New Product Form -->
        <div class="card p-4 mb-4">
            <h5 class="card-title">Add New Product</h5>
            <form method="POST">
                <div class="row g-3 mt-1">
                    <div class="col-md-3">
                        <input type="text" name="product_name" class="form-control" placeholder="Product Name" required minlength="2" maxlength="100">
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="Price (RM)" required min="0.01" max="9999.99">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="original_stock" class="form-control" placeholder="Original Stock" required min="0" max="99999">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="image_url" class="form-control" placeholder="Image URL" value="default.jpg">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="add_product" class="btn btn-orange w-100" id="addProductBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Add Product
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Products Cards -->
        <div class="card p-4">
            <h5 class="card-title">Current Products</h5>
            <?php if (count($products) == 0): ?>
                <p class="text-muted py-4">No products found. Add your first product above!</p>
            <?php else: ?>
                <div class="row mt-3">
                    <?php foreach ($products as $p): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="images/<?= $p['image_url'] ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($p['product_name']) ?>" style="max-height: 200px; object-fit: contain; background-color: #f8f9fa;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">
                                        <?= htmlspecialchars($p['product_name']) ?>
                                        <?php if ($p['current_stock'] <= 0): ?>
                                            <span class="badge bg-danger stock-badge">Out of Stock</span>
                                        <?php elseif ($p['current_stock'] <= 5): ?>
                                            <span class="badge bg-warning stock-badge">Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-success stock-badge">In Stock</span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="card-text">
                                        <strong>Price:</strong> RM <?= number_format($p['price'], 2) ?><br>
                                        <strong>Current Stock:</strong> <?= $p['current_stock'] ?>
                                    </p>
                                    <div class="mt-auto">
                                        <a href="editproducts.php?id=<?= $p['product_id'] ?>" class="btn btn-brown btn-sm me-2">Edit</a>
                                        <a href="products.php?delete_id=<?= $p['product_id'] ?>"
                                           onclick="return confirm('Delete <?= htmlspecialchars($p['product_name']) ?>?')"
                                           class="btn btn-tawny btn-sm">Delete</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show loading state on form submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = document.getElementById('addProductBtn');
            const spinner = btn.querySelector('.spinner-border');
            btn.disabled = true;
            spinner.classList.remove('d-none');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
        });

        // Show error message if exists
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error')) {
            alert('Error: ' + urlParams.get('error'));
        }
    </script>
</body>
</html>
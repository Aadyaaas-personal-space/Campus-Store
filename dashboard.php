<?php
require_once 'db_connect.php';
if (!isset($_SESSION['vendor'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - Campus Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/animate.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div id="preloader"><div class="spinner"></div></div>
    <div class="animate__animated animate__fadeIn">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="index.php">🎓 Campus Store</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="vendor.php">Vendors</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                🛒 Cart 
                                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                    <span class="badge bg-danger"><?php echo count($_SESSION['cart']); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php if (isset($_SESSION['customer'])): ?>
                            <li class="nav-item"><a class="nav-link" href="customer_dashboard.php">👤 Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="customer_logout.php">Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="customer_login.php">👤 Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link active" href="dashboard.php">📊 Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Dashboard -->
        <div class="container mt-5">
            <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['vendor']); ?>! 📊</h2>
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#products">📦 Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#orders">📋 Orders</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Products Tab -->
                <div class="tab-pane fade show active" id="products">
                    <div class="text-center mb-4">
                        <a href="#add-product" class="btn btn-success" data-bs-toggle="collapse">Add New Product</a>
                    </div>
                    <div class="collapse slide-in" id="add-product">
                        <form action="add_product.php" method="POST" enctype="multipart/form-data" class="w-50 mx-auto mb-5">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price ($)</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </form>
                    </div>
                    <h3 class="mb-4">Your Products</h3>
                    <div class="row">
                        <?php
                        $vendor = $_SESSION['vendor'];
                        $stmt = $conn->prepare("SELECT * FROM products p JOIN vendors v ON p.vendor_id = v.id WHERE v.username = ?");
                        $stmt->bind_param("s", $vendor);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $hasProducts = false;
                        while ($product = $result->fetch_assoc()) {
                            $hasProducts = true;
                            echo '
                            <div class="col-md-4 mb-4 slide-in">
                                <div class="card h-100 shadow-sm">
                                    <img src="images/' . htmlspecialchars($product['image']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>
                                        <p class="card-text">Price: $' . number_format($product['price'], 2) . '</p>
                                    </div>
                                </div>
                            </div>';
                        }
                        if (!$hasProducts) {
                            echo '<div class="alert alert-info text-center slide-in">No products added yet. Start adding products! 📦</div>';
                        }
                        $stmt->close();
                        ?>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div class="tab-pane fade" id="orders">
                    <h3 class="mb-4">Your Orders</h3>
                    <form method="GET" class="mb-4 slide-in">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by Order ID or Customer Name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                    <button type="submit" class="btn btn-primary">🔍 Search</button>
                                    <a href="dashboard.php#orders" class="btn btn-outline-secondary">Clear</a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    <option value="Pending" <?php echo isset($_GET['status']) && $_GET['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Processed" <?php echo isset($_GET['status']) && $_GET['status'] === 'Processed' ? 'selected' : ''; ?>>Processed</option>
                                    <option value="Delivered" <?php echo isset($_GET['status']) && $_GET['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="accordion" id="ordersAccordion">
                        <?php
                        $vendor = $_SESSION['vendor'];
                        $search_query = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
                        $selected_status = isset($_GET['status']) ? $_GET['status'] : '';
                        $query = "SELECT DISTINCT o.* FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id JOIN vendors v ON p.vendor_id = v.id WHERE v.username = ?";
                        $params = [$vendor];
                        if ($search_query) {
                            $query .= " AND (LOWER(o.order_id) LIKE ? OR LOWER(o.customer_name) LIKE ?)";
                            $params[] = "%$search_query%";
                            $params[] = "%$search_query%";
                        }
                        if ($selected_status) {
                            $query .= " AND o.status = ?";
                            $params[] = $selected_status;
                        }
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $hasOrders = false;
                        while ($order = $result->fetch_assoc()) {
                            $hasOrders = true;
                            $order_id = $order['id'];
                            $vendor_total = 0;
                            $items_stmt = $conn->prepare("SELECT p.name, oi.quantity, oi.price FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN vendors v ON p.vendor_id = v.id WHERE oi.order_id = ? AND v.username = ?");
                            $items_stmt->bind_param("is", $order_id, $vendor);
                            $items_stmt->execute();
                            $items_result = $items_stmt->get_result();
                            while ($item = $items_result->fetch_assoc()) {
                                $vendor_total += $item['price'] * $item['quantity'];
                            }
                            echo '
                            <div class="accordion-item slide-in">
                                <h2 class="accordion-header" id="orderHeading' . $order_id . '">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#orderCollapse' . $order_id . '">
                                        Order ID: ' . htmlspecialchars($order['order_id']) . ' | Status: ' . htmlspecialchars($order['status']) . ' | Total: $' . number_format($vendor_total, 2) . '
                                    </button>
                                </h2>
                                <div id="orderCollapse' . $order_id . '" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                                    <div class="accordion-body">
                                        <p><strong>Customer:</strong> ' . htmlspecialchars($order['customer_name']) . '</p>
                                        <p><strong>Email:</strong> ' . htmlspecialchars($order['customer_email']) . '</p>
                                        <p><strong>Address:</strong> ' . htmlspecialchars($order['customer_address']) . '</p>
                                        <p><strong>Order Date:</strong> ' . htmlspecialchars($order['order_date']) . '</p>
                                        <h5>Items</h5>
                                        <ul class="list-group mb-3">';
                            $items_stmt->execute();
                            $items_result = $items_stmt->get_result();
                            while ($item = $items_result->fetch_assoc()) {
                                echo '
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ' . htmlspecialchars($item['name']) . ' (x' . $item['quantity'] . ')
                                    <span>$' . number_format($item['price'] * $item['quantity'], 2) . '</span>
                                </li>';
                            }
                            echo '
                                        </ul>
                                        <form action="update_order_status.php" method="POST">
                                            <input type="hidden" name="order_id" value="' . $order_id . '">
                                            <div class="input-group">
                                                <select name="status" class="form-select">
                                                    <option value="Pending" ' . ($order['status'] === 'Pending' ? 'selected' : '') . '>Pending</option>
                                                    <option value="Processed" ' . ($order['status'] === 'Processed' ? 'selected' : '') . '>Processed</option>
                                                    <option value="Delivered" ' . ($order['status'] === 'Delivered' ? 'selected' : '') . '>Delivered</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary">Update Status</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>';
                            $items_stmt->close();
                        }
                        if (!$hasOrders) {
                            echo '<div class="alert alert-info text-center slide-in">No orders found matching your criteria. 📋</div>';
                        }
                        $stmt->close();
                        $conn->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-light text-center py-4 mt-5">
            <div class="container">
                <p>&copy; 2025 Campus Store - Multi-Vendor Platform 🚀</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
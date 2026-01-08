<?php
require_once 'db_connect.php';
if (!isset($_SESSION['customer'])) {
    header('Location: customer_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Campus Store</title>
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
                        <li class="nav-item"><a class="nav-link active" href="customer_dashboard.php">👤 Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="customer_logout.php">Logout</a></li>
                        <?php if (isset($_SESSION['vendor'])): ?>
                            <li class="nav-item"><a class="nav-link" href="dashboard.php">📊 Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="login.php">👨‍💼 Vendor Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Dashboard -->
        <div class="container mt-5">
            <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['customer']['name']); ?>! 👤</h2>
            <h3 class="mb-4">Your Orders</h3>
            <div class="accordion" id="ordersAccordion">
                <?php
                $customer_id = $_SESSION['customer']['id'];
                $stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
                $stmt->bind_param("i", $customer_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($order = $result->fetch_assoc()) {
                        $order_id = $order['id'];
                        $stmt_items = $conn->prepare("SELECT p.name, oi.quantity, oi.price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $stmt_items->bind_param("i", $order_id);
                        $stmt_items->execute();
                        $items_result = $stmt_items->get_result();
                        echo '
                        <div class="accordion-item slide-in">
                            <h2 class="accordion-header" id="orderHeading' . $order_id . '">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#orderCollapse' . $order_id . '">
                                    Order ID: ' . htmlspecialchars($order['order_id']) . ' | Status: ' . htmlspecialchars($order['status']) . ' | Total: $' . number_format($order['total'], 2) . '
                                </button>
                            </h2>
                            <div id="orderCollapse' . $order_id . '" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                                <div class="accordion-body">
                                    <p><strong>Order Date:</strong> ' . htmlspecialchars($order['order_date']) . '</p>
                                    <p><strong>Address:</strong> ' . htmlspecialchars($order['customer_address']) . '</p>
                                    <h5>Items</h5>
                                    <ul class="list-group mb-3">';
                        while ($item = $items_result->fetch_assoc()) {
                            echo '
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ' . htmlspecialchars($item['name']) . ' (x' . $item['quantity'] . ')
                                <span>$' . number_format($item['price'] * $item['quantity'], 2) . '</span>
                            </li>';
                        }
                        echo '
                                    </ul>
                                </div>
                            </div>
                        </div>';
                        $stmt_items->close();
                    }
                } else {
                    echo '<div class="alert alert-info text-center slide-in">No orders found. Start shopping now! 🛍️</div>';
                }
                $stmt->close();
                $conn->close();
                ?>
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
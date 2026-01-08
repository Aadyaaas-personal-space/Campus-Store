<?php
require_once 'db_connect.php';
if (!isset($_SESSION['customer']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: customer_login.php?redirect=checkout');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_SESSION['customer']['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $order_id = 'ORD-' . time();
    $total = 0;

    foreach ($_SESSION['cart'] as $item) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->bind_param("i", $item['index']);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $total += $product['price'] * $item['quantity'];
        $stmt->close();
    }

    $stmt = $conn->prepare("INSERT INTO orders (order_id, customer_id, customer_name, customer_email, customer_address, total, order_date, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'Pending')");
    $stmt->bind_param("sisssd", $order_id, $customer_id, $name, $email, $address, $total);
    $stmt->execute();
    $order_db_id = $conn->insert_id;

    foreach ($_SESSION['cart'] as $item) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->bind_param("i", $item['index']);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_db_id, $item['index'], $item['quantity'], $product['price']);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['cart'] = [];
    $_SESSION['order_id'] = $order_id;
    header('Location: checkout.php?success=1');
    exit;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Campus Store</title>
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

        <!-- Confirmation -->
        <div class="container mt-5">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success text-center slide-in">
                    Order placed successfully! Your order ID is <strong><?php echo htmlspecialchars($_SESSION['order_id']); ?></strong>.
                </div>
            <?php else: ?>
                <div class="alert alert-danger text-center slide-in">Error placing order. Please try again.</div>
            <?php endif; ?>
            <div class="text-center">
                <a href="customer_dashboard.php" class="btn btn-primary">View Order History</a>
                <a href="index.php" class="btn btn-outline-primary">Continue Shopping</a>
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
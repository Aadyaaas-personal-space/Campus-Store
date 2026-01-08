<?php
require_once 'db_connect.php';
if (!isset($_SESSION['customer']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: customer_login.php?redirect=checkout');
    exit;
}

$customer_id = $_SESSION['customer']['id'];
$stmt = $conn->prepare("SELECT name, email, address FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Campus Store</title>
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

        <!-- Checkout Form -->
        <div class="container mt-5">
            <h2 class="text-center mb-4">🛒 Checkout</h2>
            <div class="row">
                <div class="col-md-6 slide-in">
                    <h4>Billing Information</h4>
                    <form action="process_checkout.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="4" required><?php echo htmlspecialchars($customer['address']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Place Order 🚀</button>
                    </form>
                </div>
                <div class="col-md-6 slide-in">
                    <h4>Order Summary</h4>
                    <ul class="list-group mb-3">
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item) {
                            $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
                            $stmt->bind_param("i", $item['index']);
                            $stmt->execute();
                            $product = $stmt->get_result()->fetch_assoc();
                            $subtotal = $product['price'] * $item['quantity'];
                            $total += $subtotal;
                            echo '
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ' . htmlspecialchars($product['name']) . ' (x' . $item['quantity'] . ')
                                <span>$' . number_format($subtotal, 2) . '</span>
                            </li>';
                            $stmt->close();
                        }
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                            Total
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </li>
                    </ul>
                    <a href="cart.php" class="btn btn-outline-primary w-100">Edit Cart</a>
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
<?php $conn->close(); ?>
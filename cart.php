<?php
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Campus Store</title>
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
                            <a class="nav-link active" href="cart.php">
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

        <!-- Cart Contents -->
        <div class="container mt-5">
            <h2 class="text-center mb-4">🛒 Your Shopping Cart</h2>
            <?php
            if (empty($_SESSION['cart'])) {
                echo '<div class="alert alert-info text-center slide-in">Your cart is empty. Start shopping now! 🛍️</div>';
            } else {
                $total = 0;
                echo '<div class="row">';
                foreach ($_SESSION['cart'] as $index => $item) {
                    $product_id = $item['index'];
                    $stmt = $conn->prepare("SELECT p.name, p.price, p.image, v.username AS vendor FROM products p JOIN vendors v ON p.vendor_id = v.id WHERE p.id = ?");
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $product = $stmt->get_result()->fetch_assoc();
                    $subtotal = $product['price'] * $item['quantity'];
                    $total += $subtotal;
                    echo '
                    <div class="col-md-4 mb-4 slide-in">
                        <div class="card h-100 shadow-sm">
                            <img src="images/' . htmlspecialchars($product['image']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>
                                <p class="card-text text-muted">👨‍💼 ' . htmlspecialchars($product['vendor']) . '</p>
                                <p class="card-text">Price: $' . number_format($product['price'], 2) . '</p>
                                <form action="update_cart.php" method="POST" class="input-group mb-3">
                                    <input type="hidden" name="index" value="' . $index . '">
                                    <span class="input-group-text">Qty</span>
                                    <input type="number" name="quantity" class="form-control" value="' . $item['quantity'] . '" min="1" max="10" required>
                                    <button type="submit" class="btn btn-outline-primary">Update</button>
                                </form>
                                <p class="card-text">Subtotal: $' . number_format($subtotal, 2) . '</p>
                                <a href="remove_from_cart.php?index=' . $index . '" class="btn btn-danger w-100">Remove</a>
                            </div>
                        </div>
                    </div>';
                    $stmt->close();
                }
                echo '</div>';
                echo '<div class="text-center mt-4 slide-in">';
                echo '<h4>Total: $' . number_format($total, 2) . '</h4>';
                echo '<a href="clear_cart.php" class="btn btn-warning me-2">Clear Cart</a>';
                if (isset($_SESSION['customer'])) {
                    echo '<a href="checkout.php" class="btn btn-success me-2">Proceed to Checkout</a>';
                } else {
                    echo '<a href="customer_login.php?redirect=checkout" class="btn btn-success me-2">Login to Checkout</a>';
                }
                echo '<a href="index.php" class="btn btn-primary">Continue Shopping</a>';
                echo '</div>';
            }
            $conn->close();
            ?>
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
<?php
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Store - Multi-Vendor</title>
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
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="vendor.php">Vendors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>" href="cart.php">
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

        <!-- Hero Section -->
        <div class="container mt-4">
            <div class="text-center bg-primary text-white p-5 rounded animate__animated animate__fadeInDown">
                <h1>🎓 Welcome to Campus Store</h1>
                <p class="lead">Shop from multiple vendors on your campus! 📚🍕💻</p>
                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <div class="alert alert-success mt-3 slide-in">
                        🛒 You have <strong><?php echo count($_SESSION['cart']); ?></strong> items in your cart!
                        <a href="cart.php" class="btn btn-light btn-sm ms-2">View Cart</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Monster Section -->
        <div class="container mt-5 monster-section">
            <h2 class="text-center mb-4">Meet Our Campus Monsters! 🦁</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <img src="images/monster1.png" class="monster-img" alt="Monster 1">
                    <p class="text-center mt-2">Study Monster 📚</p>
                </div>
                <div class="col-md-4 mb-4">
                    <img src="images/monster2.png" class="monster-img" alt="Monster 2">
                    <p class="text-center mt-2">Snack Monster 🍕</p>
                </div>
                <div class="col-md-4 mb-4">
                    <img src="images/monster3.png" class="monster-img" alt="Monster 3">
                    <p class="text-center mt-2">Tech Monster 💻</p>
                </div>
            </div>
        </div>

        <!-- Product Listing -->
        <div class="container mt-5">
            <h2 class="text-center mb-4">🛍️ Our Products</h2>
            <div class="row" id="product-list">
                <?php
                $stmt = $conn->prepare("SELECT p.id, p.name, p.price, p.image, v.username AS vendor FROM products p JOIN vendors v ON p.vendor_id = v.id");
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($product = $result->fetch_assoc()) {
                        echo '
                        <div class="col-md-4 mb-4 slide-in">
                            <div class="card h-100 shadow-sm">
                                <img src="images/' . htmlspecialchars($product['image']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '" style="height: 200px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>
                                    <p class="card-text text-muted">👨‍💼 ' . htmlspecialchars($product['vendor']) . '</p>
                                    <p class="card-text fs-3 fw-bold text-primary">$<span id="price-' . $product['id'] . '">' . number_format($product['price'], 2) . '</span></p>
                                    <form action="add_to_cart.php" method="POST" class="mt-auto">
                                        <input type="hidden" name="index" value="' . $product['id'] . '">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">Qty</span>
                                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="10" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">🛒 Add to Cart</button>
                                    </form>
                                    <small class="text-success mt-2">Fast Delivery on Campus! 🚀</small>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<div class="col-12"><div class="alert alert-warning text-center slide-in">No products available yet. Vendors are adding products soon! ⏳</div></div>';
                }
                $stmt->close();
                $conn->close();
                ?>
            </div>
            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <div class="text-center mt-5">
                <a href="cart.php" class="btn btn-success btn-lg slide-in">🛒 View Cart (<?php echo count($_SESSION['cart']); ?> items)</a>
            </div>
            <?php endif; ?>
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
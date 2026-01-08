<?php
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendors - Campus Store</title>
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
                        <li class="nav-item"><a class="nav-link active" href="vendor.php">Vendors</a></li>
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

        <!-- Vendor Listing -->
        <div class="container mt-5">
            <h2 class="text-center mb-4">Our Vendors 👨‍💼</h2>
            <div class="row">
                <?php
                $stmt = $conn->prepare("SELECT v.username, COUNT(p.id) as product_count FROM vendors v LEFT JOIN products p ON v.id = p.vendor_id GROUP BY v.id");
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($vendor = $result->fetch_assoc()) {
                        echo '
                        <div class="col-md-4 mb-4 slide-in">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <h5 class="card-title">' . htmlspecialchars($vendor['username']) . '</h5>
                                    <p class="card-text">Products: ' . $vendor['product_count'] . '</p>
                                    <a href="vendor.php?vendor=' . urlencode($vendor['username']) . '" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<div class="col-12"><div class="alert alert-info text-center slide-in">No vendors available yet! ⏳</div></div>';
                }
                ?>
            </div>

            <!-- Vendor Products -->
            <?php
            if (isset($_GET['vendor'])) {
                $vendor_name = $conn->real_escape_string($_GET['vendor']);
                $stmt = $conn->prepare("SELECT p.id, p.name, p.price, p.image FROM products p JOIN vendors v ON p.vendor_id = v.id WHERE v.username = ?");
                $stmt->bind_param("s", $vendor_name);
                $stmt->execute();
                $result = $stmt->get_result();
                echo '<h3 class="mt-5 text-center">Products by ' . htmlspecialchars($vendor_name) . '</h3>';
                echo '<div class="row">';
                if ($result->num_rows > 0) {
                    while ($product = $result->fetch_assoc()) {
                        echo '
                        <div class="col-md-4 mb-4 slide-in">
                            <div class="card h-100 shadow-sm">
                                <img src="images/' . htmlspecialchars($product['image']) . '" class="card-img-top" alt="' . htmlspecialchars($product['name']) . '" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>
                                    <p class="card-text fs-5 text-primary">$' . number_format($product['price'], 2) . '</p>
                                    <form action="add_to_cart.php" method="POST">
                                        <input type="hidden" name="index" value="' . $product['id'] . '">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">Qty</span>
                                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="10" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">🛒 Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<div class="col-12"><div class="alert alert-warning text-center slide-in">No products found for this vendor! 📦</div></div>';
                }
                $stmt->close();
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
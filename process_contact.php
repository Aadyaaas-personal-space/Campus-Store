<?php
require_once 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process contact form (e.g., save to database or send email)
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);
    // For simplicity, display success message (extend with email integration later)
    $success = "Thank you, $name! Your message has been received.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Confirmation - Campus Store</title>
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
                        <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
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

        <!-- Confirmation -->
        <div class="container mt-5">
            <div class="alert alert-success text-center slide-in">
                <?php echo isset($success) ? $success : 'Error processing your message.'; ?>
            </div>
            <div class="text-center">
                <a href="index.php" class="btn btn-primary">Back to Home</a>
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
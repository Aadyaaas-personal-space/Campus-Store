<?php
require_once 'db_connect.php';
if (!isset($_SESSION['vendor'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $vendor = $_SESSION['vendor'];

    $stmt = $conn->prepare("SELECT v.id FROM vendors v WHERE v.username = ?");
    $stmt->bind_param("s", $vendor);
    $stmt->execute();
    $vendor_id = $stmt->get_result()->fetch_assoc()['id'];
    $stmt->close();

    $stmt = $conn->prepare("UPDATE orders o SET status = ? WHERE o.id = ? AND EXISTS (SELECT 1 FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = o.id AND p.vendor_id = ?)");
    $stmt->bind_param("sii", $status, $order_id, $vendor_id);
    $stmt->execute();
    $stmt->close();
    header('Location: dashboard.php#orders');
    exit;
}
$conn->close();
?>
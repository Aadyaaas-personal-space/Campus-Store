<?php
require_once 'db_connect.php';
$_SESSION['cart'] = [];
header('Location: cart.php');
exit;
?>
<?php
require_once 'db_connect.php';
unset($_SESSION['customer']);
header('Location: index.php');
exit;
?>
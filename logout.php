<?php
require_once 'db_connect.php';
unset($_SESSION['vendor']);
header('Location: index.php');
exit;
?>
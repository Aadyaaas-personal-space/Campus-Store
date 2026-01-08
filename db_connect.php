<?php
session_start(); // Start session for all files
$host = 'localhost';
$user = 'root';
$password = ''; // Update with your MySQL password
$dbname = 'campus_store';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4'); // Ensure UTF-8 encoding
?>
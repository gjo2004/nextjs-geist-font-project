<?php
// Database connection settings
$host = 'localhost';
$port = 3307;
$dbname = 'your_database_name'; // Replace with your actual database name
$username = 'your_username';   // Replace with your DB username
$password = 'your_password';   // Replace with your DB password

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

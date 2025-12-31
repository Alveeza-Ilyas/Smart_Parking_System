<?php
session_start();

$host = 'localhost';
$db   = 'smart_parking';
$user = 'root';       // Change if different
$pass = '';           // Your MySQL password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if(!isLoggedIn()){
        header('Location: index.php');
        exit;
    }
}
?>

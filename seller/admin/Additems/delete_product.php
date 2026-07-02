<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$host = 'localhost';
$db   = 'allnepalspices';
$user = 'postgres';
$pass = 'root';
$port = '5432';


$conn = pg_connect("host=$host port=$port dbname=$db user=$user password=$pass");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
}

// delete_product.php

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);


?>
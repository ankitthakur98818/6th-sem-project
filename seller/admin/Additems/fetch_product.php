<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Database connection using pg_connect (same as add_product.php)
$conn = pg_connect("host=localhost port=5432 dbname=allnepalspices user=postgres password=root");

if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    if (isset($_GET['id'])) {
        // Fetch single product
        $result = pg_query_params($conn, "SELECT * FROM products WHERE id = $1", [$_GET['id']]);
    } else {
        // Fetch all products
        $result = pg_query($conn, "SELECT * FROM products ORDER BY id DESC");
    }

    if (!$result) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Query failed']);
        exit;
    }

    $products = [];
    while ($row = pg_fetch_assoc($result)) {
        $products[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($products);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}

pg_close($conn);
?>

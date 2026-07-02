<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please login.");
}

// Always check request type
// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     die('Invalid request method');
// }

$conn = pg_connect("host=localhost port=5432 dbname=allnepalspices user=postgres password=root");
if (!$conn) {
    die("Database connection failed");
}

echo" database connected successfully ";

/* GET FORM DATA SAFELY */
$name  = trim($_POST['productName'] ?? '');
$price = trim($_POST['productPrice'] ?? '');

$old_price = ($_POST['productOldPrice'] ?? '') !== ''
    ? $_POST['productOldPrice']
    : null;

$category = trim($_POST['productCategory'] ?? '');
$on_sale  = isset($_POST['onSale']) ? 't' : 'f';
$in_stock = isset($_POST['Instock']) ? 't' : 'f';

/* VALIDATION */
if ($name === '' || $price === '' || $category === '') {
    die("Required fields missing");
}

/* IMAGE VALIDATION */
if (
    !isset($_FILES['productImage']) ||
    $_FILES['productImage']['error'] !== UPLOAD_ERR_OK
) {
    die("Image upload failed");
}

$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$imageName = time() . "_" . basename($_FILES['productImage']['name']);
$imagePath = "uploads/" . $imageName;

move_uploaded_file(
    $_FILES['productImage']['tmp_name'],
    __DIR__ . "/" . $imagePath
);

/* INSERT DATA */
$query = "
INSERT INTO products
(name, price, old_price, category, image_path, on_sale, in_stock)
VALUES ($1, $2, $3, $4, $5, $6, $7)
";

$result = pg_query_params($conn, $query, [
    $name,
    $price,
    $old_price,
    $category,
    $imagePath,
    $on_sale,
    $in_stock
]);

if (!$result) {
    die("Database insert failed: " . pg_last_error($conn));
}

/* SUCCESS */
header("Location: adminadd.html");
exit;
?>
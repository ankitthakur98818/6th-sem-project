<?php
require_once "../database/dbpgadmin.php";

$stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
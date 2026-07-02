<?php
require_once "../database/dbpgadmin.php";

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");

$stmt->execute([
    ':status' => $data['status'],
    ':id' => $data['id']
]);

echo json_encode(["success" => true]);
?>
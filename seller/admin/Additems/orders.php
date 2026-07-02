<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

require "../backend/dbpgadmin.php";
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Orders</h2>
<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php foreach ($orders as $order): ?>
<tr>
    <td><?= $order['id'] ?></td>
    <td><?= $order['first_name']." ".$order['last_name'] ?></td>
    <td><?= $order['email'] ?></td>
    <td>NPR <?= $order['total'] ?></td>
    <td><?= $order['status'] ?></td>
    <td><?= $order['created_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>

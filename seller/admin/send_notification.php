<?php
header("Content-Type: application/json");
session_start();

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Please login as admin']);
    exit;
}

require_once '../../../TheSpiceNepal/database/dbpgadmin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$message = trim($_POST['message'] ?? '');
$type = $_POST['type'] ?? 'info';
$target = $_POST['target'] ?? 'all';
$emails = trim($_POST['emails'] ?? '');

if (empty($title) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Title and message required']);
    exit;
}

if (!in_array($type, ['info', 'success', 'warning', 'error'])) {
    $type = 'info';
}

$userEmails = [];
if ($target === 'all') {
    try {
        $stmt = $conn->query("SELECT email FROM users WHERE role != 'admin' OR role IS NULL");  // Assume users table
        $userEmails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching users: ' . $e->getMessage()]);
        exit;
    }
} else {
    $userEmails = array_filter(array_map('trim', explode(',', $emails)));
    $userEmails = array_filter($userEmails, function($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    });
}

if (empty($userEmails)) {
    echo json_encode(['success' => false, 'message' => 'No valid users found']);
    exit;
}

$sentCount = 0;
try {
    $stmt = $conn->prepare("
        INSERT INTO notifications (title, message, type, user_email, created_at, is_read) 
        VALUES (:title, :message, :type, :user_email, NOW(), 0)
    ");

    foreach ($userEmails as $email) {
        $stmt->execute([
            ':title' => $title,
            ':message' => $message,
            ':type' => $type,
            ':user_email' => $email
        ]);
        $sentCount++;
    }

    echo json_encode([
        'success' => true,
        'message' => "Successfully sent notification to {$sentCount} user(s)"
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>


<?php
session_start();

// Set header to return JSON
header('Content-Type: application/json');

// This should point to your database connection script.
// Assuming users.php is in /seller/admin/Additems/
// and dbpgadmin.php is in /TheSpiceNepal/database/
include_once '../../../TheSpiceNepal/database/dbpgadmin.php';

/**
 * Sends a JSON error response and terminates the script.
 * @param string $message The error message.
 */
function send_error($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Check if the database connection is established.
if (!isset($conn) || !$conn) {
    send_error('Database connection could not be established.');
}

try {
    // Prepare and execute the query to fetch all registered users.
    // Order by ID to show newest users first.
    $stmt = $conn->prepare("SELECT id, fullname, email, phone, address, gender, created_at, avatar FROM register ORDER BY id DESC");
    $stmt->execute();

    // Fetch all users.
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Send a success response with the user data.
    echo json_encode(['success' => true, 'users' => $users]);

} catch (PDOException $e) {
    // In case of a database error, send a generic error message for security.
    // You can log the detailed error ($e->getMessage()) for debugging.
    send_error('An error occurred while fetching user data.');
}
?>
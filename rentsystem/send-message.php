<?php
session_start();
require_once 'config.php';

//Caretaker sends messages to tenants.
if (!isset($_SESSION['caretaker_id'])) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $caretaker_id = $_SESSION['caretaker_id'];

    $stmt = $conn->prepare("INSERT INTO messages (caretaker_id, content, timestamp) VALUES (?, ?, NOW())");
    $stmt->bind_param('is', $caretaker_id, $message);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        http_response_code(500);
        echo 'Failed to send message';
    }
}
?>

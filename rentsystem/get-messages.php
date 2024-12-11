<?php
session_start();
require_once 'config.php';

// Fetch caretaker messages with reactions and view timestamps.
// Check if the user is logged in and is a caretaker
if (!isset($_SESSION['caretaker_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$caretaker_id = $_SESSION['caretaker_id'];

$query = "
    SELECT m.id, m.content, m.timestamp,
        COALESCE(JSON_ARRAYAGG(
            JSON_OBJECT(
                'tenant_name', u.full_name,
                'emoji', r.emoji,
                'timestamp', r.timestamp
            )
        ), '[]') AS reactions
    FROM messages m
    LEFT JOIN reactions r ON m.id = r.message_id
    LEFT JOIN users u ON r.tenant_id = u.id
    WHERE m.caretaker_id = ?
    GROUP BY m.id
";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $caretaker_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'content' => $row['content'],
        'timestamp' => $row['timestamp'],
        'reactions' => json_decode($row['reactions'], true),
    ];
}

header('Content-Type: application/json');
echo json_encode($messages);
?>

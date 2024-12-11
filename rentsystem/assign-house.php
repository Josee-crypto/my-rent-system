<?php
include('config.php'); // Include the database configuration

// Enable debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type for JSON
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // Only allow POST requests
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
        exit;
    }

    // Retrieve and sanitize POST data
    $tenantId = $_POST['tenantId'] ?? null;
    $houseNumber = $_POST['houseNumber'] ?? null;

    if (!$tenantId || !$houseNumber) {
        echo json_encode(['status' => 'error', 'message' => 'Both tenant ID and house number are required.']);
        exit;
    }

    // Update the database
    $stmt = $pdo->prepare("UPDATE users SET house_number = :house_number WHERE user_id = :tenant_id AND user_type = 'tenant'");
    $result = $stmt->execute([
        ':house_number' => $houseNumber,
        ':tenant_id' => $tenantId
    ]);

    if ($result && $stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'House number assigned successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to assign house number. Tenant not found or update failed.']);
    }
} catch (PDOException $e) {
    // Log database errors
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Log unexpected errors
    error_log("Unexpected Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
}
?>

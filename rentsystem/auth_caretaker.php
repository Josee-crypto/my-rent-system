<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'caretaker_login') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Validate caretaker login
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND user_type = 'caretaker'");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['caretaker_name'] = $user['full_name'];
                $_SESSION['caretaker_phone'] = $user['phone_number'];
                echo 'success';
            } else {
                echo 'Invalid login credentials';
            }
        } elseif ($action === 'fetch_tenants') {
            try {
                // Fetch tenants from the database
                $stmt = $pdo->prepare("SELECT full_name, phone_number, house_number FROM users WHERE user_type = 'tenant'");
                $stmt->execute();
                $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Send tenants as JSON response
                echo json_encode($tenants);
                exit;
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                exit;
            }
        } else {
            echo json_encode(['error' => 'Invalid action']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode([
        'error' => 'Invalid request method',
        'method_received' => $_SERVER['REQUEST_METHOD'], // Debugging information
        'expected_method' => 'POST'
    ]);
}

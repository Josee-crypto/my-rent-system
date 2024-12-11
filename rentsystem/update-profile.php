<?php
session_start();
include('config.php');

// Check if the user is logged in and is a tenant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'tenant') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Update profile query
    $updateQuery = "UPDATE users SET full_name = :fullName, email = :email, phone_number = :phone WHERE user_id = :userId";
    
    // Add password update if provided
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE users SET full_name = :fullName, email = :email, phone_number = :phone, password_hash = :password WHERE user_id = :userId";
    }

    $stmt = $pdo->prepare($updateQuery);
    $params = [
        ':fullName' => $fullName,
        ':email' => $email,
        ':phone' => $phone,
        ':userId' => $userId
    ];

    if (!empty($password)) {
        $params[':password'] = $password;
    }

    if ($stmt->execute($params)) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>

<?php
session_start();
include('config.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin Login
    if ($_POST['action'] === 'admin_login') {
        $email = $_POST['username']; // Change from 'username' to 'email'
        $password = $_POST['password'];
    
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND user_type = 'admin'");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
    
            if ($user) {
                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_type'] = $user['user_type'];
                    echo 'success';
                } else {
                    echo 'Invalid password';
                }
            } else {
                echo 'Admin not found';
            }
        } catch (PDOException $e) {
            echo 'Database error: ' . $e->getMessage();
        }
    }
    
   
    
    // Tenant Login
    elseif ($_POST['action'] === 'tenant_login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare query to find tenant user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND user_type = 'tenant'");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // Verify password
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_type'] = $user['user_type'];
            echo 'success';  // Return success response
        } else {
            echo 'error';  // Return error response for invalid credentials
        }
    } 

    //caretaker Login
 
    
    // Tenant Registration
    elseif ($_POST['action'] === 'register_tenant') {
        if (isset($_POST['fullName'], $_POST['email'], $_POST['phone'], $_POST['password'], $_POST['confirmPassword'])) {
            $fullName = $_POST['fullName'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];

            // Validate password match
            if ($password !== $confirmPassword) {
                echo 'Passwords do not match';
                exit;
            }

            // Check if email already exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->rowCount() > 0) {
                echo 'Email already registered';
                exit;
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert the tenant into the database
            $stmt = $pdo->prepare("
                INSERT INTO users (full_name, email, phone_number, password_hash, user_type) 
                VALUES (:fullName, :email, :phone, :password, 'tenant')
            ");
            $result = $stmt->execute([
                ':fullName' => $fullName,
                ':email' => $email,
                ':phone' => $phone,
                ':password' => $hashedPassword
            ]);

            // Check if the insert was successful
            if ($result) {
                echo 'success';  // Return success response
            } else {
                echo 'error';  // Return error response
            }
        } else {
            echo 'All fields are required';
        }
    }
}
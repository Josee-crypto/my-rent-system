<?php
try {
    // Replace 'localhost' with '127.0.0.1' if necessary for WAMP
    $pdo = new PDO('mysql:host=localhost;dbname=jeff_rentals', 'root', ''); // Default user is 'root' and default password is empty on WAMP
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

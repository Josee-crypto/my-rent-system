<?php
include('config.php');
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_type = 'tenant'");
$stmt->execute();
$tenants = $stmt->fetchAll();
echo json_encode($tenants);
?>
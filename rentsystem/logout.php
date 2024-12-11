<?php
// Start the session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to the login or home page
header("Location: index.html");
exit;
?>

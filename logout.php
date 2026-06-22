<?php
// logout.php

// Enable debug mode for error tracking
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if not already initialized
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear session data from server memory
$_SESSION = array();

// Invalidate session cookie in client browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Completely destroy active session instance
session_destroy();

// Redirect browser to dashboard home page
header("Location: index.php");
exit;
?>

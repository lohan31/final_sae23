<?php
// logout.php

// 1. Enable debug mode to catch any unexpected behavior
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Initialize the session to access session variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Clear all session variables in memory
$_SESSION = array();

// 4. Destroy the session cookie in the user's browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 5. Permanently destroy the session on the server side
session_destroy();

// 6. Redirect the user back to the home page immediately
header("Location: index.php");
exit;
?>

<?php
/*
    engine/logout.php    VERSION 1.3
    Initiates the logout process.
    Reviewed 7/12/2023
*/

session_start();
// Unset all of the session variables
$_SESSION = array();

// This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();
header("Location: https://project.lucande.io/index.php");
exit();
?>

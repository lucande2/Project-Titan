<?php
include_once '../engine/dbConnect.php';
session_start();

// Checking if user is logged in and has an admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../content/login.php');
    exit;
}

// If the 'id' variable is set in the URL, we will delete the user
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Create a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");

    // Bind the id to our prepared statement
    $stmt->bind_param("i", $id);

    // Execute the prepared statement
    $stmt->execute();

    // Close the prepared statement
    $stmt->close();

    // Redirect back to the manage users page
    header("Location: ../admin/manage_users.php");
    exit;
}

// Close the database connection
$conn->close();
?>

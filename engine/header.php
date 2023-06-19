<!DOCTYPE html>
<html>
<head>
    <title>Titan</title>
    <link rel="stylesheet" href="https://project.lucande.io/css/style_v7.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<?php
include_once dirname(__FILE__) . '/dbConnect.php';
session_start();
?>

<div class="top-bar">
    <div class="top-bar-container">
        <a href="../index.php"><img src="https://images.lucande.io/galleries//system/logo.png" alt="Logo"></a>
        <div class="links">
            <?php
            if (isset($_SESSION['username'])) {
                echo '<div class="user-info">';
                echo '<p>Welcome, ' . htmlspecialchars($_SESSION['username']) . '</p>';
                echo '<a class="button-link" href="../engine/logout.php">Logout</a>';
                echo '</div>';
            } else {
                echo '<a class="button-link" href="../content/register.php">Register</a>';
                echo '<a class="button-link" href="../content/login.php">Login</a>';
            }
            ?>
        </div>
    </div>
</div>
<div class="container main-content">

    <div class="side-bar">
        <!-- sidebar content goes here -->
        <?php
        if (!isset($_SESSION['username'])) {
            echo '<p>You are not logged in, please <a href="../content/login.php">login</a> or <a href="../content/register.php">register a new account</a>.</p>';
        } else {
            echo '<p>Welcome, ' . htmlspecialchars($_SESSION['username']) . '</p>';
            echo '<ul class="sidebar-menu">';

            // Query database for user role
            $username = $_SESSION['username'];
            $sql = "SELECT role FROM users WHERE username = '$username'";
            $result = mysqli_query($conn, $sql);
            $user = mysqli_fetch_assoc($result);

            if ($user['role'] === 'admin' && strpos($_SERVER['REQUEST_URI'], 'admin/') !== false) {
                // Admin menu
                echo '<li><a href="../admin/portal.php">Admin Portal</a></li>';
                echo '<li><a href="../admin/manage_users.php">Manage Users</a></li>';
                echo '<li><a href="../admin/manage_foods.php">Manage Food</a></li>';
                echo '<li><a href="../admin/manage_meals.php">Manage Meals</a></li>';
                echo '<li><a href="../admin/settings.php">Site Settings</a></li>';
            } else {
                // Regular user menu
                echo '<li><a href="../content/view_profile.php">View Profile</a></li>';
                echo '<li><a href="../content/add_meal.php">Add a Meal</a></li>';
                echo '<li><a href="../content/foods/add_food.php">Add a Food</a></li>';
                if ($user['role'] === 'admin') {
                    echo '<li><a href="../admin/portal.php">Admin</a></li>';
                }
            }
            echo '</ul>';
        }
        ?>
    </div>

    <div class="center-content">
        <!-- main content goes here -->

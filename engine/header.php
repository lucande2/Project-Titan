<?php
/*
    engine/header.php    VERSION 1.3
    Loads the necessary page requisites, styling, and establishes the header, sidebar, and centre content.
    Reviewed 7/12/2023
*/

// Define path
include_once dirname(__FILE__) . '/dbConnect.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize userId
$userId = null;

if (isset($_SESSION['username'])) {
    // Query database for user role
    $username = $_SESSION['username'];
    $sql = "SELECT role, id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Store user id in a variable
    $userId = $_SESSION["user-id"];

    // Retrieve site_theme for the logged-in user
    $themeSql = "SELECT site_theme FROM users WHERE id = ?";
    $themeStmt = $conn->prepare($themeSql);
    $themeStmt->bind_param('i', $userId);
    $themeStmt->execute();
    $themeResult = $themeStmt->get_result();
    $userTheme = $themeResult->fetch_assoc()['site_theme'];

}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Titan</title>
    <link rel="stylesheet" href="../css/style.css?v=1.46">
    <link rel="stylesheet" href="../../css/style.css?v=1.46">

    <?php if (isset($userTheme)): ?>
        <link rel="stylesheet" href="../css/<?= htmlspecialchars($userTheme) ?>.css?v=1.50">
        <link rel="stylesheet" href="../../css/<?= htmlspecialchars($userTheme) ?>.css?v=1.50">
    <?php endif; ?>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>


<body>

<div class="top-bar">
    <div class="top-bar-container">

        <a href="https://project.lucande.io/index.php"><img src="https://images.lucande.io/galleries//system/logo.png" alt="Logo"></a>

        <span class="sub-logo">TITAN</span>
        <div class="links">
            <?php if (isset($_SESSION['username'])): ?>
                <div class="user-info">
                    <div class="dropdown">
                        <button class="dropbtn"><i class="fa fa-user"></i>  <?php echo htmlspecialchars($_SESSION['username']); ?> <i class="fa fa-caret-down"></i></button>
                        <div class="dropdown-content">
                            <a href="https://project.lucande.io/content/profile.php?id=<?php echo $userId; ?>">View Profile</a>
                            <a href="https://project.lucande.io/content/account_settings.php?id=<?php echo $userId; ?>">Account Settings</a>
                            <a href="https://project.lucande.io/engine/logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a class="button-link" href="https://project.lucande.io/content/register.php">Register</a>
                <a class="button-link" href="https://project.lucande.io/content/login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container main-content">

    <div class="side-bar">
        <!-- sidebar content goes here -->
        <?php
        if (!isset($_SESSION['username'])) {
            echo '<p>You are not logged in, please <a href="https://project.lucande.io/content/login.php">login</a> or <a href="https://project.lucande.io/content/register.php">register a new account</a>.</p>';
        } else {
            echo '<h2>Menu</h2>';
            echo '<ul class="sidebar-menu">';

            if ($user['role'] === 'admin' && strpos($_SERVER['REQUEST_URI'], 'admin/') !== false) {
                // Admin menu
                echo '<li><a href="../admin/portal.php">Admin Portal</a></li>';
                echo '<li><a href="../admin/manage_users.php">Manage Users</a></li>';
                echo '<li><a href="../admin/manage_foods.php">Manage Food</a></li>';
                echo '<li><a href="../admin/manage_meals.php">Manage Meals</a></li>';
                echo '<li><a href="../admin/settings.php">Site Settings</a></li>';
                echo '<br>';

                echo '<li><a href="https://project.lucande.io/">Site Home</a></li>';
            } else {
                // Regular user menu
                echo '<li><a href="https://project.lucande.io/content/profile.php?id='.$userId.'">View Profile</a></li>';
                echo '<li><a href="https://project.lucande.io/content/foods/search_food.php">Search Foods</a></li>';
                echo '<li><a href="https://project.lucande.io/content/foods/add_food.php">Add a Food</a></li>';
                echo '<li><a href="https://project.lucande.io/content/meals/add_meal.php">Add a Meal</a></li>';
                echo '<li><a href="https://project.lucande.io/content/analysis/portal.php">Analysis Centre</a></li>';
                if ($user['role'] === 'admin') {
                    echo '<li><a href="https://project.lucande.io/admin/portal.php">Admin Portal</a></li>';
                }
            }
            echo '</ul>';
        }
        ?>
    </div>

    <div class="center-content">
        <!-- main content goes here -->

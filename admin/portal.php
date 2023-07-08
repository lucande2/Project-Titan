<?php
/*
    admin/portal.php    VERSION 1.3
    Home page for the administrative centre.  Updates the header side-menu to have administrative portal links.
    Reviewed 7/8/2023
*/

// Include header
include_once '../engine/header.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo '<p>You are not logged in, please <a href="../content/login.php">login</a> or <a href="../content/register.php">register a new account</a>.</p>';
    exit;
}

// Define variables
$username = $_SESSION['username'];
$sql = "SELECT role FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Check if user is administrator
if ($user['role'] !== 'admin') {
    echo '<p>You do not have permission to view this page.</p>';
    exit;
}

// Table information functions start
// Count total users
$sql = "SELECT COUNT(*) AS total FROM users";
$result = mysqli_query($conn, $sql);
$user_count = mysqli_fetch_assoc($result)['total'];

// Count users created in the past week
$sql = "SELECT COUNT(*) AS total FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
$result = mysqli_query($conn, $sql);
$weekly_user_count = mysqli_fetch_assoc($result)['total'];

// Count users created in the past 24 hours
$sql = "SELECT COUNT(*) AS total FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$result = mysqli_query($conn, $sql);
$daily_user_count = mysqli_fetch_assoc($result)['total'];

// Count total food
$sql = "SELECT COUNT(*) AS total FROM food";
$result = mysqli_query($conn, $sql);
$food_count = mysqli_fetch_assoc($result)['total'];

// Count food created in the past week
$sql = "SELECT COUNT(*) AS total FROM food WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
$result = mysqli_query($conn, $sql);
$weekly_food_count = mysqli_fetch_assoc($result)['total'];

// Count food created in the past 24 hours
$sql = "SELECT COUNT(*) AS total FROM food WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$result = mysqli_query($conn, $sql);
$daily_food_count = mysqli_fetch_assoc($result)['total'];

// Fetch total meals
$sql = "SELECT COUNT(*) AS total FROM meals";
$result = mysqli_query($conn, $sql);
$total_meal_count = mysqli_fetch_assoc($result)['total'];

// Fetch meals for the past week
$sql = "SELECT COUNT(*) AS total FROM meals WHERE meal_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
$result = mysqli_query($conn, $sql);
$weekly_meal_count = mysqli_fetch_assoc($result)['total'];

// Fetch meals for the past 24 hours
$sql = "SELECT COUNT(*) AS total FROM meals WHERE meal_date >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$result = mysqli_query($conn, $sql);
$daily_meal_count = mysqli_fetch_assoc($result)['total'];
// End table information fetching functions
?>

<!-- Page starts -->
<h1>Welcome to the Admin Portal</h1>
<h2>User Statistics</h2>
<p>Total users and those created in the past week or day.</p>
<table class="table-custom">
    <tr>
        <th>Total</th>
        <th>Past Week</th>
        <th>Past Day</th>
    </tr>
    <tr>
        <td><?php echo $user_count; ?></td>
        <td><?php echo $weekly_user_count; ?></td>
        <td><?php echo $daily_user_count; ?></td>
    </tr>
</table>
<br>
<h2>Food Statistics</h2>
<p>Food entry totals and those created in the past week or day.</p>
<table class="table-custom">
    <tr>
        <th>Total</th>
        <th>Past Week</th>
        <th>Past Day</th>
    </tr>
    <tr>
        <td><?php echo $food_count; ?></td>
        <td><?php echo $weekly_food_count; ?></td>
        <td><?php echo $daily_food_count; ?></td>
    </tr>
</table>
<br>
<h2>Meal Statistics</h2>
<p>Meal entry totals and those created in the past week or day.</p>
<table class="table-custom">
    <tr>
        <th>Total</th>
        <th>Past Week</th>
        <th>Past Day</th>
    </tr>
    <tr>
        <td><?php echo $total_meal_count; ?></td>
        <td><?php echo $weekly_meal_count; ?></td>
        <td><?php echo $daily_meal_count; ?></td>
    </tr>
</table>

<?php
include_once '../engine/footer.php';
?>

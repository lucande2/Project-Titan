<?php
include_once '../engine/header.php';

// Check if user is admin
if (!isset($_SESSION['username'])) {
    echo '<p>You are not logged in, please <a href="../content/login.php">login</a> or <a href="../content/register.php">register a new account</a>.</p>';
    exit;
}

$username = $_SESSION['username'];
$sql = "SELECT role FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($user['role'] !== 'admin') {
    echo '<p>You do not have permission to view this page.</p>';
    exit;
}

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

?>

<h1>Welcome to the Admin Portal</h1>
<h2>User Statistics</h2>
<table class="table-custom">
    <tr>
        <th>Total Users</th>
        <th>Users Created in Past Week</th>
        <th>Users Created in Past 24 Hours</th>
    </tr>
    <tr>
        <td><?php echo $user_count; ?></td>
        <td><?php echo $weekly_user_count; ?></td>
        <td><?php echo $daily_user_count; ?></td>
    </tr>
</table>

<h2>Food Statistics</h2>
<table class="table-custom">
    <tr>
        <th>Total Food</th>
        <th>Food Created in Past Week</th>
        <th>Food Created in Past 24 Hours</th>
    </tr>
    <tr>
        <td><?php echo $food_count; ?></td>
        <td><?php echo $weekly_food_count; ?></td>
        <td><?php echo $daily_food_count; ?></td>
    </tr>
</table>

<?php
include_once '../engine/footer.php';
?>

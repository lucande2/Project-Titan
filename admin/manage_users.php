<?php
/*
    admin/manage_users.php    VERSION 1.3
    Accessed from the administrative portal.  Allows an administrator the ability to view, manage, and delete users.
    Reviewed 7/8/2023
*/

// Include header and database connection variables
include_once '../engine/header.php';
include_once '../engine/dbConnect.php';

// Start session
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    echo '<p>You do not have permission to view this page.</p>';
    exit;
}

// Get the page number from the query string and validate it
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if (!filter_var($page, FILTER_VALIDATE_INT) || $page < 1) {
    header('Location: error.php');
    exit();
}

// Calculate the offset for the SQL query
$items_per_page = 20;
$offset = ($page - 1) * $items_per_page;

// Get users from database
$result = mysqli_query($conn, "SELECT * FROM users LIMIT $offset, $items_per_page");
$total_users = mysqli_query($conn, "SELECT COUNT(*) FROM users");
$total_users = mysqli_fetch_array($total_users)[0];
$total_pages = ceil($total_users / $items_per_page);
?>

<!-- Page Starts -->
<h1>Manage Users</h1>

<table class="table-custom">
    <tr>
        <th>Username</th>
        <th>Email</th>
        <th>Created At</th>
        <th>Manage User</th>
    </tr>
    <?php while($user = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['created_at']; ?></td>
            <td>
                <div class="dropdown">
                    <button class="dropbtn">Action
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="manage_user.php?username=<?php echo $user['username']; ?>">Manage</a>
                        <a href="../content/profile.php?id=<?php echo $user['id']; ?>">View</a>
                        <a href="#" onclick="confirmDelete(<?php echo $user['id']; ?>)">Delete</a>
                    </div>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<br>
<p>Pages:</p>
<div class="pagination">
    <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <a class="button-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>

<?php include_once '../engine/footer.php'; ?>

<script>
    function confirmDelete(userId) {
        if (confirm("Are you sure you want to delete this user?")) {
            location.href = 'delete_user.php?id=' + userId;
        }
    }
</script>

<?php
include_once '../engine/header.php';
include_once '../engine/dbConnect.php';

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    if (!isset($_SESSION['username'])) {
        echo "Session username is not set<br>";
    }
    if ($_SESSION['role'] != 'admin') {
        echo "Session role is not admin, it is: " . $_SESSION['role'] . "<br>";
    }
    // header('Location: ../content/login.php');
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
                <a class="button-link" href="manage_user.php?username=<?php echo $user['username']; ?>">Manage User</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<div class="pagination">
    <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <a class="button-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>

<?php include_once '../engine/footer.php'; ?>

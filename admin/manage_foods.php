<?php

/*
    admin/manage_foods.php    VERSION 1.3
    Accessed from the administrative portal.  Allows an administrator the ability to see, delete, and manage foods.
    Reviewed 7/8/2023
*/

// Include connections and header
include_once '../engine/header.php';
include_once '../engine/dbConnect.php';

// Start session
session_start();

// Validate user is logged in and an adminsitrator
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    if (!isset($_SESSION['username'])) {
        echo "Session username is not set<br>";
    }
    if ($_SESSION['role'] != 'admin') {
        echo "Session role is not admin, it is: " . $_SESSION['role'] . "<br>";
    }
    exit;
}

// Pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if (!filter_var($page, FILTER_VALIDATE_INT) || $page < 1) {
    header('Location: error.php');
    exit();
}

$items_per_page = 20;
$offset = ($page - 1) * $items_per_page;

// Define variables
$result = mysqli_query($conn, "SELECT * FROM food LIMIT $offset, $items_per_page");
$total_foods = mysqli_query($conn, "SELECT COUNT(*) FROM food");
$total_foods = mysqli_fetch_array($total_foods)[0];
$total_pages = ceil($total_foods / $items_per_page);
?>

<!-- Page starts -->
<h1>Manage Foods</h1>

<table class="table-custom">
    <tr>
        <th>Brand: Name</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>
    <?php while($food = mysqli_fetch_assoc($result)): ?>
        <tr> <!-- start of an entry -->
            <td><?php echo $food['brand'].": ".$food['name']; ?></td>
            <td><?php echo $food['created_at']; ?></td>
            <td>
                <div class="dropdown">
                    <button class="dropbtn">Action
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="../content/foods/manage_food.php?id=<?php echo $food['id']; ?>">Manage</a>
                        <a href="../content/foods/view_food.php?id=<?php echo $food['id']; ?>">View</a>
                        <a href="../engine/processes/delete_food.php?id=<?php echo $food['id']; ?>">Delete</a>
                    </div>
                </div>
            </td> <!-- Actions -->
        </tr> <!-- end of entry -->
    <?php endwhile; ?>
</table>
<br>
<p>Pages</p>
<div class="pagination">
    <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <a class="button-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>

<?php include_once '../engine/footer.php'; ?>

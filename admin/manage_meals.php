<?php
include_once '../engine/header.php';
include_once '../engine/dbConnect.php';

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

// Get meals from database
$result = mysqli_query($conn, "SELECT meals.id, meals.meal_type, users.username FROM meals JOIN users ON meals.user_id = users.id LIMIT $offset, $items_per_page");
$total_meals = mysqli_query($conn, "SELECT COUNT(*) FROM meals");
$total_meals = mysqli_fetch_array($total_meals)[0];
$total_pages = ceil($total_meals / $items_per_page);
?>

<h1>Manage Meals</h1>

<table class="table-custom">
    <tr>
        <th>Meal ID</th>
        <th>Username</th>
        <th>Meal Type</th>
        <th>Manage Meal</th>
    </tr>
    <?php while($meal = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $meal['id']; ?></td>
            <td><?php echo $meal['username']; ?></td>
            <td><?php echo $meal['meal_type']; ?></td>
            <td>
                <div class="dropdown">
                    <button class="dropbtn">Action
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="../content/meals/manage_meal.php?id=<?php echo $meal['id']; ?>">Manage</a>
                        <a href="../content/meals/view_meal.php?id=<?php echo $meal['id']; ?>">View</a>
                        <a href="#" onclick="confirmDelete(<?php echo $meal['id']; ?>)">Delete</a>
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
    function confirmDelete(mealId) {
        if (confirm("Are you sure you want to delete this meal?")) {
            location.href = '../engine/processes/delete_meal.php?id=' + mealId;
        }
    }
</script>

<?php
include('../engine/dbConnect.php'); // Include the dbConnect file
include('../engine/header.php'); // Include the header file
include('../engine/processes/fetch_meal_details.php'); // Include the header file
// Assuming user id is passed as GET parameter
$userId = $_GET['id'];

// Fetch user information from the database
$userQuery = "SELECT id, username, health_focus, dietary_restrictions FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!-- User Information -->
<div class="data-section">
    <h2>User Profile</h2>
    <p>Username: <?php echo $user['username']; ?></p>
    <p>ID: <?php echo $user['id']; ?></p>
    <p>Health Focus: <?php echo ($user['health_focus'] == 'Unaligned') ? 'N/A' : $user['health_focus']; ?></p>
    <p>Dietary Restrictions: <?php echo ($user['dietary_restrictions'] == 'Unrestricted') ? 'N/A' : $user['dietary_restrictions']; ?></p>
</div>

<!-- Meals -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?php
// Pagination variables
$limit = 7;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch user meals from the database
$mealQuery = "SELECT id, meal_date, meal_type, notes FROM meals WHERE user_id = ? ORDER BY meal_date DESC";
$stmt = $conn->prepare($mealQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$meals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get meal details using getProfileMeals() function
$mealDetails = getProfileMeals($userId, $conn);

// Group meals by date
$mealsGroupedByDate = [];
foreach($mealDetails as $meal) {
    $date = date('Y-m-d', strtotime($meal['meal_date']));
    $mealsGroupedByDate[$date][] = $meal;
}

// Sort the array by keys in reverse order
krsort($mealsGroupedByDate);

// Pagination
$totalDays = count($mealsGroupedByDate);
$mealsGroupedByDate = array_slice($mealsGroupedByDate, $start, $limit, true);

foreach($mealsGroupedByDate as $date => $meals) {
    echo '<div class="data-section">';
    echo '<h3>' . date('F j, Y', strtotime($date)) . '</h3>';
    $mealCount = 0; // Counter for meals
    echo '<div class="meal-container">'; // New meal container
    foreach($meals as $meal) {
        // Add a class to hide meals if there are more than four
        $hiddenClass = ($mealCount >= 4) ? ' hidden-meal' : '';
        echo '<div class="meal' . $hiddenClass . '">';
        echo '<table style="border: none; width: 100%;">';
        echo '<colgroup>
        <col style="width:15%">
        <col style="width:44%">
        <col style="width:40%">
                </colgroup>';
        echo '<tr><th>Type</th><th>Highlights</th><th></th></tr>';
        echo '<tr><td>' . $meal['meal_type'] . '</td>';
        echo '<td>';
        echo 'Calories: ' . $meal['total_calories'] . ', ';
        echo 'Proteins: ' . $meal['total_proteins'];
        echo '</td>';
        echo '<td><a class="button-link" href="../content/meals/view_meal.php?id=' . $meal['id'] . '">View</a> <a class="button-link" href="../content/meals/manage_meal.php?id=' . $meal['id'] . '">Manage</a></td>';
        echo '</tr>';
        echo '</table>';
        echo '</div>';
        $mealCount++;
    }

    // If there are more than four meals, show the "Show more" and "Show less" button
    if ($mealCount > 4) {
        echo '<a href="#" class="button-link show-more">Show more</a>';
        echo '<a href="#" class="button-link show-less hidden">Show less</a>';
    }
    echo '</div>'; // Close meal container
    echo '</div>';
}



// Calculate total pages for the meals
$total_pages = ceil($totalDays / $limit);
?>

<!-- Pagination -->
<p>Pages:</p>
<div class="pagination">
    <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <a class="button-link" href="?id=<?php echo $userId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>

<script src="../engine/javascript/profile_v1.01.js"></script>

<?php
include('../engine/footer.php'); // Include the footer file
?>

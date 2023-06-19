// TODO: Incorporate!
<?php
require_once '../../engine/dbConnect.php';
include '../../engine/header.php';
session_start();

if(!isset($_SESSION['username'])) {
    // User not logged in. Redirect them back to the login.php page.
    header('Location: ../login.php');
    exit;
}

// Get the user's id
$user_id = $_SESSION['user_id'];

// Fetch all food items added by the user
$query = "SELECT * FROM food WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$food_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Items</title>
    <link rel="stylesheet" href="../../css/style_v7.css">
</head>
<body>
<?php include '../../engine/header.php'; ?>

<h1>Your Food Items</h1>

<?php foreach ($food_items as $food): ?>
    <h2><?= htmlspecialchars($food['name']) ?></h2>
    <p>Brand: <?= htmlspecialchars($food['brand']) ?></p>
    <p>Food Group: <?= htmlspecialchars($food['food_group']) ?></p>
    <p>Serving Size: <?= htmlspecialchars($food['serving_size']) . ' user_food.php' . htmlspecialchars($food['serving_measurement']) ?></p>

    <h3>Ingredients</h3>
    <ul>
        <?php
        // Fetch the ingredients for this food item
        $query = "SELECT i.name FROM ingredients i JOIN food_ingredients fi ON i.id = fi.ingredient_id WHERE fi.food_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $food['id']);
        $stmt->execute();
        $ingredients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($ingredients as $ingredient):
            ?>
            <li><?= htmlspecialchars($ingredient['name']) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>

<?php include '../../engine/footer.php'; ?>
</body>
</html>

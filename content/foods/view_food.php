<?php
session_start();
require_once '../../engine/dbConnect.php';
include '../../engine/header.php';

// Sanitize the GET parameter
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Fetch data from the food table
$query = $conn->prepare("SELECT * FROM food WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$food = $result->fetch_assoc();

// Fetch data from the fats table
$query = $conn->prepare("SELECT * FROM fats WHERE food_id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$fats = $result->fetch_assoc();

// Fetch data from the additional_info table
$query = $conn->prepare("SELECT * FROM additional_info WHERE food_id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$additional_info = $result->fetch_assoc();

// Fetch ingredients for this food item
$query = $conn->prepare("SELECT ingredients.name FROM food_ingredients INNER JOIN ingredients ON food_ingredients.ingredient_id = ingredients.id WHERE food_ingredients.food_id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$ingredients = $result->fetch_all(MYSQLI_ASSOC);

// Fetch nutrients for this food item
$query = $conn->prepare("SELECT nutrients.name, nutrients.amount, nutrients.measurement FROM food_nutrients INNER JOIN nutrients ON food_nutrients.nutrient_id = nutrients.id WHERE food_nutrients.food_id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$nutrients = $result->fetch_all(MYSQLI_ASSOC);

// Fetch tags for this food item
$query = $conn->prepare("SELECT tags.name FROM food_tags INNER JOIN tags ON food_tags.tag_id = tags.id WHERE food_tags.food_id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$tags = $result->fetch_all(MYSQLI_ASSOC);

// Close the connection
$conn->close();
?>

<h1><?php echo htmlspecialchars($food['name']); ?></h1>
<div class="data-section">
    <h2>Details</h2>
    <p><strong>Brand:</strong> <?php echo htmlspecialchars($food['brand']); ?></p>
    <p><strong>Food Group:</strong> <?php echo htmlspecialchars($food['food_group']); ?></p>
    <p><strong>Serving Size:</strong> <?php echo htmlspecialchars($food['serving_size']) . ' ' . htmlspecialchars($food['serving_measurement']); ?></p>
</div>

<div class="data-section">
    <h2>Ingredients</h2>
    <p><?php echo htmlspecialchars(implode(", ", array_column($ingredients, 'name'))); ?></p>
</div>

<div class="data-section">
    <h2>Fats</h2>
    <table class="table-custom">
        <tr>
            <th>Total Fats</th>
            <th>Saturated Fats</th>
            <th>Trans Fats</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($fats['total']); ?></td>
            <td><?php echo htmlspecialchars($fats['saturated_fats']); ?></td>
            <td><?php echo htmlspecialchars($fats['trans_fats']); ?></td>
        </tr>
    </table>
</div>

<div class="data-section">
    <h2>Nutrients</h2>
    <p><?php echo htmlspecialchars(implode(", ", array_map(function($nutrient) { return $nutrient['name'] . ': ' . $nutrient['amount'] . $nutrient['measurement']; }, $nutrients))); ?></p>
</div>

<div class="data-section">
    <h2>Additional Info</h2>
    <table class="table-custom">
        <tr>
            <th>Cholesterol</th>
            <th>Dietary Fibres</th>
            <th>Total Sugars</th>
            <th>Sodium</th>
            <th>Proteins</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($additional_info['cholesterol']); ?></td>
            <td><?php echo htmlspecialchars($additional_info['dietary_fibres']); ?></td>
            <td><?php echo htmlspecialchars($additional_info['total_sugars']); ?></td>
            <td><?php echo htmlspecialchars($additional_info['sodium']); ?></td>
            <td><?php echo htmlspecialchars($additional_info['proteins']); ?></td>
        </tr>
    </table>
</div>

<div class="data-section">
    <h2>Tags</h2>
    <p><?php echo htmlspecialchars(implode(", ", array_column($tags, 'name'))); ?></p>
</div>
<a href="/content/foods/manage_food.php?id=<?php echo htmlspecialchars($id); ?>" class="button-link">Manage Food</a>
<a href="../../engine/processes/delete_food.php?id=<?php echo $food['id']; ?>" class="button-link red" onclick="return confirm('Are you sure you want to delete this food item?')">Delete</a>

<?php include '../../engine/footer.php'; ?>

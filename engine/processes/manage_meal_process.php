<?php
/*
    engine/proccesses/manage_meal_process.php    VERSION 1.3
    Code for meal modification.
    Reviewed 7/12/2023
*/

// Include your database connection file
require('../dbConnect.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user-id'])) {
    // Redirect to login page
    header('Location: ../../content/login.php');
    exit();
}

$user_id = $_SESSION['user-id'];

// Sanitize and retrieve the POST data
$meal_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$meal_date = filter_input(INPUT_POST, 'meal_date', FILTER_SANITIZE_STRING);
$meal_type = filter_input(INPUT_POST, 'meal_type', FILTER_SANITIZE_STRING);
$food_list_data = json_decode($_POST['food_list_data'], true);
$meal_notes = filter_input(INPUT_POST, 'meal_notes', FILTER_SANITIZE_STRING);

// Prepare SQL statement to update the meal in the meals table
$sql_update_meal = "UPDATE meals SET meal_date = ?, meal_type = ?, notes = ? WHERE id = ? AND user_id = ?";

$stmt = $conn->prepare($sql_update_meal);

if ($stmt === false) {
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}

// Bind parameters and execute statement
$stmt->bind_param('sssii', $meal_date, $meal_type, $meal_notes, $meal_id, $user_id);

if ($stmt->execute() === false) {
    die('execute() failed: ' . htmlspecialchars($stmt->error));
}

// Set the meal totals to 0 for the given meal by deleting existing totals
$sql_reset_totals = "UPDATE meal_totals SET 
    total_calories = 0.00, ingredient_list = NULL, total_cholesterol = 0.00, total_dietary_fibres = 0.00, total_sugars = 0.00,
    total_proteins = 0.00, total_sodium = 0.00, total_fat = 0.00, total_saturated_fats = 0.00, total_trans_fats = 0.00,
    total_calcium = 0.00, total_chloride = 0.00, total_chromium = 0.00, total_copper = 0.00, total_fluoride = 0.00, total_iodine = 0.00,
    total_iron = 0.00, total_magnesium = 0.00, total_manganese = 0.00, total_molybdenum = 0.00, total_phosphorus = 0.00,
    total_potassium = 0.00, total_selenium = 0.00, total_sulfur = 0.00, total_vitamin_A = 0.00, total_vitamin_B1 = 0.00,
    total_vitamin_B2 = 0.00, total_vitamin_B3 = 0.00, total_vitamin_B5 = 0.00, total_vitamin_B6 = 0.00, total_vitamin_B7 = 0.00,
    total_vitamin_B9 = 0.00, total_vitamin_B12 = 0.00, total_vitamin_C = 0.00, total_vitamin_D = 0.00, total_vitamin_E = 0.00,
    total_vitamin_K = 0.00, total_zinc = 0.00
    WHERE meal_id = ?";

$stmt_reset = $conn->prepare($sql_reset_totals);

if ($stmt_reset === false) {
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}

$stmt_reset->bind_param('i', $meal_id);

if ($stmt_reset->execute() === false) {
    die('execute() failed: ' . htmlspecialchars($stmt_reset->error));
}

$stmt_reset->close();


// Delete existing meal_foods for this meal
$sql_delete_meal_foods = "DELETE FROM meal_foods WHERE meal_id = ?";

$stmt_delete = $conn->prepare($sql_delete_meal_foods);

if ($stmt_delete === false) {
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}

$stmt_delete->bind_param('i', $meal_id);

if ($stmt_delete->execute() === false) {
    die('execute() failed: ' . htmlspecialchars($stmt_delete->error));
}

$stmt_delete->close();

// Re-add updated meal_foods for this meal
$sql_insert_meal_foods = "INSERT INTO meal_foods(meal_id, food_id, servings) VALUES(?, ?, ?)";

foreach ($food_list_data as $food_data) {
    $stmt2 = $conn->prepare($sql_insert_meal_foods);

    if ($stmt2 === false) {
        die('prepare() failed: ' . htmlspecialchars($conn->error));
    }

    $stmt2->bind_param('iis', $meal_id, $food_data['food_id'], $food_data['servings']);

    if ($stmt2->execute() === false) {
        die('execute() failed: ' . htmlspecialchars($stmt2->error));
    }

    $stmt2->close();

    require_once('../../engine/processes/update_meal_totals.php');

    // Call the update_meal_totals function
    update_meal_totals($meal_id, $conn, $food_data['food_id'], $food_data['servings']);

}


$stmt->close();
$conn->close();

// Redirect back to the meal list page
header("Location: ../../content/meals/view_meal.php?id=$meal_id");
exit();
?>

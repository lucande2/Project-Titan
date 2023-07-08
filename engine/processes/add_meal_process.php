<?php
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
$meal_date = filter_input(INPUT_POST, 'meal_date', FILTER_SANITIZE_STRING);
$meal_type = filter_input(INPUT_POST, 'meal_type', FILTER_SANITIZE_STRING);
$food_list_data = json_decode($_POST['food_list_data'], true);
$meal_notes = filter_input(INPUT_POST, 'meal_notes', FILTER_SANITIZE_STRING);


//...
//echo "User ID: " . $user_id . "<br>";

// Prepare SQL statement to insert the meal into the meals table
$sql_insert_meal = "INSERT INTO meals(user_id, meal_date, meal_type, notes) VALUES(?, ?, ?, ?)";

$stmt = $conn->prepare($sql_insert_meal);

if ($stmt === false) {
    // Handle error here, for example:
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}

// Bind parameters and execute statement
$stmt->bind_param('isss', $user_id, $meal_date, $meal_type, $meal_notes);

if ($stmt->execute() === false) {
    // Handle error here, for example:
    //die('execute() failed: ' . htmlspecialchars($stmt->error));
}

$meal_id = $conn->insert_id;


// Initalise Meal_Totals table
$sql_insert_meal_totals = "INSERT INTO meal_totals(meal_id) VALUES(?)";
$stmt3 = $conn->prepare($sql_insert_meal_totals);

if ($stmt3 === false) {
    // Handle error here
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}

$stmt3->bind_param('i', $meal_id);

if ($stmt3->execute() === false) {
    // Handle error here
    die('execute() failed: ' . htmlspecialchars($stmt3->error));
}

$stmt3->close();


$sql_insert_meal_foods = "INSERT INTO meal_foods(meal_id, food_id, servings) VALUES(?, ?, ?)";

foreach ($food_list_data as $food_data) {

    $stmt2 = $conn->prepare($sql_insert_meal_foods);

    if ($stmt2 === false) {
        die('prepare() failed: ' . htmlspecialchars($conn->error));
    }

    // Combine serving quantity and unit
    $servings = $food_data['servings']['quantity'] . ' ' . $food_data['servings']['unit'];

    $stmt2->bind_param('iis', $meal_id, $food_data['food_id'], $servings);

    if ($stmt2->execute() === false) {
        die('execute() failed: ' . htmlspecialchars($stmt2->error));
    }

    $stmt2->close();

    require_once('../../engine/processes/update_meal_totals.php');
    update_meal_totals($meal_id, $conn, $food_data['food_id'], $servings);
}


$stmt->close();
$conn->close();

// Redirect back to the meal list page
header("Location: ../../content/meals/view_meal.php?id=$meal_id");
exit();
?>
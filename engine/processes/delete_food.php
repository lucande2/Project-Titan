<?php
/*
    engine/proccesses/delete_food.php    VERSION 1.3
    Script which deletes a food item, and records in the relevant tables.
    Reviewed 7/12/2023
*/

// Include your database connection file
include '../dbConnect.php';

// Get the food id from the URL
$food_id = $_GET['id'];

// Create delete queries
$queries = [
    "DELETE FROM food_ingredients WHERE food_id = $food_id",
    "DELETE FROM fats WHERE food_id = $food_id",
    "DELETE FROM additional_info WHERE food_id = $food_id",
    "DELETE FROM food_nutrients WHERE food_id = $food_id",
    "DELETE FROM food_tags WHERE food_id = $food_id",
    "DELETE FROM food WHERE id = $food_id",
];

// Execute each delete query
foreach ($queries as $query) {
    if (!mysqli_query($conn, $query)) {
        die("Error deleting food: " . mysqli_error($conn));
    }
}

// Close the database connection
mysqli_close($conn);

// Redirect to the food list page
header('Location: ../../admin/manage_foods.php');
exit();
?>

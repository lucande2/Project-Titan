<?php
/*
    engine/proccesses/update_meal_totals.php    VERSION 1.3
    Script which is used to update the meal_totals table for when meals are updated.
    Reviewed 7/12/2023
*/

require(__DIR__ . '/../dbConnect.php');
require(__DIR__ . '/fetch_food_details.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function update_meal_totals($meal_id, $conn, $food_id, $servings)
{
    // Fetch food details
    $food = getFoodByID($food_id, $conn);

    if ($food === null) {
        // Food not found, return without doing anything
        return;
    }

    // Remove text from servings before calculations
    preg_match('/(\d+(\.\d+)?)\s*([a-zA-Z]+)/', $servings, $matches);
    $servings_number = $matches[1]; // Extract the servings number
    $servings_measurement = $matches[3]; // Extract the servings measurement

    // Fetch ingredients of the food
    $ingredients = getIngredientsByFoodID($food_id, $conn);

    // Concatenate ingredients into a string with comma separation
    $ingredient_list = implode(", ", $ingredients);

    // Fetch nutrients of the food
    $nutrients = getNutrientsByFoodID($food_id, $conn);

    // Fetch fats of the food
    $fats = getFatsByFoodID($food_id, $conn)[0];

    // Fetch additional info of the food
    $additional_info = getAdditionalInfoByFoodID($food_id, $conn)[0];

    // Prepare the update query
    $query = "UPDATE meal_totals SET ";

    // Initialize nutrient updates array
    $updates = [];

    $params = [];
    $types = "";

// Update ingredient list
    $updates[] = "ingredient_list = CONCAT(ingredient_list, ', ', ?)";
    $params[] = $ingredient_list;
    $types .= "s";

    foreach ($nutrients as $nutrient) {
        $nutrientName = $nutrient['name'];
        $multiplier = $nutrient['measurement'] == 'mg' ? 1000 : 1;
        $updates[] = "total_" . strtolower(str_replace(' ', '_', $nutrientName)) . " = total_" .
            strtolower(str_replace(' ', '_', $nutrientName)) . " + ?";
        $params[] = $nutrient['amount'] * $servings_number * $multiplier;
        $types .= "d";
    }

    // Update fats
    $updates[] = "total_fat = total_fat + ?";
    $params[] = $fats['total'] * $servings_number;
    $types .= "d";

    $updates[] = "total_saturated_fats = total_saturated_fats + ?";
    $params[] = $fats['saturated_fats'] * $servings_number;
    $types .= "d";

    $updates[] = "total_trans_fats = total_trans_fats + ?";
    $params[] = $fats['trans_fats'] * $servings_number;
    $types .= "d";

    // Update additional info
    $updates[] = "total_cholesterol = total_cholesterol + ?";
    $params[] = $additional_info['cholesterol'] * $servings_number;
    $types .= "d";

    $updates[] = "total_dietary_fibres = total_dietary_fibres + ?";
    $params[] = $additional_info['dietary_fibres'] * $servings_number;
    $types .= "d";

    $updates[] = "total_calories = total_calories + ?";
    $params[] = $food['calories'] * $servings_number;
    $types .= "d";


    $updates[] = "total_sugars = total_sugars + ?";
    $params[] = $additional_info['total_sugars'] * $servings_number;
    $types .= "d";

    $updates[] = "total_sodium = total_sodium + ?";
    $params[] = $additional_info['sodium'] * $servings_number;
    $types .= "d";

    $updates[] = "total_proteins = total_proteins + ?";
    $params[] = $additional_info['proteins'] * $servings_number;
    $types .= "d";



    // Concatenate updates to the query
    $query .= implode(', ', $updates);

    // Add the meal_id condition
    $query .= " WHERE meal_id = ?";
    $params[] = $meal_id;
    $types .= "i";

    // Prepare the statement
    $stmt = $conn->prepare($query);

    // Bind the parameters
    $stmt->bind_param($types, ...$params);

    // Execute the statement
    if (!$stmt->execute()) {
        // Handle error
        echo "Error updating meal totals: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

?>

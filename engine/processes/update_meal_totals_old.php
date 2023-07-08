<?php

require(__DIR__ . '/../dbConnect.php');
require_once('fetch_meal_details.php');
require_once('fetch_food_details.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function update_meal_totals($meal_id, $conn, $food_id, $servings)
{
    // Get food information
    $food = getFoodByID($food_id, $conn);
    $nutrients = getNutrientsByFoodID($food_id, $conn);
    $ingredients = getIngredientsByFoodID($food_id, $conn);
    $tags = getTagsByFoodID($food_id, $conn);
    $fats = getFatsByFoodID($food_id, $conn);
    $additional_info = getAdditionalInfoByFoodID($food_id, $conn);

    // Remove text from servings before calculations
    preg_match('/(\d+(\.\d+)?)\s*([a-zA-Z]+)/', $servings, $matches);
    $servings_number = $matches[1]; // Extract the servings number
    $servings_measurement = $matches[3]; // Extract the servings measurement

    // Calculate the totals based on the number of servings
    foreach ($additional_info as $key => $value) {
        if (is_numeric($value)) {
            $additional_info[$key] *= $servings_number;
        }
    }
    foreach ($fats as $key => $value) {
        if (is_numeric($value)) {
            $fats[$key] *= $servings_number;
        }
    }

    // Calculate the totals based on the number of servings
    // Check if the necessary elements in the arrays exist before trying to access them
    $total_cholesterol = isset($additional_info[0]['cholesterol']) ? $additional_info[0]['cholesterol'] * $servings_number : 0;
    $total_dietary_fibres = isset($additional_info[0]['dietary_fibres']) ? $additional_info[0]['dietary_fibres'] * $servings_number : 0;
    $total_sugars = isset($additional_info[0]['total_sugars']) ? $additional_info[0]['total_sugars'] * $servings_number : 0;
    $total_proteins = isset($additional_info[0]['proteins']) ? $additional_info[0]['proteins'] * $servings_number : 0;
    $total_sodium = isset($additional_info[0]['sodium']) ? $additional_info[0]['sodium'] * $servings_number : 0;
    $total_fat = isset($fats[0]['total']) ? $fats[0]['total'] * $servings_number : 0;
    $total_saturated_fats = isset($fats[0]['saturated_fats']) ? $fats[0]['saturated_fats'] * $servings_number : 0;
    $total_trans_fats = isset($fats[0]['trans_fats']) ? $fats[0]['trans_fats'] * $servings_number : 0;

    // Get the nutrients for the food
    $nutrients = getNutrientsByFoodID($food['id'], $conn);

    // Concatenate the ingredients into a comma-separated string
    $ingredient_list = implode(", ", $ingredients);

    // Initialize the nutrient totals
    $total_calcium = 0;
    $total_chloride = 0;
    $total_chromium = 0;
    $total_copper = 0;
    $total_fluoride = 0;
    $total_iodine = 0;
    $total_iron = 0;
    $total_magnesium = 0;
    $total_manganese = 0;
    $total_molybdenum = 0;
    $total_phosphorous = 0;
    $total_potassium = 0;
    $total_selenium = 0;
    $total_sulfur = 0;
    $total_vitamin_A = 0;
    $total_vitamin_B1 = 0;
    $total_vitamin_B2 = 0;
    $total_vitamin_B3 = 0;
    $total_vitamin_B5 = 0;
    $total_vitamin_B6 = 0;
    $total_vitamin_B7 = 0;
    $total_vitamin_B9 = 0;
    $total_vitamin_B12 = 0;
    $total_vitamin_C = 0;
    $total_vitamin_D = 0;
    $total_vitamin_E = 0;
    $total_vitamin_K = 0;
    $total_zinc = 0;

    // Calculate the nutrient totals
    foreach ($nutrients as $nutrient) {
        $nutrientName = $nutrient['name'];
        $multiplier = $nutrient['measurement'] == 'mg' ? 1000 : 1;
        switch ($nutrientName) {
            case 'Calcium':
                $total_calcium += $nutrient['amount'] * $servings_number * $multiplier;
                break;
            case 'Chloride':
                $total_chloride += $nutrient['amount'] * $servings_number;
                break;
            case 'Chromium':
                $total_chromium += $nutrient['amount'] * $servings_number;
                break;
            case 'Copper':
                $total_copper += $nutrient['amount'] * $servings_number;
                break;
            case 'Fluoride':
                $total_fluoride += $nutrient['amount'] * $servings_number;
                break;
            case 'Iodine':
                $total_iodine += $nutrient['amount'] * $servings_number;
                break;
            case 'Iron':
                $total_iron += $nutrient['amount'] * $servings_number;
                break;
            case 'Magnesium':
                $total_magnesium += $nutrient['amount'] * $servings_number;
                break;
            case 'Manganese':
                $total_manganese += $nutrient['amount'] * $servings_number;
                break;
            case 'Molybdenum':
                $total_molybdenum += $nutrient['amount'] * $servings_number;
                break;
            case 'Phosphorous':
                $total_phosphorous += $nutrient['amount'] * $servings_number;
                break;
            case 'Potassium':
                $total_potassium += $nutrient['amount'] * $servings_number;
                break;
            case 'Selenium':
                $total_selenium += $nutrient['amount'] * $servings_number;
                break;
            case 'Sulfur':
                $total_sulfur += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin A':
                $total_vitamin_A += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin B1':
                $total_vitamin_B1 += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin B2':
                $total_vitamin_B2 += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin B3':
                $total_vitamin_B3 += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin B5':
                $total_vitamin_B5 += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin B6':
                $total_vitamin_B6 += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin B7':
                $total_vitamin_B7 += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin B9':
                $total_vitamin_B9 += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin B12':
                $total_vitamin_B12 += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin C':
                $total_vitamin_C += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin D':
                $total_vitamin_D += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin E':
                $total_vitamin_E += $nutrient['amount'] * $servings_number;
                break;
            case 'Vitamin K':
                $total_vitamin_K += $nutrient['amount'] * $servings_number;
                break;
            case 'Zinc':
                $total_zinc += $nutrient['amount'] * $servings_number;
                break;
            // Add more cases for other nutrients if needed
            default:
                break;
        }
    }

    // Query preparation
    $query = "INSERT INTO meal_totals (meal_id, total_calories, ingredient_list, total_cholesterol, total_dietary_fibres, 
                         total_sugars, total_proteins, total_sodium, total_fat, total_saturated_fats, total_trans_fats, 
                         total_calcium, total_chloride, total_chromium, total_copper, total_fluoride, total_iodine, total_iron, 
                         total_magnesium, total_manganese, total_molybdenum, total_phosphorous, total_potassium, total_selenium, 
                         total_sulfur, total_vitamin_A, total_vitamin_B1, total_vitamin_B2, total_vitamin_B3, total_vitamin_B5, 
                         total_vitamin_B6, total_vitamin_B7, total_vitamin_B9, total_vitamin_B12, total_vitamin_C, total_vitamin_D, 
                         total_vitamin_E, total_vitamin_K, total_zinc)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Heartbeat to keep connection alive
    if (!mysqli_ping($conn)) {
        // Close the connection
        mysqli_close($conn);
        // Reconnect
        require(__DIR__ . '/../dbConnect.php');
    }

// Prepare the statement
    $stmt = $conn->prepare($query);

// Check if the statement was successfully prepared
    if (!$stmt) {
        //echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        return false;
    }

// Bind parameters
    $stmt->bind_param(
        'idsdddddddddddddddddddddddddddddddddddd',
        $meal_id,
        $total_calories,
        $total_cholesterol,
        $ingredient_list,
        $total_dietary_fibres,
        $total_sugars,
        $total_proteins,
        $total_sodium,
        $total_fat,
        $total_saturated_fats,
        $total_trans_fats,
        $total_calcium,
        $total_chloride,
        $total_chromium,
        $total_copper,
        $total_fluoride,
        $total_iodine,
        $total_iron,
        $total_magnesium,
        $total_manganese,
        $total_molybdenum,
        $total_phosphorous,
        $total_potassium,
        $total_selenium,
        $total_sulfur,
        $total_vitamin_A,
        $total_vitamin_B1,
        $total_vitamin_B2,
        $total_vitamin_B3,
        $total_vitamin_B5,
        $total_vitamin_B6,
        $total_vitamin_B7,
        $total_vitamin_B9,
        $total_vitamin_B12,
        $total_vitamin_C,
        $total_vitamin_D,
        $total_vitamin_E,
        $total_vitamin_K,
        $total_zinc
    );

    // Execute the statement
    if (!$stmt->execute()) {
        //echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        return false;
    }

    // Close the statement
    $stmt->close();

    return true;
}
?>
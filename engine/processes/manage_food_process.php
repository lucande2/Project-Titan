<?php
require_once '../../engine/dbConnect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $food_id = mysqli_real_escape_string($conn, htmlspecialchars($_POST['id']));

    // Sanitize input
    $food_name = mysqli_real_escape_string($conn, htmlspecialchars($_POST['name']));
    $brand = mysqli_real_escape_string($conn, htmlspecialchars($_POST['brand']));
    $food_group = mysqli_real_escape_string($conn, htmlspecialchars($_POST['food_group']));
    $serving_size = mysqli_real_escape_string($conn, htmlspecialchars($_POST['serving_size']));
    $serving_measurement = mysqli_real_escape_string($conn, htmlspecialchars($_POST['serving_measurement']));
    $calories = !empty($_POST['calories']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['calories'])) : "0";

    // Prepare SQL statement to update food item
    $stmt = $conn->prepare("UPDATE food SET name = ?, brand = ?, food_group = ?, serving_size = ?, serving_measurement = ?, calories = ? WHERE id = ?");
    $stmt->bind_param("sssissi", $food_name, $brand, $food_group, $serving_size, $serving_measurement, $calories, $food_id);

    // Execute SQL statement
    $stmt->execute();

    // Fetch existing ingredients
    $stmt = $conn->prepare("SELECT ingredient_id FROM food_ingredients WHERE food_id = ?");
    $stmt->bind_param('i', $food_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_ingredients = array();
    while($row = $result->fetch_assoc()) {
        $existing_ingredients[] = $row['ingredient_id'];
    }

    // Incoming ingredients
    $incoming_ingredients = array();
    foreach ($_POST['ingredients'] as $ingredient) {
        $ingredient = mysqli_real_escape_string($conn, htmlspecialchars($ingredient));

        $stmt = $conn->prepare("SELECT id FROM ingredients WHERE name = ?");
        $stmt->bind_param("s", $ingredient);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $ingredient_id = $result->fetch_assoc()['id'];
        } else {
            $stmt = $conn->prepare("INSERT INTO ingredients (name) VALUES (?)");
            $stmt->bind_param("s", $ingredient);
            $stmt->execute();
            $ingredient_id = $conn->insert_id;
        }

        $incoming_ingredients[] = $ingredient_id;
    }

    // Ingredients to delete are in existing_ingredients but not in incoming_ingredients
    $ingredients_to_delete = array_diff($existing_ingredients, $incoming_ingredients);

    // Ingredients to add are in incoming_ingredients but not in existing_ingredients
    $ingredients_to_add = array_diff($incoming_ingredients, $existing_ingredients);

    // Delete ingredients
    $stmt = $conn->prepare("DELETE FROM food_ingredients WHERE food_id = ? AND ingredient_id = ?");
    foreach($ingredients_to_delete as $ingredient_id) {
        $stmt->bind_param('ii', $food_id, $ingredient_id);
        $stmt->execute();
    }

    // Add ingredients
    $stmt = $conn->prepare("INSERT INTO food_ingredients (food_id, ingredient_id) VALUES (?, ?)");
    foreach($ingredients_to_add as $ingredient_id) {
        $stmt->bind_param('ii', $food_id, $ingredient_id);
        $stmt->execute();
    }

    // Update the fats information
    $total_fats = !empty($_POST['total_fats']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['total_fats'])) : "0";
    $saturated_fats = !empty($_POST['saturated_fats']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['saturated_fats'])) : "0";
    $trans_fats = !empty($_POST['trans_fats']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['trans_fats'])) : "0";

    $stmt = $conn->prepare("UPDATE fats SET total = ?, saturated_fats = ?, trans_fats = ? WHERE food_id = ?");
    $stmt->bind_param("iiii", $total_fats, $saturated_fats, $trans_fats, $food_id);
    $stmt->execute();

    // Update the additional info
    $cholesterol = !empty($_POST['cholesterol']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['cholesterol'])) : "0";
    $dietary_fibres = !empty($_POST['dietary_fibres']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['dietary_fibres'])) : "0";
    $total_sugars = !empty($_POST['total_sugars']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['total_sugars'])) : "0";
    $proteins = !empty($_POST['proteins']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['proteins'])) : "0";
    $sodium = !empty($_POST['sodium']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['sodium'])) : "0";

    $stmt = $conn->prepare("UPDATE additional_info SET cholesterol = ?, dietary_fibres = ?, total_sugars = ?, proteins = ?, sodium = ? WHERE food_id = ?");
    $stmt->bind_param("iiiiii", $cholesterol, $dietary_fibres, $total_sugars, $proteins, $sodium, $food_id);
    
    $stmt->execute();

    // Get existing nutrients for this food item
    $stmt = $conn->prepare("SELECT * FROM nutrients WHERE id IN (SELECT nutrient_id FROM food_nutrients WHERE food_id = ?)");
    $stmt->bind_param("i", $food_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_nutrients = $result->fetch_all(MYSQLI_ASSOC);

    // Process the nutrients that were passed in the POST data
    $nutrient_names = $_POST['nutrient_names'];
    $nutrient_amounts = $_POST['nutrient_amounts'];
    $nutrient_units = $_POST['nutrient_units'];

    for ($i = 0; $i < count($nutrient_names); $i++) {
        $name = mysqli_real_escape_string($conn, htmlspecialchars($nutrient_names[$i]));
        $amount = mysqli_real_escape_string($conn, htmlspecialchars($nutrient_amounts[$i]));
        $unit = mysqli_real_escape_string($conn, htmlspecialchars($nutrient_units[$i]));

        $found = false;
        foreach ($existing_nutrients as $key => $existing_nutrient) {
            if ($existing_nutrient['name'] == $name) {
                $found = true;
                unset($existing_nutrients[$key]);  // Remove it from the array

                if ($existing_nutrient['amount'] != $amount || $existing_nutrient['measurement'] != $unit) {
                    // If the nutrient has changed, update it
                    $stmt = $conn->prepare("UPDATE nutrients SET amount = ?, measurement = ? WHERE id = ?");
                    $stmt->bind_param("isi", $amount, $unit, $existing_nutrient['id']);
                    $stmt->execute();
                }
                break;
            }
        }

        if (!$found) {
            // If the nutrient didn't already exist, insert it
            $stmt = $conn->prepare("INSERT INTO nutrients (name, amount, measurement) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $name, $amount, $unit);
            $stmt->execute();

            // Get the id of the nutrient that was just inserted
            $nutrient_id = $conn->insert_id;

            // Now insert the food-nutrient relationship
            $stmt = $conn->prepare("INSERT INTO food_nutrients (food_id, nutrient_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $food_id, $nutrient_id);
            $stmt->execute();
        }
    }

    // If there are any remaining nutrients in the $existing_nutrients array, they were not included in the POST data and should be deleted
    foreach ($existing_nutrients as $existing_nutrient) {
        // Delete the food-nutrient relationship
        $stmt = $conn->prepare("DELETE FROM food_nutrients WHERE food_id = ? AND nutrient_id = ?");
        $stmt->bind_param("ii", $food_id, $existing_nutrient['id']);
        $stmt->execute();

        // Delete the nutrient
        $stmt = $conn->prepare("DELETE FROM nutrients WHERE id = ?");
        $stmt->bind_param("i", $existing_nutrient['id']);
        $stmt->execute();
    }

    // Tags
    $tags = isset($_POST['tags']) ? $_POST['tags'] : array();


    $stmt = $conn->prepare("SELECT tag_id FROM food_tags WHERE food_id = ?");
    $stmt->bind_param('i', $food_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_tags = array();
    while($row = $result->fetch_assoc()) {
        $existing_tags[] = $row['tag_id'];
    }

    // Incoming tags
    $incoming_tags = array();
    foreach ($tags as $tag) {
        $tag = mysqli_real_escape_string($conn, htmlspecialchars($tag));

        // Check if the tag exists
        $stmt = $conn->prepare("SELECT id FROM tags WHERE name = ?");
        $stmt->bind_param("s", $tag);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If the tag exists, get its id
            $tag_id = $result->fetch_assoc()['id'];
        } else {
            // If the tag doesn't exist, insert it
            $stmt = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
            $stmt->bind_param("s", $tag);
            $stmt->execute();

            // Get the id of the tag that was just inserted
            $tag_id = $conn->insert_id;
        }

        // Fill the incoming_tags array
        $incoming_tags[] = $tag_id;
    }

// Delete food_tags entries that are not in incoming_tags
    $stmt = $conn->prepare("DELETE FROM food_tags WHERE food_id = ? AND tag_id = ?");
    foreach($existing_tags as $tag_id) {
        if (!in_array($tag_id, $incoming_tags)) {
            $stmt->bind_param('ii', $food_id, $tag_id);
            $stmt->execute();
        }
    }

// Insert food_tags entries that are not in existing_tags
    $stmt = $conn->prepare("INSERT INTO food_tags (food_id, tag_id) VALUES (?, ?)");
    foreach($incoming_tags as $tag_id) {
        if (!in_array($tag_id, $existing_tags)) {
            $stmt->bind_param('ii', $food_id, $tag_id);
            $stmt->execute();
        }
    }

    // Redirect to the updated food page
    header("Location: /content/foods/view_food.php?id=$food_id");
    exit;
}
?>

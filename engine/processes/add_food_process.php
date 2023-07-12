<?php
/*
    engine/proccesses/add_food_process.php    VERSION 1.3
    Script for add_food.php processing
    Reviewed 7/12/2023
*/

session_start();
require_once '../../engine/dbConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $food_name = mysqli_real_escape_string($conn, htmlspecialchars($_POST['name']));
    $brand = mysqli_real_escape_string($conn, htmlspecialchars($_POST['brand']));
    $food_group = mysqli_real_escape_string($conn, htmlspecialchars($_POST['food_group']));
    $serving_size = mysqli_real_escape_string($conn, htmlspecialchars($_POST['serving_size']));
    $serving_measurement = mysqli_real_escape_string($conn, htmlspecialchars($_POST['serving_measurement']));
    $calories = !empty($_POST['calories']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['calories'])) : 0;

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO food (name, brand, food_group, serving_size, serving_measurement, calories, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssisi", $food_name, $brand, $food_group, $serving_size, $serving_measurement, $calories); // Calories added to bind_param

    // Execute SQL statement
    $stmt->execute();

    // Get the id of the food item that was just inserted
    $food_id = $conn->insert_id;
    // Now, loop through the ingredients and insert them
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

        $stmt = $conn->prepare("INSERT INTO food_ingredients (food_id, ingredient_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $food_id, $ingredient_id);
        $stmt->execute();
    }

    // Insert the fats information
    $total_fats = !empty($_POST['total_fats']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['total_fats'])) : 0;
    $saturated_fats = !empty($_POST['saturated_fats']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['saturated_fats'])) : 0;
    $trans_fats = !empty($_POST['trans_fats']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['trans_fats'])) : 0;

    $stmt = $conn->prepare("INSERT INTO fats (food_id, total, saturated_fats, trans_fats) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $food_id, $total_fats, $saturated_fats, $trans_fats);
    $stmt->execute();

// Insert the additional info
    $cholesterol = !empty($_POST['cholesterol']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['cholesterol'])) : 0;
    $dietary_fibres = !empty($_POST['dietary_fibres']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['dietary_fibres'])) : 0;
    $total_sugars = !empty($_POST['total_sugars']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['total_sugars'])) : 0;
    $proteins = !empty($_POST['proteins']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['proteins'])) : 0;
    $sodium = !empty($_POST['sodium']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['sodium'])) : 0;

    $stmt = $conn->prepare("INSERT INTO additional_info (food_id, cholesterol, dietary_fibres, total_sugars, proteins, sodium) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiii", $food_id, $cholesterol, $dietary_fibres, $total_sugars, $proteins, $sodium);
    $stmt->execute();


    // Insert the nutrients and their amounts
    $nutrient_names = $_POST['nutrient_names'];
    $nutrient_amounts = $_POST['nutrient_amounts'];
    $nutrient_units = $_POST['nutrient_units'];

    for ($i = 0; $i < count($nutrient_names); $i++) {
        $name = mysqli_real_escape_string($conn, htmlspecialchars($nutrient_names[$i]));
        $amount = mysqli_real_escape_string($conn, htmlspecialchars($nutrient_amounts[$i]));
        $unit = mysqli_real_escape_string($conn, htmlspecialchars($nutrient_units[$i]));

        // Insert the nutrient
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

    // Now, loop through the tags and insert them
    foreach ($_POST['tags'] as $tag) {
        $tag = mysqli_real_escape_string($conn, htmlspecialchars($tag));

        $stmt = $conn->prepare("SELECT id FROM tags WHERE name = ?");
        $stmt->bind_param("s", $tag);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $tag_id = $result->fetch_assoc()['id'];
        } else {
            $stmt = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
            $stmt->bind_param("s", $tag);
            $stmt->execute();
            $tag_id = $conn->insert_id;
        }

        $stmt = $conn->prepare("INSERT INTO food_tags (food_id, tag_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $food_id, $tag_id);
        $stmt->execute();
    }

    // Redirect to the new food page
    header("Location: ../../content/view_food.php?id=$food_id");
    exit;
}
?>

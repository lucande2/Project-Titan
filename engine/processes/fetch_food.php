<?php

function getFoodByID($id, $conn)
{
// Prepare a statement
$stmt = $conn->prepare("SELECT * FROM food WHERE id = ?");
// Bind parameters
$stmt->bind_param("i", $id);
// Execute the statement
$stmt->execute();
// Get the result
$result = $stmt->get_result();
// Fetch data
$food = $result->fetch_assoc();
// Free result
$result->free();
// Close the statement
$stmt->close();

return $food;
}
function getIngredientsByFoodID($id, $mysqli) {
    if ($stmt = $mysqli->prepare("SELECT ingredients.name FROM ingredients JOIN food_ingredients ON ingredients.id = food_ingredients.ingredient_id WHERE food_ingredients.food_id = ?")) {
        $stmt->bind_param('i', $id);

        if($stmt->execute()) {
            $result = $stmt->get_result();
            $ingredients = array();
            while($row = $result->fetch_assoc()) {
                $ingredients[] = $row['name'];
            }
            return $ingredients;
        }
        $stmt->close();
    }
    return NULL;
}

function getNutrientsByFoodID($id, $mysqli) {
    if ($stmt = $mysqli->prepare("SELECT nutrients.name, nutrients.amount, nutrients.measurement FROM nutrients JOIN food_nutrients ON nutrients.id = food_nutrients.nutrient_id WHERE food_nutrients.food_id = ?")) {
        $stmt->bind_param('i', $id);

        if($stmt->execute()) {
            $result = $stmt->get_result();
            $nutrients = array();
            while($row = $result->fetch_assoc()) {
                $nutrients[] = $row;
            }
            return $nutrients;
        }
        $stmt->close();
    }
    return NULL;
}

function getTagsByFoodID($id, $mysqli) {
    if ($stmt = $mysqli->prepare("SELECT tags.name FROM tags JOIN food_tags ON tags.id = food_tags.tag_id WHERE food_tags.food_id = ?")) {
        $stmt->bind_param('i', $id);

        if($stmt->execute()) {
            $result = $stmt->get_result();
            $tags = array();
            while($row = $result->fetch_assoc()) {
                $tags[] = $row['name'];
            }
            return $tags;
        }
        $stmt->close();
    }
    return NULL;
}

function getFatsByFoodID($id, $mysqli) {
    if ($stmt = $mysqli->prepare("SELECT total, saturated_fats, trans_fats FROM fats WHERE food_id = ?")) {
        $stmt->bind_param('i', $id);

        if($stmt->execute()) {
            $result = $stmt->get_result();
            $fats = array();
            while($row = $result->fetch_assoc()) {
                $fats[] = $row;
            }
            return $fats;
        }
        $stmt->close();
    }
    return NULL;
}

function getAdditionalInfoByFoodID($id, $mysqli) {
    if ($stmt = $mysqli->prepare("SELECT cholesterol, dietary_fibres, total_sugars, sodium, proteins FROM additional_info WHERE food_id = ?")) {
        $stmt->bind_param('i', $id);

        if($stmt->execute()) {
            $result = $stmt->get_result();
            $additional_info = array();
            while($row = $result->fetch_assoc()) {
                $additional_info[] = $row;
            }
            return $additional_info;
        }
        $stmt->close();
    }
    return NULL;
}

?>


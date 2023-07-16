<?php
/*
    engine/proccesses/fetch_food_details.php    VERSION 1.3
    Script which retrieves food details.  Functions are used in add_meal, view_food.
    Reviewed 7/12/2023
*/

require(__DIR__ . '/../dbConnect.php');

function getFoodByID($id, $conn)
{
    if ($conn instanceof mysqli) { // Check if $conn is a valid MySQLi connection object
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
    } else {
        // Handle the case when $conn is not a valid connection object
        return null; // or any appropriate error handling
    }
}
function getIngredientsByFoodID($id, $conn) {
    if ($stmt = $conn->prepare("SELECT ingredients.name FROM ingredients JOIN food_ingredients ON ingredients.id = food_ingredients.ingredient_id WHERE food_ingredients.food_id = ?")) {
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
    return [];
}

function getNutrientsByFoodID($id, $conn) {
    if ($stmt = $conn->prepare("SELECT nutrients.name, nutrients.amount, nutrients.measurement FROM nutrients JOIN food_nutrients ON nutrients.id = food_nutrients.nutrient_id WHERE food_nutrients.food_id = ?")) {
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
    return [];
}

function getTagsByFoodID($id, $conn) {
    if ($stmt = $conn->prepare("SELECT tags.name FROM tags JOIN food_tags ON tags.id = food_tags.tag_id WHERE food_tags.food_id = ?")) {
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
    return [];
}

function getFatsByFoodID($id, $conn) {
    if ($stmt = $conn->prepare("SELECT total, saturated_fats, trans_fats FROM fats WHERE food_id = ?")) {
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                // No fats record found, create a blank record with zero values
                $stmt = $conn->prepare("INSERT INTO fats (food_id, total, saturated_fats, trans_fats) VALUES (?, 0, 0, 0)");
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    $stmt->close();
                } else {
                    // Handle the INSERT error
                    echo "Error creating fats record: " . $stmt->error;
                    $stmt->close();
                    return [];
                }
            } else {
                $fats = array();
                while ($row = $result->fetch_assoc()) {
                    $fats[] = $row;
                }
                return $fats;
            }
        } else {
            // Handle the SELECT error
            echo "Error fetching fats record: " . $stmt->error;
            $stmt->close();
            return [];
        }
    } else {
        // Handle the prepare error
        echo "Error preparing fats statement: " . $conn->error;
        return [];
    }
}

function getAdditionalInfoByFoodID($id, $conn) {
    if ($stmt = $conn->prepare("SELECT cholesterol, dietary_fibres, total_sugars, sodium, proteins FROM additional_info WHERE food_id = ?")) {
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                // No additional_info record found, create a blank record with zero values
                $stmt = $conn->prepare("INSERT INTO additional_info (food_id, cholesterol, dietary_fibres, total_sugars, sodium, proteins) VALUES (?, 0, 0, 0, 0, 0)");
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    $stmt->close();
                } else {
                    // Handle the INSERT error
                    echo "Error creating additional_info record: " . $stmt->error;
                    $stmt->close();
                    return [];
                }
            } else {
                $additional_info = array();
                while ($row = $result->fetch_assoc()) {
                    $additional_info[] = $row;
                }
                return $additional_info;
            }
        } else {
            // Handle the SELECT error
            echo "Error fetching additional_info record: " . $stmt->error;
            $stmt->close();
            return [];
        }
    } else {
        // Handle the prepare error
        echo "Error preparing additional_info statement: " . $conn->error;
        return [];
    }
}



?>


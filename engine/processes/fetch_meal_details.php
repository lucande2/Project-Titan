<?php
/*
    engine/proccesses/fetch_meal_details.php    VERSION 1.3
    Fetches meal details for use in view_meal, profiles, and analysis.
    Reviewed 7/12/2023
*/

function getMealDetails($id, $conn) {
    $mealDetails = [];

    // Fetch meal data and associated user
    $query = $conn->prepare(
        "SELECT meals.*, users.username, meals.notes, users.id as user_id
         FROM meals
         INNER JOIN users ON meals.user_id = users.id
         WHERE meals.id = ?"
    );

    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $meal = $result->fetch_assoc();
    $mealDetails['meal'] = $meal;
    $mealDetails['notes'] = $meal['notes'];
    $mealDetails['user_id'] = $meal['user_id'];  // storing user_id in the returned array

    // Fetch food items in the meal along with servings
    $query = $conn->prepare("SELECT food.*, meal_foods.servings, food.serving_measurement
                             FROM meal_foods
                             INNER JOIN food ON meal_foods.food_id = food.id
                             WHERE meal_foods.meal_id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $foods = $result->fetch_all(MYSQLI_ASSOC);
    $mealDetails['foods'] = $foods;

    // Fetch meal total nutrients data
    $query = $conn->prepare("SELECT * FROM meal_totals WHERE meal_id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $totals = $result->fetch_assoc();
    $mealDetails['totals'] = $totals;

    $query->close();
    $conn->close();

    return $mealDetails;
}


function getProfileMeals($userId, $conn) {
    // Fetch meal data
    $query = $conn->prepare(
        "SELECT meals.id, meals.meal_date, meals.meal_type, meal_totals.total_calories, meal_totals.total_proteins
         FROM meals
         INNER JOIN meal_totals ON meals.id = meal_totals.meal_id
         WHERE meals.user_id = ?"
    );

    if (!$query) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $query->bind_param("i", $userId);
    $query->execute();

    $result = $query->get_result();
    $meals = $result->fetch_all(MYSQLI_ASSOC);

    $query->close();

    return $meals;
}


?>

<?php
/*
    engine/proccesses/delete_meal.php    VERSION 1.3
    The script for meal deletion.
    Reviewed 7/12/2023
*/

include '../dbConnect.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../content/login.php");
    exit();
}

// Get the meal ID from the query string
if (isset($_GET['id'])) {
    $meal_id = $_GET['id'];

    // Check if the logged-in user is the creator of the meal
    $user_id = $_SESSION['user-id'];
    $sql = "SELECT * FROM meals WHERE id = '$meal_id' AND user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        // Delete the meal and associated records
        $deleteMealSQL = "DELETE FROM meals WHERE id = '$meal_id'";
        $deleteMealFoodsSQL = "DELETE FROM meal_foods WHERE meal_id = '$meal_id'";
        $deleteMealTotalsSQL = "DELETE FROM meal_totals WHERE meal_id = '$meal_id'";

        // Execute the deletion queries

        mysqli_query($conn, $deleteMealFoodsSQL);
        mysqli_query($conn, $deleteMealTotalsSQL);
        mysqli_query($conn, $deleteMealSQL);

        // Redirect to a page indicating successful deletion
        // Check if the user is an admin
        if ($_SESSION['role'] != 'admin') {
            // Redirect to the search food list page with the user id
            header('Location: ../../content/profile.php?id=' . $_SESSION['user_id']);
        }
        else {
            // Redirect to the manage food list page
            header('Location: ../../admin/manage_foods.php');
        }
        exit();
    } else {
        // The logged-in user is not the creator of the meal
        echo "You are not authorized to delete this meal.";
    }
} else {
    // No meal ID provided in the query string
    echo "Invalid request.";
}

?>

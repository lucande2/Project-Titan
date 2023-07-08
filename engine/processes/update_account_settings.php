<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../dbConnect.php';
include_once 'analysis_preset.php';

// Retrieve form data and sanitize inputs
$user_id = $_SESSION['user-id'];
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$health_focus = filter_input(INPUT_POST, 'health_focus', FILTER_SANITIZE_STRING);
$dietary_restrictions = filter_input(INPUT_POST, 'dietary_restrictions', FILTER_SANITIZE_STRING);
$biological_sex = filter_input(INPUT_POST, 'biological_sex', FILTER_SANITIZE_STRING);
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$profile_privacy = filter_input(INPUT_POST, 'profile_privacy', FILTER_VALIDATE_BOOLEAN);
$site_theme = filter_input(INPUT_POST, 'site_theme', FILTER_SANITIZE_STRING);


// Update the password if it is not empty
if (!empty($password)) {
    // Hash the new password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update the hashed password in the database
    $query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $query->bind_param("si", $hashed_password, $user_id);
    $query->execute();

    // Check if the update was successful
    if ($query->affected_rows > 0) {
        // Password update successful
    } else {
        // Password update failed
    }
}

// Save old values
$query = $conn->prepare("SELECT biological_sex, health_focus FROM users WHERE id = ?");
$query->bind_param('i', $user_id);
$query->execute();

$result = $query->get_result();
$userOldValues = $result->fetch_assoc();

// Now update the values in the database
// Update the health focus, dietary restrictions, and biological sex in the database
$query = $conn->prepare("UPDATE users SET username = ?, health_focus = ?, dietary_restrictions = ?, biological_sex = ?, profile_privacy = ?, site_theme = ? WHERE id = ?");
$query->bind_param("ssssisi", $username, $health_focus, $dietary_restrictions, $biological_sex, $profile_privacy, $site_theme, $user_id);
$query->execute();

if ($userOldValues['biological_sex'] != $biological_sex || $userOldValues['health_focus'] != $health_focus) {
    // The gender or focus has changed. Drop all user values and re-initialise.
    $stmt = $conn->prepare("DELETE FROM ac_user_values WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    initialiseUserNutrientValues($user_id, $biological_sex, $health_focus);
}

// Get array of tracked nutrients from the form data
$tracked_nutrients = filter_input(INPUT_POST, 'tracked_nutrients', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

// Remove all current tracking for the user
$query = $conn->prepare("DELETE FROM ac_user_tracking WHERE user_id = ?");
$query->bind_param('i', $user_id);
$query->execute();

// Add all the new tracking for the user
foreach ($tracked_nutrients as $nutrient_id) {
    $query = $conn->prepare("INSERT INTO ac_user_tracking (user_id, nutrient_id) VALUES (?, ?)");
    $query->bind_param('ii', $user_id, $nutrient_id);
    $query->execute();
}

// Check if the update was successful
if ($query->affected_rows > 0) {
    // Update successful
    header("Location: ../../content/profile.php?id={$user_id}");
    exit;
} else {
    // Update failed
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
}

?>
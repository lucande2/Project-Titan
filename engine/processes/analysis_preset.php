<?php
/*
    engine/proccesses/analysis_preset.php    VERSION 1.3
    Analysis functions pertaining to preset values.
    Reviewed 7/12/2023
*/

include_once '../dbConnect.php';
include_once '../../engine/dbConnect.php';

function presetValueFetch() {
    global $conn;
    $gender = $_POST['gender'];
    $focus = $_POST['focus'];

    $query = "SELECT n.nutrient_name, p.ac_amount, m.measurement_name
              FROM ac_preset_values p 
              JOIN ac_nutrients n ON p.ac_nutrient_id = n.ac_nutrient_id
              JOIN ac_measurements m ON p.ac_measurement_id = m.ac_measurement_id
              JOIN ac_gender g ON p.ac_gender_id = g.ac_gender_id
              JOIN ac_focus f ON p.ac_focus_id = f.ac_focus_id
              WHERE g.gender_name = ? AND f.focus_name = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $gender, $focus);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($data);
}



/*
 * Initialise Values (on Administration Panel)
 * Gives an administrator the ability to reinitialise an account's variables if there is an issue.
 */
function initialiseUserNutrientValues($userId, $biological_sex = 'male', $health_focus = 'healthy') {
    global $conn;

    // Get the genderId and focusId based on the user's selection
    $genderIdSql = "SELECT ac_gender_id FROM ac_gender WHERE gender_name = ?";
    $focusIdSql = "SELECT ac_focus_id FROM ac_focus WHERE focus_name = ?";

    $stmt = $conn->prepare($genderIdSql);
    $stmt->bind_param('s', $biological_sex);
    $stmt->execute();
    $genderId = $stmt->get_result()->fetch_assoc()['ac_gender_id'];

    $stmt = $conn->prepare($focusIdSql);
    $stmt->bind_param('s', $health_focus);
    $stmt->execute();
    $focusId = $stmt->get_result()->fetch_assoc()['ac_focus_id'];

    // Get the nutrients and measurements for the selected gender and focus
    $stmt = $conn->prepare("SELECT ac_nutrient_id, ac_amount, ac_measurement_id FROM ac_preset_values WHERE ac_gender_id = ? AND ac_focus_id = ?");
    $stmt->bind_param('ii', $genderId, $focusId);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Loop through the results and insert each nutrient value for the user
    foreach($results as $row) {
        $stmt = $conn->prepare("INSERT INTO ac_user_values (user_id, ac_gender_id, ac_focus_id, ac_nutrient_id, ac_amount, ac_measurement_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iiidii', $userId, $genderId, $focusId, $row['ac_nutrient_id'], $row['ac_amount'], $row['ac_measurement_id']);
        $stmt->execute();
    }
}

function resetToPresetValues($userId) {
    global $conn;

    // Fetch the user's biological_sex and health_focus from the users table
    $userQuery = "SELECT biological_sex, health_focus FROM users WHERE id = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $userResult = $stmt->get_result()->fetch_assoc();
    $biological_sex = $userResult['biological_sex'];
    $health_focus = $userResult['health_focus'];

    // Get the genderId and focusId based on the user's selection
    $genderIdSql = "SELECT ac_gender_id FROM ac_gender WHERE gender_name = ?";
    $focusIdSql = "SELECT ac_focus_id FROM ac_focus WHERE focus_name = ?";

    $stmt = $conn->prepare($genderIdSql);
    $stmt->bind_param('s', $biological_sex);
    $stmt->execute();
    $genderId = $stmt->get_result()->fetch_assoc()['ac_gender_id'];

    $stmt = $conn->prepare($focusIdSql);
    $stmt->bind_param('s', $health_focus);
    $stmt->execute();
    $focusId = $stmt->get_result()->fetch_assoc()['ac_focus_id'];

    // Get the nutrients and measurements for the selected gender and focus
    $stmt = $conn->prepare("SELECT ac_nutrient_id, ac_amount, ac_measurement_id FROM ac_preset_values WHERE ac_gender_id = ? AND ac_focus_id = ?");
    $stmt->bind_param('ii', $genderId, $focusId);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Loop through the results and update each nutrient value for the user
    foreach($results as $row) {
        $stmt = $conn->prepare("UPDATE ac_user_values SET ac_amount = ?, ac_measurement_id = ? WHERE user_id = ? AND ac_nutrient_id = ?");
        $stmt->bind_param('diii', $row['ac_amount'], $row['ac_measurement_id'], $userId, $row['ac_nutrient_id']);
        $stmt->execute();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset"])) {
    $userId = $_POST["userId"];
    resetToPresetValues($userId);  // Fetches and uses biological_sex and health_focus from the users table
}


?>


<?php
/*
    engine/proccesses/analysis_values.php   VERSION 1.3
    Functions pertaining to analysis values.
    Reviewed 7/12/2023
*/

include_once '../../engine/dbConnect.php';

/*
 * getUserValues (For "Analysis".  NOT values.php)
 * Get values ONLY IF tracked
 */
function getUserValues($userId, $conn) {
    $query = "SELECT n.ac_nutrient_id, n.nutrient_name, u.ac_amount, m.measurement_name,
             (CASE WHEN t.user_id IS NULL THEN 0 ELSE 1 END) as is_tracked
          FROM ac_user_values u 
          JOIN ac_nutrients n ON u.ac_nutrient_id = n.ac_nutrient_id
          JOIN ac_measurements m ON u.ac_measurement_id = m.ac_measurement_id
          LEFT JOIN ac_user_tracking t ON u.user_id = t.user_id AND u.ac_nutrient_id = t.nutrient_id
          WHERE u.user_id = ? AND t.user_id IS NOT NULL";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        if ($result = $stmt->get_result()) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data;
        } else {
            error_log("Failed to execute query: (" . $stmt->errno . ") " . $stmt->error);
            return false;
        }
    } else {
        error_log("Failed to prepare statement: (" . $conn->errno . ") " . $conn->error);
        return false;
    }
}

/*
 * getUserValues2 (For "VALUES.php".  NOT ANALYSIS_COMPARE)
 * Does not care if user tracks value or not!
 */
function getUserValues2($userId, $conn) {
    $query = "SELECT n.ac_nutrient_id, n.nutrient_name, u.ac_amount, m.measurement_name,
             (CASE WHEN t.user_id IS NULL THEN 0 ELSE 1 END) as is_tracked
          FROM ac_user_values u 
          JOIN ac_nutrients n ON u.ac_nutrient_id = n.ac_nutrient_id
          JOIN ac_measurements m ON u.ac_measurement_id = m.ac_measurement_id
          LEFT JOIN ac_user_tracking t ON u.user_id = t.user_id AND u.ac_nutrient_id = t.nutrient_id
          WHERE u.user_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        if ($result = $stmt->get_result()) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            return $data;
        } else {
            error_log("Failed to execute query: (" . $stmt->errno . ") " . $stmt->error);
            return false;
        }
    } else {
        error_log("Failed to prepare statement: (" . $conn->errno . ") " . $conn->error);
        return false;
    }
}

/*
 * multiplyUserValues
 * Multiplies the ac_amount of each nutrient in the userValues array by the number of days in the range.
 */
function multiplyUserValues($userValues, $days) {
    foreach($userValues as &$value) {
        $value['ac_amount'] *= $days;
    }
    return $userValues;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["new_amount"])) {
        $userId = $_POST['user_id'];
        include_once '../../engine/dbConnect.php';
        if(updateUserValues($userId, $_POST["new_amount"], $conn)){
            header("Location: /content/analysis/values.php?id=".$userId);
            exit;
        }
    }
}

?>
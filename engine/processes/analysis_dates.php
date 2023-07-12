<?php
/*
    engine/proccesses/analysis_dates.php    VERSION 1.3
    Functions for analysis centre pertaining to the dates.  Used specifically in week_ and custom_look.php
    Reviewed 7/12/2023
*/

//include_once '../dbConnect.php';
include_once '../../engine/dbConnect.php';

/*
 * select_time_frame
 * Returns an associative array with 'start_date' and 'end_date' values.
 * The returned dates span a maximum of 31 days.
 */
function select_time_frame($startDate, $endDate) {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);

    $diff = $start->diff($end)->days;

    // Limiting the difference to 31 days
    if ($diff > 31) {
        $end = clone $start;
        $end->add(new DateInterval('P31D'));
    }

    return array('start_date' => $start, 'end_date' => $end);
}

/*
 * fetch_dates
 * Returns an array of DateTime objects for every day within the provided range.
 * Handles month changes correctly.
 */
function fetch_dates($startDate, $endDate) {
    $dates = array();
    $period = new DatePeriod(
        $startDate,
        new DateInterval('P1D'),
        $endDate->add(new DateInterval('P1D'))  // Adding 1 day to include end date in the period
    );

    foreach ($period as $key => $value) {
        $dates[] = $value;
    }

    return $dates;
}

/*
 * fetch_meals_inRange
 * Fetches any meals for the provided user within the specified date range.
 * Returns an array of meal data for each day within the range that has meals.
 */
function fetch_meals_inRange($userId, $dates, $conn) {
    $mealsInRange = array();

    $query = "SELECT * FROM meals WHERE user_id = ? AND meal_date BETWEEN ? AND ?";

    if ($stmt = $conn->prepare($query)) {
        foreach($dates as $date) {
            $formattedDate = $date->format('Y-m-d');
            $stmt->bind_param('iss', $userId, $formattedDate, $formattedDate);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows > 0){
                $meals = $result->fetch_all(MYSQLI_ASSOC);
                $mealsInRange[$date->format('Y-m-d')] = $meals;
            }
        }
    } else {
        error_log("Failed to prepare statement: (" . $conn->errno . ") " . $conn->error);
    }

    return $mealsInRange;
}

?>

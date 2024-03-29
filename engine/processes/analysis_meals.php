<?php
/*
    engine/proccesses/analysis_meals.php    VERSION 1.3
    Functions for the analysis centre pertaining to meals.
    Reviewed 7/12/2023
*/

include_once '../../engine/dbConnect.php';

/*
 * fetch_meal_totals
 * Fetches the total nutrient values for each meal in the mealsInRange array.
 */
function fetch_meal_totals($mealsInRange, $conn) {
    $mealTotals = array();

    $query = "SELECT * FROM meal_totals WHERE meal_id = ?";

    if ($stmt = $conn->prepare($query)) {
        foreach($mealsInRange as $date => $meals) {
            foreach($meals as $meal) {
                $stmt->bind_param('i', $meal['id']);
                $stmt->execute();
                $result = $stmt->get_result();

                if($result->num_rows > 0){
                    $mealTotal = $result->fetch_assoc();
                    $mealTotals[$meal['id']] = $mealTotal;
                }
            }
        }
    } else {
        error_log("Failed to prepare statement: (" . $conn->errno . ") " . $conn->error);
    }

    return $mealTotals;
}

/*
 * fetch_daily_meal_totals
 * Fetches the total nutrient values for each day in the mealsInRange array.
 */
function fetch_daily_meal_totals($mealsInRange, $conn) {
    $dailyTotals = array();

    $query = "SELECT * FROM meal_totals WHERE meal_id = ?";

    if ($stmt = $conn->prepare($query)) {
        foreach($mealsInRange as $date => $meals) {
            // Initialise daily totals for this date
            $dailyTotals[$date] = array();
            foreach($meals as $meal) {
                $stmt->bind_param('i', $meal['id']);
                $stmt->execute();
                $result = $stmt->get_result();

                if($result->num_rows > 0){
                    $mealTotal = $result->fetch_assoc();

                    // Add the meal total to the daily totals
                    foreach($mealTotal as $nutrient => $total) {
                        // Skip the id and meal_id fields
                        if($nutrient != 'id' && $nutrient != 'meal_id') {
                            // Initialise nutrient total if it doesn't exist yet
                            if(!array_key_exists($nutrient, $dailyTotals[$date])) {
                                $dailyTotals[$date][$nutrient] = 0;
                            }

                            // Add the meal total to the daily total
                            $dailyTotals[$date][$nutrient] += $total;
                        }
                    }
                }
            }
        }
    } else {
        error_log("Failed to prepare statement: (" . $conn->errno . ") " . $conn->error);
    }

    return $dailyTotals;
}


/*
 * sum_meal_totals
 * Sums up the total nutrient values for all meals in the mealTotals array.
 */
function sum_meal_totals($mealTotals) {
    $totalsSum = array();

    // Initialize the totals sum array with zeroes
    foreach($mealTotals as $mealTotal) {
        foreach($mealTotal as $nutrient => $total) {
            // Skip the id and meal_id fields
            if($nutrient != 'id' && $nutrient != 'meal_id') {
                if(!array_key_exists($nutrient, $totalsSum)) {
                    $totalsSum[$nutrient] = 0;
                }
            }
        }
    }

    // Add up the totals
    foreach($mealTotals as $mealTotal) {
        foreach($mealTotal as $nutrient => $total) {
            // Skip the id and meal_id fields
            if($nutrient != 'id' && $nutrient != 'meal_id') {
                $totalsSum[$nutrient] += $total;
            }
        }
    }

    return $totalsSum;
}

/*
 * linkValues
 * Links the column names of the meal_totals table to their corresponding nutrient names in the ac_nutrients table.
 * Also performs conversions of units to standardise them!
 */
function linkValues($totals, $conn) {
    // Column to nutrient name mapping
    $mapping = [
        'total_calories' => 'Calories',
        'total_cholesterol' => 'Cholesterol',
        'total_dietary_fibres' => 'Fibres',
        'total_sugars' => 'Sugars',
        'total_proteins' => 'Proteins',
        'total_sodium' => 'Sodium',
        'total_fat' => 'Fat',
        'total_saturated_fats' => 'Saturated Fat',
        'total_trans_fats' => 'Trans Fat',
        'total_calcium' => 'Calcium',
        'total_chloride' => 'Chloride',
        'total_chromium' => 'Chromium',
        'total_copper' => 'Copper',
        'total_fluoride' => 'Fluoride',
        'total_iodine' => 'Iodine',
        'total_iron' => 'Iron',
        'total_magnesium' => 'Magnesium',
        'total_manganese' => 'Manganese',
        'total_molybdenum' => 'Molybdenum',
        'total_phosphorus' => 'Phosphorus',
        'total_potassium' => 'Potassium',
        'total_selenium' => 'Selenium',
        'total_sulfur' => 'Sulfur',
        'total_vitamin_A' => 'Vitamin A',
        'total_vitamin_B1' => 'Vitamin B1',
        'total_vitamin_B2' => 'Vitamin B2',
        'total_vitamin_B3' => 'Vitamin B3',
        'total_vitamin_B5' => 'Vitamin B5',
        'total_vitamin_B6' => 'Vitamin B6',
        'total_vitamin_B7' => 'Vitamin B7',
        'total_vitamin_B9' => 'Vitamin B9',
        'total_vitamin_B12' => 'Vitamin B12',
        'total_vitamin_C' => 'Vitamin C',
        'total_vitamin_D' => 'Vitamin D',
        'total_vitamin_E' => 'Vitamin E',
        'total_vitamin_K' => 'Vitamin K',
        'total_zinc' => 'Zinc',
    ];

    if (is_array(reset($totals))) {
        // If it's a multidimensional array (e.g., array of daily totals)
        foreach($totals as $date => $dailyTotals) {
            foreach($dailyTotals as $column => $value) {
                if(array_key_exists($column, $mapping)) {
                    $nutrient_name = $mapping[$column];
                    $totals[$date][$nutrient_name] = $value;
                    unset($totals[$date][$column]);
                }
            }
        }
    } else {
        // If it's a single-dimensional array (e.g., total over all days)
        foreach($totals as $column => $value) {
            if(array_key_exists($column, $mapping)) {
                $nutrient_name = $mapping[$column];
                $totals[$nutrient_name] = $value;
                unset($totals[$column]);
            }
        }
    }

    return $totals;
}


function addUnits($values) {
    $units = array(
        'Saturated Fat' => ' g',
        'Trans Fat' => ' g',
        'Calories' => ' cal',
        'Cholesterol' => ' %',
        'Fibres' => ' g',
        'Sugars' => ' g',
        'Sodium' => ' mg',
        'Fat' => ' g',
        'Proteins' => ' g'
    );

    foreach ($values as $key => $value) {
        if (isset($units[$key])) {
            $values[$key] .= $units[$key];
        } else if ($key !== 'ingredient_list') {
            if ($value > 1000) {
                $values[$key] = ($value / 1000) . ' mg';
            } else {
                $values[$key] .= ' mcg';
            }
        }
    }

    return $values;
}


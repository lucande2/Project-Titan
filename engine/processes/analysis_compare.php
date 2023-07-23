<?php
/*
    engine/proccesses/analysis_compare.php    VERSION 1.3
    The brains of the analysis centre.  Takes the values, classifies them in a rule group.
    Reviewed 7/12/2023
*/

include_once '/usr/share/nginx/html_project/engine/dbConnect.php';
include_once 'analysis_meals.php';

// Print the current working directory
//echo 'Current working directory: ' . getcwd() . "<br>";

// Print the current include path
//echo 'Current include path: ' . get_include_path() . "<br>";

/*
 *  Normalise Nutrient Values
 */
function normalizeUserDefinedValue($nutrient, $value) {
    $nutrientsToNormalize = [
        'Copper',
        'Fluoride',
        'Iron',
        'Magnesium',
        'Manganese',
        'Phosphorus',
        'Potassium',
        'Sulfur',
        'Vitamin B1',
        'Vitamin B2',
        'Vitamin B3',
        'Vitamin B5',
        'Vitamin B6',
        'Vitamin C',
        'Vitamin E',
        'Zinc'
    ];

    if (in_array($nutrient, $nutrientsToNormalize)) {
        return $value * 1000;
    }

    return $value;
}

/*
 * ANALYSIS RULES GROUP 1
 * Calcium, Chloride, Chromium, Iodine, Molybdenum, Selenium, Vitamin A, Vitamin B7, Vitamin B9, Vitamin B12,
 * Vitamin D, Vitamin K, Copper, Fluoride, Iron, Magnesium, Manganese, Phosphorus, Potassium, Sulfur, Vitamin B1,
 * Vitamin B2, Vitamin B3, Vitamin B5, Vitamin B6, Vitamin C, Vitamin E, Zinc: These need to be evaluated for
 * being within +/- 10% of the user-defined values.
 */
function analysis_rules_group1($nutrient, $userDefinedValue, $actualValue) {
    $userDefinedValue = normalizeUserDefinedValue($nutrient, $userDefinedValue);

    $lowerLimit = 0.9 * $userDefinedValue;
    $upperLimit = 1.1 * $userDefinedValue;

    if ($actualValue < $lowerLimit) {
        return "low";
    } else if ($actualValue > $upperLimit) {
        return "high";
    } else {
        return "good";
    }
}


/*
 * ANALYSIS RULES GROUP 2
 * Sodium, Sugars, Calories, Fats: These need to be evaluated for being over +10% of the user-defined values.
 */
function analysis_rules_group2($nutrient, $userDefinedValue, $actualValue) {
    $userDefinedValue = normalizeUserDefinedValue($nutrient, $userDefinedValue);

    $upperLimit = 1.1 * $userDefinedValue;

    if ($actualValue > $upperLimit) {
        return "high";
    } else {
        return "good";
    }
}


/*
 * ANALYSIS RULES GROUP 3
 * For the remainder
 */
function analysis_rules_group3($nutrient, $userDefinedValue, $actualValue) {
    $userDefinedValue = normalizeUserDefinedValue($nutrient, $userDefinedValue);

    $lowerLimit = 0.9 * $userDefinedValue;

    if ($actualValue < $lowerLimit) {
        return "low";
    } else {
        return "good";
    }
}


/*
 * ANALYSIS COMPARE
 *
 */
function analysis_compare($userValues, $dailyMealTotals) {
    $analysisResult = array();
    global $conn;

    // Use linkValues to map the nutrients
    $linkedDailyMealTotals = linkValues($dailyMealTotals, $conn);

    // Evaluate each nutrient for each day
    foreach($linkedDailyMealTotals as $date => $dailyTotals) {
        foreach($userValues as $value) {
            $nutrient_name = $value['nutrient_name'];
            $userDefinedValue = $value['ac_amount'];
            $actualValue = isset($dailyTotals[$nutrient_name]) ? $dailyTotals[$nutrient_name] : 0;

            /*// Debugging lines
            echo "<p style='color: white'>";
            echo "Nutrient: " . $nutrient_name . "<br>";
            echo "User defined value: " . $userDefinedValue . "<br>";
            echo "Actual value: " . $actualValue . "<br>";
            echo "</p>";*/

            // Check which group the nutrient belongs to and call the corresponding function
            switch($nutrient_name) {
                case 'Calcium':
                case 'Chloride':
                case 'Chromium':
                case 'Iodine':
                case 'Molybdenum':
                case 'Selenium':
                case 'Vitamin A':
                case 'Vitamin B7':
                case 'Vitamin B9':
                case 'Vitamin B12':
                case 'Vitamin D':
                case 'Vitamin K':
                case 'Copper':
                case 'Fluoride':
                case 'Iron':
                case 'Magnesium':
                case 'Manganese':
                case 'Phosphorus':
                case 'Potassium':
                case 'Sulfur':
                case 'Vitamin B1':
                case 'Vitamin B2':
                case 'Vitamin B3':
                case 'Vitamin B5':
                case 'Vitamin B6':
                case 'Vitamin C':
                case 'Vitamin E':
                case 'Zinc':
                    $result = analysis_rules_group1($nutrient_name, $userDefinedValue, $actualValue);
                    break;
                case 'Sodium':
                case 'Sugars':
                case 'Calories':
                case 'Fat':
                case 'Trans Fat':
                case 'Saturated Fat':
                    $result = analysis_rules_group2($nutrient_name, $userDefinedValue, $actualValue);
                    break;

                case 'Fibres':
                case 'Proteins':
                    $result = analysis_rules_group3($nutrient_name, $userDefinedValue, $actualValue);
                    break;
            }

            $analysisResult[$date][$nutrient_name] = $result;
        }
    }

    return $analysisResult;
}

/*
 *  getAverages will gather and average "goods/highs/lows".
 */
function getAverages($analysisResult) {
    $summary = array('good' => 0, 'high' => 0, 'low' => 0, 'total' => 0);

    foreach ($analysisResult as $date => $nutrientResults) {
        foreach ($nutrientResults as $nutrient => $result) {
            $summary[$result]++;
            $summary['total']++;
        }
    }

    $summary['good'] /= $summary['total'];
    $summary['high'] /= $summary['total'];
    $summary['low'] /= $summary['total'];

    return $summary;
}

/*
 *  troublesomeNutrients
 *  Flagged nutrients
 */
function troublesomeNutrients($analysisResults) {
    $troublesome = array();

    // Loop over the analysis results
    foreach($analysisResults as $date => $dailyResult) {
        // Loop over the results for each nutrient
        foreach($dailyResult as $nutrient => $result) {
            // Check if the nutrient has a 'high' or 'low' result
            if($result == 'high' || $result == 'low') {
                // If it does, increment the count for that nutrient in the $troublesome array
                if(array_key_exists($nutrient, $troublesome)) {
                    $troublesome[$nutrient] += 1;
                } else {
                    $troublesome[$nutrient] = 1;
                }
            }
        }
    }

    // Sort the array by value in descending order so that the nutrients with the most 'high' and 'low' results are first
    arsort($troublesome);

    return $troublesome;
}

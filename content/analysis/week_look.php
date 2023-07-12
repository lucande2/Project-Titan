<?php
/*
    content/analysis/week_look.php    VERSION 1.3
    Allows a user to view their week analysis starting on the previous Sunday.  It also allows the previous week.
    Reviewed 7/12/2023
*/

include_once '../../engine/header.php';
include_once '../../engine/dbConnect.php';
include_once '../../engine/processes/analysis_dates.php';
include_once '../../engine/processes/analysis_values.php';
include_once '../../engine/processes/analysis_meals.php';
include_once '../../engine/processes/analysis_compare.php';
include_once '../../engine/processes/analysis_chart.php';

$userId = $_GET['id'];

if (isset($_GET['prevSunday'])) {
    // Use the provided date as the last Sunday
    $lastSunday = $_GET['prevSunday'];

    // Calculate the previous Saturday
    $lastSaturday = date('Y-m-d', strtotime($lastSunday . ' + 6 days'));
} else {
    // Get today's day of the week (0 (for Sunday) through 6 (for Saturday))
    $todayDayOfWeek = date('w');

    // Get the last Sunday
    $lastSunday = date('Y-m-d', strtotime('-'.$todayDayOfWeek.' days'));

    // Get the Saturday after the last Sunday
    $lastSaturday = date('Y-m-d', strtotime($lastSunday. ' + 6 days'));
}

// Set up variables for use in functions
$startDate = $lastSunday;
$endDate = $lastSaturday;

// The timeframe from the form
$timeFrame = select_time_frame($startDate, $endDate);

// Array of dates within the timeframe
$dates = fetch_dates($timeFrame['start_date'], $timeFrame['end_date']);

// A list of all meals within the date ranges
$mealsInRange = fetch_meals_inRange($userId, $dates, $conn);

// Fetch meal total nutrient values for each meal in range
$mealTotals = fetch_meal_totals($mealsInRange, $conn);
//print_r($mealTotals);

// Fetch user's nutrient values for display later
$userValues = getUserValues($userId, $conn);

// Sum up the meal totals
$totalsSum = sum_meal_totals($mealTotals);

// Multiply nutrient values by the number of days in the range
$multipliedValues = multiplyUserValues($userValues, count($dates));

// Fetch daily meal totals for meals in range
$dailyMealTotals = fetch_daily_meal_totals($mealsInRange, $conn);

// Link the meal totals to their corresponding nutrient names
$linkedValues = linkValues($totalsSum, $conn);

// Link the daily meal totals to their corresponding nutrient names
$linkedDailyMealTotals = linkValues($dailyMealTotals, $conn);

// Call the analysis_compare function to compute the analysis results
$analysisResults = analysis_compare($userValues, $linkedDailyMealTotals);

// Get the averages
$averages = getAverages($analysisResults);

// Determine the troublesome nutrients
$troublesome = troublesomeNutrients($analysisResults);

// Get the top # most troublesome nutrients
$topTroublesome = array_slice($troublesome, 0, 10, true);

// Create a new array to hold the chart data for all days.
$chartData = [];

// Output data
$output = array(
    'timeFrame' => $timeFrame,
    'dates' => $dates,
    'mealsInRange' => $mealsInRange
);


// Initialize an array to hold the pie chart data for each day.
$analysisData = [];

foreach($analysisResults as $date => $dayAnalysis) {
    $dayData = ["good" => 0, "low" => 0, "high" => 0];
    foreach($dayAnalysis as $nutrient => $result) {
        $dayData[$result]++;
    }

    $analysisData[$date] = [
        "good" => ["label" => "Good", "value" => $dayData["good"], "color" => generateAnalysisColor("good")],
        "low" => ["label" => "Too low", "value" => $dayData["low"], "color" => generateAnalysisColor("low")],
        "high" => ["label" => "Too high", "value" => $dayData["high"], "color" => generateAnalysisColor("high")],
    ];
}
?>

<!-- Page Starts-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://kit.fontawesome.com/0bd93e423d.js" crossorigin="anonymous"></script>

<h2>Analysis Centre</h2>
<ul class="centre-menu">
    <!-- Adding the links to the center menu -->
    <li class="menu-item"><a href="values.php?id=<?php echo $userId; ?>">Your<br>Values</a></li>
    <li class="menu-item"><a href="week_look.php?id=<?php echo $userId; ?>">Weekly<br>Progress</a></li>
    <li class="menu-item"><a href="custom_look.php?id=<?php echo $userId; ?>">Custom Range<br>Analysis</a></li>
</ul>
<br><
<div class="data-section">
    <h1>Your Week</h1>
    <a href="week_look.php?id=<?= $userId ?>&prevSunday=<?= date('Y-m-d', strtotime($lastSunday. ' - 1 week')) ?>"
       class="btn btn-primary">Previous Week</a>
    <a href="custom_look.php?id=<?= $userId ?>" class="btn btn-secondary">Custom Range</a>
</div>

<!-- Summary Section -->
<div class="data-section">
    <h2>Summary</h2>
    <!-- Most Troublesome Nutrients -->
    <div>
        <h3>Average Nutritional Intake</h3>
        <p>Displays how often recommendations were met or exceeded in the date range.</p>
        <table class="table-custom">
            <thead>
            <tr>
                <th>Category</th>
                <th>Average</th>
            </tr>
            </thead>
            <tbody>
            <?php $averages = getAverages($analysisResults); ?>
            <tr>
                <td>Good</td>
                <td><?php echo number_format($averages['good'] * 100, 2); ?>%</td>
            </tr>
            <tr>
                <td>Too Low</td>
                <td><?php echo number_format($averages['low'] * 100, 2); ?>%</td>
            </tr>
            <tr>
                <td>Too High</td>
                <td><?php echo number_format($averages['high'] * 100, 2); ?>%</td>
            </tr>
            </tbody>
        </table>
        <h3>Most Troublesome Nutrients</h3>
        <ul>
            <?php foreach($topTroublesome as $nutrient => $count) : ?>
                <li><?= $nutrient ?>: <?= $count ?> days</li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Day-by-Day Breakout -->
<?php
// Output for meals within range and their total nutrient values
foreach($mealsInRange as $date => $meals) {
    echo "<div class=data-section>";
    echo "<h2>Meals on " . $date . ":</h2>";

    // Display each meal for the day
    foreach ($meals as $meal) {
        echo "Meal ID: " . $meal['id'] . ", meal type: " . $meal['meal_type'] . "<br>";
    }

    // Get the data for the current day's pie chart.
    $dayData = $analysisData[$date];

    // Display the pie chart
    echo '<div class="chart-row">'; // Create a new row for the chart and table
    echo '<div class="chart-container" style="height:200px; width:200px;">';
    echo '<canvas id="analysis-chart-' . $date . '"></canvas>';
    echo '</div>';

    $chartDataForDay = [
        'labels' => array_column($dayData, 'label'),
        'data' => array_map('floatval', array_column($dayData, 'value')),
        'colors' => array_column($dayData, 'color'),
        'date' => $date,
    ];

    $chartData[] = $chartDataForDay;

    // Create table
    echo '<div class="chart-data-table">'; 
    echo '<table class="table-custom">';
    echo "<tr><th>Color</th><th>Label</th><th>Count</th></tr>";
    foreach ($analysisData[$date] as $analysisType => $typeData) {
        echo "<tr>";
        echo "<td style='background-color: " . $typeData['color'] . ";'></td>"; // color
        echo "<td>" . $typeData['label'] . "</td>"; // label
        echo "<td>" . $typeData['value'] . "</td>"; // count
        echo "</tr>";
    }
    echo '</table>';
    echo '</div>'; // Close table div
    echo '</div>'; // Close chart row

    // If there are total nutrient values for this day, display them
    if (array_key_exists($date, $dailyMealTotals)) {
        echo "<h3>Nutrients Analysis</h3>";
        echo "<table class='table-custom'>";
        echo "<tr><th>Nutrient</th><th>Daily Recommended Total</th><th>Total Daily Intake</th><th>Analysis</th></tr>"; // add Analysis column

        // map the nutrients correctly
        $linkedDailyMealTotals = linkValues($dailyMealTotals[$date], $conn);

        foreach ($userValues as $value) {
            $nutrient_name = $value['nutrient_name'];
            echo "<tr>";
            echo "<td>" . $nutrient_name . "</td>";
            echo "<td>" . $value['ac_amount'] . " " . $value['measurement_name'] . "</td>";
            // check if nutrient is available in the daily meal totals
            if (array_key_exists($nutrient_name, $linkedDailyMealTotals)) {
                echo "<td>" . $linkedDailyMealTotals[$nutrient_name] . "</td>";
            } else {
                echo "<td>0</td>"; // Or any value to indicate no consumption of this nutrient in this day
            }

            // add analysis column
            if (isset($analysisResults[$date][$nutrient_name])) {
                switch ($analysisResults[$date][$nutrient_name]) {
                    case "high":
                        echo "<td><i class='fa-solid fa-arrow-up' style='color: #ffd104;'></i></td>";
                        break;
                    case "low":
                        echo "<td><i class='fa-solid fa-arrow-down' style='color: #ffd104;'></i></td>";
                        break;
                    case "good":
                        echo "<td><i class='fa-solid fa-check' style='color: #00b528;'></i></td>";
                        break;
                }
            } else {
                echo "<td></td>";
            }

            echo "</tr>";


        }
        echo "</table><br>";
        echo "</div>";
    }
}

include_once '../../engine/footer.php';

?>

<script>
    // Convert chartData PHP array to JavaScript array
    var chartData = <?php echo json_encode($chartData); ?>;
    var chartOptions = {
        type: 'doughnut', 
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                }
            },
            cutout: '50%', // Set this option for a ring-like chart
        }
    };

    // Create a new chart for each day's data
    chartData.forEach(function(dayData) {
        var ctx = document.getElementById('analysis-chart-' + dayData.date);
        var chart = new Chart(ctx, {
            ...chartOptions,
            data: {
                labels: dayData.labels,
                datasets: [{
                    data: dayData.data,
                    backgroundColor: dayData.colors
                }]
            }
        });
    });
</script>

<?php
/*
    content/meals/view_meal.php    VERSION 1.3
    Allows a user to view a meal, takes an ID from POST.
    Reviewed 7/12/2023
*/

session_start();
require_once '../../engine/dbConnect.php';
include '../../engine/header.php';



// Sanitize the GET parameter
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Fetch data from fetch_meal_details.php
require_once '../../engine/processes/fetch_meal_details.php';
$mealDetails = getMealDetails($id, $conn);
$ingredients = explode(', ', $mealDetails['totals']['ingredient_list']);

?>

<!-- Page Starts -->

<link rel="stylesheet" href="../../css/charts.css?v=1.02">

<div class="data-section">
    <h2>Meal Details</h2>
    <p><?php echo htmlspecialchars($mealDetails['meal']['meal_type']) . " by " . htmlspecialchars($mealDetails['meal']['username']); ?></p>
    <p><?php echo htmlspecialchars($mealDetails['notes']); ?></p>
</div>

<div class="data-section">
    <h2>Foods</h2>
    <ul>
        <?php foreach ($mealDetails['foods'] as $food) : ?>
            <li><?php echo htmlspecialchars($food['servings']) . 'x ' . htmlspecialchars($food['name']); ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="data-section">
    <h2>Ingredients</h2>
    <ul>
        <?php foreach ($ingredients as $ingredient) : ?>
            <li><?php echo htmlspecialchars($ingredient); ?></li>
        <?php endforeach; ?>
    </ul>
</div>


<div class="data-section">
    <h2>Macronutrients</h2>
    <div class="data-section-two-col">
        <div>
            <table class="table-custom">
                <tr>
                    <th>Macronutrient</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <td>Calories</td>
                    <td><?php echo htmlspecialchars($mealDetails['totals']['total_calories']); ?></td>
                </tr>
                <tr>
                    <td>Dietary Fibres</td>
                    <td><?php echo htmlspecialchars($mealDetails['totals']['total_dietary_fibres']); ?></td>
                </tr>
                <tr>
                    <td>Cholesterol</td>
                    <td><?php echo htmlspecialchars($mealDetails['totals']['total_cholesterol']); ?></td>
                </tr>
            </table>
        </div>
        <div>
            <table class="table-custom">
                <tr>
                    <th>Macronutrient</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <td>Proteins</td>
                    <td><?php echo htmlspecialchars($mealDetails['totals']['total_proteins']); ?></td>
                </tr>
                <tr>
                    <td>Sugars</td>
                    <td><?php echo htmlspecialchars($mealDetails['totals']['total_sugars']); ?></td>
                </tr>
                <tr>
                    <td>Sodium</td>
                    <td><?php echo htmlspecialchars($mealDetails['totals']['total_sodium']); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="data-section">
    <h2>Fats</h2>
    <table class="table-custom">
        <tr>
            <th>Total Fats</th>
            <th>Saturated Fats</th>
            <th>Trans Fats</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($mealDetails['totals']['total_fat']); ?></td>
            <td><?php echo htmlspecialchars($mealDetails['totals']['total_saturated_fats']); ?></td>
            <td><?php echo htmlspecialchars($mealDetails['totals']['total_trans_fats']); ?></td>
        </tr>
    </table>
</div>

<div class="data-section">
    <h2>Micronutrients</h2>
    <div class="data-section-two-col">
        <div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <?php
            function generateNutrientColor($index) {
                $hue = ($index * 15) % 360; // Adjust the hue increment to control the color gradient
                return 'hsl(' . $hue . ', 70%, 50%)';
            }

            $micronutrients = [];
            $colorIndex = 0;

            foreach ($mealDetails['totals'] as $key => $value) {
                if ($value > 0 && strpos($key, 'total_') === 0 && !in_array($key, ['total_proteins', 'total_sugars',
                        'total_sodium', 'total_calories', 'total_dietary_fibres', 'total_cholesterol', 'total_fat',
                        'total_saturated_fats', 'total_trans_fats'])) {
                    $nutrientName = str_replace('_', ' ', ucfirst(str_replace('total_', '', $key)));

                    // Check if value is greater than 1000
                    if ($value > 1000) {
                        $value = $value / 1000; // Convert to milligrams
                        $value = round($value, 2); // Optional: round the value to 2 decimal places
                        $value .= " mg"; // Append "mg"
                    } else {
                        $value .= " mcg"; // Append "mcg"
                    }

                    $micronutrients[] = [
                        'label' => $nutrientName,
                        'value' => $value,
                        'color' => generateNutrientColor($colorIndex), // calling the PHP function
                    ];
                    $colorIndex++;
                }
            }

            // If the micronutrients array is empty, output a <p> element
            if (empty($micronutrients)) {
                echo '<p>No micronutrients provided for this food...</p>';
            } else {
                // If the array is not empty, output the table
                echo '<table class="table-custom">';
                echo '<tr>';
                echo '<th>    </th>';
                echo '<th>Micronutrient</th>';
                echo '<th>Amount</th>';
                echo '</tr>';

                foreach ($micronutrients as $nutrient) {
                    echo '<tr>';
                    echo '<td style="background-color: ' . $nutrient['color'] . '"></td>';
                    echo '<td>' . htmlspecialchars($nutrient['label']) . '</td>';
                    echo '<td>' . htmlspecialchars($nutrient['value']) . '</td>';
                    echo '</tr>';
                }

                echo '</table>';
            }
            ?>
        </div>
        <div>
            <?php if (!empty($micronutrients)) : ?>
                <div class="chart-container" style="height:250px; width:250px;">
                    <canvas id="micronutrient-chart"></canvas>
                </div>
                <script>
                    // Create the chart
                    let ctx = document.getElementById('micronutrient-chart').getContext('2d');

                    // Get the data from PHP
                    let labels = <?php echo json_encode(array_column($micronutrients, 'label')); ?>;
                    let data = <?php echo json_encode(array_map('floatval', array_column($micronutrients, 'value'))); ?>;
                    let colors = <?php echo json_encode(array_column($micronutrients, 'color')); ?>;


                    let chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: colors,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            var index = context.dataIndex;
                                            var dataset = context.dataset.data;
                                            var value = dataset[index];
                                            var total = dataset.reduce(function(previousValue, currentValue, currentIndex, array) {
                                                return previousValue + currentValue;
                                            });
                                            var percentage = Math.floor(((value/total) * 100)+0.5);
                                            return context.chart.data.labels[index] + ': ' + percentage + "%";
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>


<a href="/content/meals/manage_meal.php?id=<?php echo htmlspecialchars($id); ?>" class="button-link">Manage Meal</a>
<a href="../../engine/processes/delete_meal.php?id=<?php echo $mealDetails['meal']['id']; ?>" class="button-link red" onclick="return confirm('Are you sure you want to delete this meal?')">Delete</a>

<?php include '../../engine/footer.php'; ?>

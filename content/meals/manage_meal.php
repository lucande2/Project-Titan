<?php
/*
    content/meals/manage_meal.php    VERSION 1.3
    Allows a user to modify a meal record.  Grabs an ID from the POST.
    Reviewed 7/12/2023
*/

include('../../engine/header.php');
require_once('../../engine/dbConnect.php');
require_once('../../engine/processes/fetch_meal_details.php');

$mealId = $_GET['id']; // retrieve meal id from query string
$mealDetails = getMealDetails($mealId, $conn);
?>

<!-- Page Starts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<h2>Manage Meal</h2>

<form action="../../engine/processes/manage_meal_process.php" method="post" id="meal_form">
    <!-- Hidden input for the meal ID -->
    <input type="hidden" id="meal_id" name="id" value="<?php echo $mealId; ?>" />

    <div class="form-group">
        <label for="meal_date">Date of Meal:</label>
        <input type="date" id="meal_date" name="meal_date" class="form-control" value="<?php echo $mealDetails['meal']['meal_date']; ?>">
    </div>
    <div class="form-group">
        <label for="meal_type">Meal Type:</label>
        <select id="meal_type" name="meal_type" class="form-control">
            <option value="Breakfast" <?php if($mealDetails['meal']['meal_type'] == 'Breakfast') echo 'selected'; ?>>Breakfast</option>
            <option value="Lunch" <?php if($mealDetails['meal']['meal_type'] == 'Lunch') echo 'selected'; ?>>Lunch</option>
            <option value="Dinner" <?php if($mealDetails['meal']['meal_type'] == 'Dinner') echo 'selected'; ?>>Dinner</option>
            <option value="Snack" <?php if($mealDetails['meal']['meal_type'] == 'Snack') echo 'selected'; ?>>Snack</option>
        </select>
    </div>
    <div class="form-group">
        <label for="meal_notes">Notes:</label>
        <textarea id="meal_notes" name="meal_notes" class="form-control"><?php echo $mealDetails['notes']; ?></textarea>
    </div>

    <h3>Food List</h3>
    <table id="food_list" class="table-custom">
        <tr>
            <th>Servings</th>
            <th>Item</th>
            <th>Action</th>
        </tr>
        <?php
        foreach($mealDetails['foods'] as $food){
            echo "<tr data-id='{$food['id']}'><td>{$food['servings']} servings</td><td>{$food['brand']}: {$food['name']} ({$food['serving_size']} {$food['serving_measurement']})</td><td><button type='button' class='remove btn btn-danger btn-sm'>Remove</button> <button type='button' class='decrement btn btn-secondary btn-sm'>-</button> <button type='button' class='increment btn btn-secondary btn-sm'>+</button></td></tr>";
        }
        ?>

    </table>

    <!-- Hidden input for the food list -->
    <input type="hidden" id="food_list_data" name="food_list_data" />

    <div class="form-inline">
        <div class="form-group">
            <label for="food_search">Search Food:</label>
            <input type="text" id="food_search" name="food_search" class="form-control" style="width: 85%;">
        </div>
        <button type="button" id="search_button" class="btn btn-primary" style="margin-left: 10px;">Search</button>
    </div>
    <div id="search_results"></div>

    <input type="submit" value="Submit" class="btn btn-primary" style="margin-top: 10px;">
</form>

<script>
    // Dynamic search as user types in the search field
    $("#food_search").keyup(function() {
        var query = $(this).val();
        if (query != "") {
            $.ajax({
                url: '../../engine/processes/search_food_process.php',
                method: 'POST',
                data: { query: query },
                success: function(data) {
                    $('#search_results').html(data);
                }
            });
        } else {
            $('#search_results').html('');
        }
    });

    // Functionality to select a food item
    $(document).on('click', '.select', function(e) {
        e.preventDefault();

        var foodId = $(this).closest('tr').data('id');
        var food = $(this).closest('tr').find('td:first').text();
        var brand = $(this).closest('tr').find('td:nth-child(2)').text();
        var serving = $(this).closest('tr').find('td:nth-child(3)').text();

        var foodItem = `${brand}: ${food} (${serving})`;

        // Check if food item already exists in the table
        var found = false;
        $('#food_list tr').each(function() {
            var currentFoodId = $(this).data('id');
            if (currentFoodId == foodId) {
                var currentServingCount = parseInt($(this).find('td:first').text().split(' ')[0]);
                // Add the serving count together
                currentServingCount += 1;
                $(this).find('td:first').text(currentServingCount + (currentServingCount > 1 ? " servings" : " serving"));
                found = true;
            }
        });

        if (!found) {
            // Append the food item to the table
            $('#food_list').append('<tr data-id="' + foodId + '"><td>1 serving</td><td>' + foodItem + '</td><td><button type="button" class="remove btn btn-danger btn-sm">Remove</button> <button type="button" class="decrement btn btn-secondary btn-sm">-</button> <button type="button" class="increment btn btn-secondary btn-sm">+</button></td></tr>');
        }
    });

    // Remove a serving from the food list or the item itself if servings count reaches zero
    $('#food_list').on('click', '.remove', function() {
        var currentServingCount = parseInt($(this).closest('tr').find('td:first').text().split(' ')[0]);
        if (currentServingCount > 1) {
            $(this).closest('tr').find('td:first').text((currentServingCount - 1) + (currentServingCount - 1 > 1 ? " servings" : " serving"));
        } else {
            $(this).closest('tr').remove();
        }
    });

    // Increment serving count
    $('#food_list').on('click', '.increment', function() {
        var currentServingCount = parseInt($(this).closest('tr').find('td:first').text().split(' ')[0]);
        currentServingCount += 1;
        $(this).closest('tr').find('td:first').text(currentServingCount + (currentServingCount > 1 ? " servings" : " serving"));
    });

    // Decrement serving count
    $('#food_list').on('click', '.decrement', function() {
        var currentServingCount = parseInt($(this).closest('tr').find('td:first').text().split(' ')[0]);
        if (currentServingCount > 1) {
            $(this).closest('tr').find('td:first').text((currentServingCount - 1) + (currentServingCount - 1 > 1 ? " servings" : " serving"));
        } else {
            $(this).closest('tr').remove();
        }
    });

    // Remove a food item from the food list
    $('#food_list').on('click', '.remove', function() {
        $(this).closest('tr').remove();
    });

    $('form').on('submit', function(e) {
        var foodList = [];
        $('#food_list tr').each(function() {
            var foodId = $(this).data('id');
            var servings = $(this).find('td:first').text();
            if (foodId) {  // exclude table header
                foodList.push({
                    food_id: foodId,
                    servings: servings,
                });
            }
        });

        $('#food_list_data').val(JSON.stringify(foodList));
    });
</script>


<?php include('../../engine/footer.php'); ?>

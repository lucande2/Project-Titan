<?php include('../../engine/header.php');
/*
    content/meals/add_meal.php    VERSION 1.3
    Allows a user to add a meal record.
    Reviewed 7/12/2023
*/
?>

<?php require_once('../../engine/dbConnect.php');
?>

<!-- Page Starts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<h2>Add a Meal</h2>
<p>Add a meal to your profile here.  The meals you add here are used for analysis purposes.  The meal type
in the future will order the meals on your profile.  The notes field allow you to add notes for future reference.</p>
<br>
<p>To add food to your meal, search for food items in the search box and add a serving.  You can add more than
one serving by clicking on the "select" button more times.  You can also use the '+' or '-' buttons for that
food item.</p>
<br>
<p>When you are done making changes, submit the meal.</p>
<form action="../../engine/processes/add_meal_process.php" method="post" id="meal_form">
    <div class="form-group">
        <label for="meal_date" style="color:#fff;">Date of Meal:</label>
        <input type="date" id="meal_date" name="meal_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="form-group">
        <label for="meal_type" style="color:#fff;">Meal Type:</label>
        <select id="meal_type" name="meal_type" class="form-control">
            <option value="Breakfast">Breakfast</option>
            <option value="Lunch">Lunch</option>
            <option value="Dinner">Dinner</option>
            <option value="Snack">Snack</option>
        </select>
    </div>
    <div class="form-group">
        <label for="meal_notes" style="color:#fff;">Notes:</label>
        <textarea id="meal_notes" name="meal_notes" class="form-control"></textarea>
    </div>

    <h3>Food List</h3>
    <table id="food_list" class="table-custom">
        <tr>
            <th>Servings</th>
            <th>Item</th>
            <th>Action</th>
        </tr>
    </table>

    <!-- Hidden input for the food list -->
    <input type="hidden" id="food_list_data" name="food_list_data" />

    <div class="form-inline">
        <div class="form-group">
            <label for="food_search" style="color:#fff;">Search Food:</label>
            <input type="text" id="food_search" name="food_search" class="form-control" style="width: 85%;">
        </div>
        <button type="button" id="search_button" class="button-link" style="margin-left: 10px;">Search</button>
        <button type="button" id="clear_button" class="button-link" style="margin-left: 10px;">Clear</button>
    </div>
    <div id="search_results"></div>

    <input type="submit" value="Submit" class="button-link" style="margin-top: 10px;">
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

        var foodItem = `${brand} ${food}`;

        // Check if food item already exists in the table
        var found = false;
        $('#food_list tr').each(function() {
            var currentFoodId = $(this).data('id');
            if (currentFoodId == foodId) {
                var currentServingQuantity = parseInt($(this).find('td:first').text());
                // Add the serving quantities together
                currentServingQuantity += 1;
                $(this).find('td:first').text(currentServingQuantity + ' servings');
                found = true;
            }
        });

        if (!found) {
            // Append the food item to the table
            $('#food_list').append('<tr data-id="' + foodId + '"><td>1 serving</td><td>' + foodItem + ' (' + serving + ')' +
                '</td><td><button type="button" class="remove btn btn-danger btn-sm">Remove</button> <button type="button" class="decrement btn ' +
                'btn-secondary btn-sm">-</button> <button type="button" class="increment btn btn-secondary btn-sm">+</button></td></tr>');
        }
    });

    // Remove food from the list
    $('#food_list').on('click', '.remove', function() {
        $(this).closest('tr').remove();
    });

    // Decrement serving size
    $('#food_list').on('click', '.decrement', function() {
        var currentServingText = $(this).closest('tr').find('td:first').text();
        var currentServingQuantity = parseInt(currentServingText);

        if (currentServingQuantity > 2) {
            $(this).closest('tr').find('td:first').text((currentServingQuantity - 1) + ' servings');
        } else if (currentServingQuantity == 2) {
            $(this).closest('tr').find('td:first').text((currentServingQuantity - 1) + ' serving');
        } else {
            $(this).closest('tr').remove();
        }
    });

    // Increment serving size
    $('#food_list').on('click', '.increment', function() {
        var currentServingQuantity = parseInt($(this).closest('tr').find('td:first').text());
        $(this).closest('tr').find('td:first').text((currentServingQuantity + 1) + ' servings');
    });

    $("#meal_form").submit(function() {
        var foodData = [];

        $('#food_list tr').each(function() {
            if ($(this).data('id')) {
                var servingsText = $(this).find('td:first').text();
                var servingsParts = servingsText.split(' ');
                var quantity = servingsParts[0];
                var unit = servingsParts[1] || '';

                foodData.push({
                    food_id: $(this).data('id'),
                    servings: {
                        quantity: quantity,
                        unit: unit
                    }
                });
            }
        });

        $('#food_list_data').val(JSON.stringify(foodData));
    });

    document.getElementById('clear_button').addEventListener('click', function() {
        document.getElementById('food_search').value = '';
        document.getElementById('search_results').innerHTML = '';
    });

</script>

<script src="/engine/javascript/add_meal_v1.0.js"></script>

<?php include('../../engine/footer.php'); ?>

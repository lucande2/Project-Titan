$("#meal_form").submit(function(e) {
    // Check date
    var date = $("#meal_date").val();
    if (!date) {
        alert('Please enter a date for the meal.');
        e.preventDefault();
        return;
    }

    // Check food list
    var foodCount = $('#food_list tr[data-id]').length;
    if (foodCount === 0) {
        alert('Please add at least one food item to the meal.');
        e.preventDefault();
        return;
    }

    // Process food data for submission as before...
    var foodData = [];

    $('#food_list tr[data-id]').each(function() {
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
    });

    $('#food_list_data').val(JSON.stringify(foodData));
});

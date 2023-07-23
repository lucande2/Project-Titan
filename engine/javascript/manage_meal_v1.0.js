$('form').on('submit', function(e) {
    var mealDate = $("#meal_date").val();
    var rowCount = $('#food_list tr').length;

    if (!mealDate || rowCount < 2) { // including table header
        alert("Please ensure there is a date and at least one food item in the table.");
        e.preventDefault();
        return;
    }

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

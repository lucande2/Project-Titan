$(document).ready(function() {

    var food_id = $('input[name="id"]').val();

    $('#add-nutrient').click(function() {
        let nutrientHtml = `
    <div class="nutrient-group row">
        <div class="col-3">
            <select name="nutrient_names[]" class="nutrient form-control">
                    <option value=" "> </option>
                    <option value="Calcium">Calcium</option>
                    <option value="Chloride">Chloride</option>
                    <option value="Chromium">Chromium</option> 
                    <option value="Copper">Copper</option>
                    <option value="Fluoride">Fluoride</option>
                    <option value="Iodine">Iodine</option>
                    <option value="Iron">Iron</option>
                    <option value="Magnesium">Magnesium</option>
                    <option value="Manganese">Manganese</option>
                    <option value="Molybdenum">Molybdenum</option>
                    <option value="Phosphorus">Phosphorus</option>
                    <option value="Potassium">Potassium</option>
                    <option value="Selenium">Selenium</option>
                    <option value="Sulfur">Sulfur</option>                        
                    <option value="Vitamin A">Vitamin A</option>
                    <option value="Vitamin B1">Vitamin B1 (Thiamin)</option>
                    <option value="Vitamin B2">Vitamin B2 (Riboflavin)</option>
                    <option value="Vitamin B3">Vitamin B3 (Niacin)</option>
                    <option value="Vitamin B5">Vitamin B5 (Pantothenic acid)</option>
                    <option value="Vitamin B6">Vitamin B6 (Pyridoxine)</option>
                    <option value="Vitamin B7">Vitamin B7 (Biotin)</option>
                    <option value="Vitamin B9">Vitamin B9 (Folate)</option>
                    <option value="Vitamin B12">Vitamin B12 (Cobalamin)</option>
                    <option value="Vitamin C">Vitamin C</option>
                    <option value="Vitamin D">Vitamin D</option>
                    <option value="Vitamin E">Vitamin E</option>
                    <option value="Vitamin K">Vitamin K</option>
                    <option value="Zinc">Zinc</option>  
            </select>
        </div>
        <div class="col-3">
            <input type="number" name="nutrient_amounts[]" class="form-control">
        </div>
        <div class="col-3">
            <select name="nutrient_units[]" class="form-control">
                    <option value="mg">mg</option>
                    <option value="mcg">mcg</option>
                    <option value="%">% daily value</option>
            </select>
        </div>
        <div class="col-2">
            <button type="button" class="remove-nutrient btn btn-danger">X</button>
        </div>
    </div>`;

        $('#nutrients').append(nutrientHtml);
    });

    $("#add-tag").click(function () {
        $("#tags").append(`
        <div class="row tag-group">
            <div class="col-9">
                <input type="text" class="tag form-control col" name="tags[]">
            </div>
            <div class="col-3">
                <button type="button" class="remove-tag btn btn-danger">X</button>
            </div>
        </div>`);
    });

    $("#add-ingredient").click(function () {
        $("#ingredients").append(`
        <div class="ingredient-group row">
            <div class="col-9">
                <input type="text" class="ingredient form-control" name="ingredients[]">
            </div>
            <div class="col-3">
                <button type="button" class="remove-ingredient btn btn-danger">X</button>
            </div>
        </div>`);
    });

    $(document).on('click', '.remove-ingredient', function() {
        $(this).closest('.ingredient-group').remove();
    });

    $(document).on('click', '.remove-nutrient', function() {
        $(this).closest('.nutrient-group').remove();
    });

    $(document).on('click', '.remove-tag', function() {
        $(this).closest('.tag-group').remove();
    });

    // Ingredient, when tab is pressed
    $(document).on('keydown', '#ingredients .ingredient:last', function(e) {
        if (e.which == 9) { // Check if 'Tab' was pressed
            e.preventDefault(); // Prevent the default action (tabbing to the next field)
            $("#add-ingredient").click(); // Simulate a click on the add-ingredient button
            $('#ingredients .ingredient:last').focus(); // Focus on the newly created field
        }
    });

    // Tags, when tab is pressed
    $(document).on('keydown', '#tags .tag:last', function(e) {
        if (e.which == 9) { // Check if 'Tab' was pressed
            e.preventDefault(); // Prevent the default action (tabbing to the next field)
            $("#add-tag").click(); // Simulate a click on the add-tag button
            $('#tags .tag:last').focus(); // Focus on the newly created field
        }
    });

    // Nutrient, when tab is pressed
    $(document).on('keydown', '#nutrients .nutrient-group:last .form-control:last', function(e) {
        if (e.which == 9) { // Check if 'Tab' was pressed
            e.preventDefault(); // Prevent the default action (tabbing to the next field)
            $("#add-nutrient").click(); // Simulate a click on the add-nutrient button
            $('#nutrients .nutrient-group:last .form-control:first').focus(); // Focus on the newly created field
        }
    });

    function addNutrientField(name = '', amount = '', measurement = '') {
        console.log("Name:", name);
        var newNutrient = $('<div class="nutrient-group row"><input type="text" class="nutrient form-control col" name="nutrients[]" value="' + name + '"><input type="text" class="nutrient-amount form-control col" name="nutrient-amounts[]" value="' + amount + '"><input type="text" class="nutrient-measurement form-control col" name="nutrient-measurements[]" value="' + measurement + '"><div class="col-auto"><button type="button" class="remove-nutrient btn btn-danger">X</button></div></div>');
        $('#nutrients').append(newNutrient);
    }

    // Adjusted addIngredientField function
    function addIngredientField(value = '') {
        var newIngredient = $('<div class="ingredient-group row"><div class="col-9"><input type="text" class="ingredient form-control" name="ingredients[]" value="' + value + '"></div><div class="col-3"><button type="button" class="remove-ingredient btn btn-danger">X</button></div></div>');
        $('#ingredients').append(newIngredient);
    }

    // Adjusted addTagField function
    function addTagField(value = '') {
        var newTag = $('<div class="tag-group row"><div class="col-9"><input type="text" class="tag form-control" name="tags[]" value="' + value + '"></div><div class="col-3"><button type="button" class="remove-tag btn btn-danger">X</button></div></div>');
        $('#tags').append(newTag);
    }
});

$('form').submit(function(e) {
    // Check Name
    if ($('#name').val() === "") {
        alert("Please enter a name for the food.");
        e.preventDefault();
        return;
    }

    // Check Brand
    if ($('#brand').val() === "") {
        alert("Please enter a brand for the food.");
        e.preventDefault();
        return;
    }

    // Check Serving Size
    if (!$('#serving_size').val() || !Number.isInteger(parseFloat($('#serving_size').val()))) {
        alert("Please enter a valid integer serving size.");
        e.preventDefault();
        return;
    }

    // Check Calories
    if (!$('#calories').val() || !Number.isInteger(parseFloat($('#calories').val()))) {
        alert("Please enter a valid integer for calories.");
        e.preventDefault();
        return;
    }

    // Check Total Fats
    if (!$('#total_fats').val() || !Number.isInteger(parseFloat($('#total_fats').val()))) {
        alert("Please enter a valid integer for total fats.");
        e.preventDefault();
        return;
    }

    // Check Saturated Fats
    if (!$('#saturated_fats').val() || !Number.isInteger(parseFloat($('#saturated_fats').val()))) {
        alert("Please enter a valid integer for saturated fats.");
        e.preventDefault();
        return;
    }

    // Check Trans Fats
    if (!$('#trans_fats').val() || !Number.isInteger(parseFloat($('#trans_fats').val()))) {
        alert("Please enter a valid integer for trans fats.");
        e.preventDefault();
        return;
    }

    // Check Cholesterol
    if (!$('#cholesterol').val() || !Number.isInteger(parseFloat($('#cholesterol').val()))) {
        alert("Please enter a valid integer for cholesterol.");
        e.preventDefault();
        return;
    }

    // Check Dietary Fibres
    if (!$('#dietary_fibres').val() || !Number.isInteger(parseFloat($('#dietary_fibres').val()))) {
        alert("Please enter a valid integer for dietary fibres.");
        e.preventDefault();
        return;
    }

    // Check Proteins
    if (!$('#proteins').val() || !Number.isInteger(parseFloat($('#proteins').val()))) {
        alert("Please enter a valid integer for proteins.");
        e.preventDefault();
        return;
    }

    // Check Sodium
    if (!$('#sodium').val() || !Number.isInteger(parseFloat($('#sodium').val()))) {
        alert("Please enter a valid integer for sodium.");
        e.preventDefault();
        return;
    }

    // Check Sugars
    if (!$('#total_sugars').val() || !Number.isInteger(parseFloat($('#total_sugars').val()))) {
        alert("Please enter a valid integer for total sugars.");
        e.preventDefault();
        return;
    }

    // Check Nutrients
    let isValid = true;
    $('.nutrient-group').each(function() {
        let nutrientName = $(this).find('select[name="nutrient_names[]"]').val();
        let nutrientAmount = $(this).find('input[name="nutrient_amounts[]"]').val();

        if (nutrientName === "" || nutrientAmount === "" || !Number.isInteger(parseFloat(nutrientAmount))) {
            isValid = false;
            return false; // break the loop
        }
    });

    if (!isValid) {
        alert("Each added nutrient must have a name and a valid integer amount.");
        e.preventDefault();
        return;
    }

    // All checks passed, form can be submitted
});
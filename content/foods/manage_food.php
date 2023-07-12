<?php
/*
    content/foods/manage_food.php    VERSION 1.3
    Make alterations to a food, grabbing the ID from POST.
    Reviewed 7/12/2023
*/

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Is a user logged in?
if(!isset($_SESSION['username'])) {
    // User not logged in. Redirect them back to the login.php page.
    header('Location: login.php');
    exit;
}

// include your function or connection setup here
include '/usr/share/nginx/html_project/engine/dbConnect.php';
include '/usr/share/nginx/html_project/engine/processes/fetch_food_details.php';
include '/usr/share/nginx/html_project/engine/processes/manage_food_process.php';

// check if id is set in the URL
if(isset($_GET['id'])) {
    // Sanitize the input to make sure it is a number
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch food data from the database
    $food = getFoodByID($id, $conn);

    // Fetch related food details like ingredients, nutrients and tags
    $ingredients = getIngredientsByFoodID($id, $conn);
    $nutrients = getNutrientsByFoodID($id, $conn);
    $tags = getTagsByFoodID($id, $conn);

    // Fetch fats and additional info
    $fats = getFatsByFoodID($id, $conn);
    $additional_info = getAdditionalInfoByFoodID($id, $conn);



} else {
    // Redirect to a different page if id is not provided
    header('Location: /usr/share/nginx/html_project/admin/manage_foods.php');
    exit;
}
?>

<!-- Page Starts -->
<?php include '../../engine/header.php'; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <h1>Edit Food</h1>
    <form action="/engine/processes/manage_food_process.php" method="post">
        <input type="hidden" name="id" value="<?= $id; ?>" />
        <div class="row">
            <div class="col">
                <label for="name" style="color:#fff;">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= isset($food['name']) ? $food['name'] : ''; ?>">
            </div>
            <div class="col">
                <label for="brand" style="color:#fff;">Brand</label>
                <input type="text" id="brand" name="brand" class="form-control" value="<?= isset($food['brand']) ? $food['brand'] : ''; ?>">
            </div>
            <div class="col">
                <label for="food_group" style="color:#fff;">Food Group</label>
                <select id="food_group" name="food_group" class="form-control">
                    <option value="Grains" <?= (isset($food['food_group']) && $food['food_group'] == 'Grains') ? 'selected' : ''; ?>>Grains</option>
                    <option value="Fruits" <?= (isset($food['food_group']) && $food['food_group'] == 'Fruits') ? 'selected' : ''; ?>>Fruits</option>
                    <option value="Vegetables" <?= (isset($food['food_group']) && $food['food_group'] == 'Vegetables') ? 'selected' : ''; ?>>Vegetables</option>
                    <option value="Proteins" <?= (isset($food['food_group']) && $food['food_group'] == 'Proteins') ? 'selected' : ''; ?>>Proteins</option>
                    <option value="Dairy" <?= (isset($food['food_group']) && $food['food_group'] == 'Dairy') ? 'selected' : ''; ?>>Dairy</option>
                    <option value="Oils" <?= (isset($food['food_group']) && $food['food_group'] == 'Oils') ? 'selected' : ''; ?>>Oils</option>
                    <option value="Supplement" <?= (isset($food['food_group']) && $food['food_group'] == 'Supplement') ? 'selected' : ''; ?>>Supplement</option>
                    <option value="Unaligned" <?= (isset($food['food_group']) && $food['food_group'] == 'Unaligned') ? 'selected' : ''; ?>>Unaligned</option>
                </select>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <label for="serving_size" style="color:#fff;">Serving Size</label>
                <input type="text" id="serving_size" name="serving_size" class="form-control" value="<?= isset($food['serving_size']) ? $food['serving_size'] : ''; ?>">
            </div>
            <div class="col">
                <label for="serving_measurement" style="color:#fff;">Measurement Type</label>
                <select id="serving_measurement" name="serving_measurement" class="form-control">
                    <option value="Cups" <?= (isset($food['serving_measurement']) && $food['serving_measurement'] == 'Cups') ? 'selected' : ''; ?>>Cups</option>
                    <option value="Ounces" <?= (isset($food['serving_measurement']) && $food['serving_measurement'] == 'Ounces') ? 'selected' : ''; ?>>Ounces</option>
                    <option value="Pieces" <?= (isset($food['serving_measurement']) && $food['serving_measurement'] == 'Pieces') ? 'selected' : ''; ?>>Pieces</option>
                    <option value="Container" <?= (isset($food['serving_measurement']) && $food['serving_measurement'] == 'Container') ? 'selected' : ''; ?>>Container</option>
                    <option value="Package" <?= (isset($food['serving_measurement']) && $food['serving_measurement'] == 'Package') ? 'selected' : ''; ?>>Package</option>
                </select>
            </div>
            <div class="col">
                <label for="calories" style="color:#fff;">Calories</label>
                <input type="number" id="calories" name="calories" class="form-control" value="<?= isset($food['calories']) ? $food['calories'] : ''; ?>">
            </div>
        </div>  <br>

        <div class="row">
            <div class="col">
                <label for="ingredients" style="color:#fff;">Ingredients</label>
                <div id="ingredients">
                    <?php foreach($ingredients as $ingredient) { ?>
                        <div class="row ingredient-group">
                            <div class="col-9">
                                <input type="text" class="ingredient form-control" name="ingredients[]" value="<?php echo $ingredient; ?>">
                            </div>
                            <div class="col-3">
                                <button type="button" class="remove-ingredient btn btn-danger">X</button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" id="add-ingredient" class="btn btn-primary">Add Ingredient</button>

            </div>
        </div><br>

        <div class="row">
            <div class="col">
                <label for="total_fats" style="color:#fff;">Total Fats</label>
                <input type="number" id="total_fats" name="total_fats" class="form-control" value="<?= !empty($fats) ? $fats[0]['total'] : ''; ?>">
            </div>
            <div class="col">
                <label for="saturated_fats" style="color:#fff;">Saturated Fats</label>
                <input type="number" id="saturated_fats" name="saturated_fats" class="form-control" value="<?= !empty($fats) ? $fats[0]['saturated_fats'] : ''; ?>">
            </div>
            <div class="col">
                <label for="trans_fats" style="color:#fff;">Trans Fats</label>
                <input type="number" id="trans_fats" name="trans_fats" class="form-control" value="<?= !empty($fats) ? $fats[0]['trans_fats'] : ''; ?>">
            </div>
        </div>

        <div class="row">
            <div class="col">
                <label for="cholesterol" style="color:#fff;">Cholesterol</label>
                <input type="number" id="cholesterol" name="cholesterol" class="form-control" value="<?= !empty($additional_info) ? $additional_info[0]['cholesterol'] : ''; ?>">
            </div>
            <div class="col">
                <label for="dietary_fibres" style="color:#fff;">Dietary Fibres</label>
                <input type="number" id="dietary_fibres" name="dietary_fibres" class="form-control" value="<?= !empty($additional_info) ? $additional_info[0]['dietary_fibres'] : ''; ?>">
            </div>
            <div class="col">
                <label for="total_sugars" style="color:#fff;">Total Sugars</label>
                <input type="number" id="total_sugars" name="total_sugars" class="form-control" value="<?= !empty($additional_info) ? $additional_info[0]['total_sugars'] : ''; ?>">
            </div>
        </div>

        <div class="row">
            <div class="col">
                <label for="sodium" style="color:#fff;">Sodium</label>
                <input type="number" id="sodium" name="sodium" class="form-control" value="<?= !empty($additional_info) ? $additional_info[0]['sodium'] : ''; ?>">
            </div>
            <div class="col">
                <label for="proteins" style="color:#fff;">Proteins</label>
                <input type="number" id="proteins" name="proteins" class="form-control" value="<?= !empty($additional_info) ? $additional_info[0]['proteins'] : ''; ?>">
            </div>
            <div class="col">
                <p> </p>
            </div>
        </div><br>


        <div class="row">
            <div class="col">
                <label for="nutrients" style="color:#fff;">Nutrients</label>
                <div id="nutrients">
                    <?php foreach($nutrients as $nutrient) { ?>
                        <div class="row nutrient-group">
                            <div class="col-3">
                                <select name="nutrient_names[]" class="nutrient form-control">
                                    <option value=" " <?php if($nutrient['name'] == ' ') echo 'selected'; ?>></option>
                                    <option value="Calcium" <?php if($nutrient['name'] == 'Calcium') echo 'selected'; ?>>Calcium</option>
                                    <option value="Chloride" <?php if($nutrient['name'] == 'Chloride') echo 'selected'; ?>>Chloride</option>
                                    <option value="Chromium" <?php if($nutrient['name'] == 'Chromium') echo 'selected'; ?>>Chromium</option>
                                    <option value="Copper" <?php if($nutrient['name'] == 'Copper') echo 'selected'; ?>>Copper</option>
                                    <option value="Fluoride" <?php if($nutrient['name'] == 'Fluoride') echo 'selected'; ?>>Fluoride</option>
                                    <option value="Iodine" <?php if($nutrient['name'] == 'Iodine') echo 'selected'; ?>>Iodine</option>
                                    <option value="Iron" <?php if($nutrient['name'] == 'Iron') echo 'selected'; ?>>Iron</option>
                                    <option value="Magnesium" <?php if($nutrient['name'] == 'Magnesium') echo 'selected'; ?>>Magnesium</option>
                                    <option value="Manganese" <?php if($nutrient['name'] == 'Manganese') echo 'selected'; ?>>Manganese</option>
                                    <option value="Molybdenum" <?php if($nutrient['name'] == 'Molybdenum') echo 'selected'; ?>>Molybdenum</option>
                                    <option value="Phosphorous" <?php if($nutrient['name'] == 'Phosphorous') echo 'selected'; ?>>Phosphorous</option>
                                    <option value="Potassium" <?php if($nutrient['name'] == 'Potassium') echo 'selected'; ?>>Potassium</option>
                                    <option value="Selenium" <?php if($nutrient['name'] == 'Selenium') echo 'selected'; ?>>Selenium</option>
                                    <option value="Sulfur" <?php if($nutrient['name'] == 'Sulfur') echo 'selected'; ?>>Sulfur</option>
                                    <option value="Vitamin A" <?php if($nutrient['name'] == 'Vitamin A') echo 'selected'; ?>>Vitamin A</option>
                                    <option value="Vitamin B1" <?php if($nutrient['name'] == 'Vitamin B1') echo 'selected'; ?>>Vitamin B1 (Thiamin)</option>
                                    <option value="Vitamin B2" <?php if($nutrient['name'] == 'Vitamin B2') echo 'selected'; ?>>Vitamin B2 (Riboflavin)</option>
                                    <option value="Vitamin B3" <?php if($nutrient['name'] == 'Vitamin B3') echo 'selected'; ?>>Vitamin B3 (Niacin)</option>
                                    <option value="Vitamin B5" <?php if($nutrient['name'] == 'Vitamin B5') echo 'selected'; ?>>Vitamin B5 (Pantothenic acid)</option>
                                    <option value="Vitamin B6" <?php if($nutrient['name'] == 'Vitamin B6') echo 'selected'; ?>>Vitamin B6 (Pyridoxine)</option>
                                    <option value="Vitamin B7" <?php if($nutrient['name'] == 'Vitamin B7') echo 'selected'; ?>>Vitamin B7 (Biotin)</option>
                                    <option value="Vitamin B9" <?php if($nutrient['name'] == 'Vitamin B9') echo 'selected'; ?>>Vitamin B9 (Folate)</option>
                                    <option value="Vitamin B12" <?php if($nutrient['name'] == 'Vitamin B12') echo 'selected'; ?>>Vitamin B12 (Cobalamin)</option>
                                    <option value="Vitamin C" <?php if($nutrient['name'] == 'Vitamin C') echo 'selected'; ?>>Vitamin C</option>
                                    <option value="Vitamin D" <?php if($nutrient['name'] == 'Vitamin D') echo 'selected'; ?>>Vitamin D</option>
                                    <option value="Vitamin E" <?php if($nutrient['name'] == 'Vitamin E') echo 'selected'; ?>>Vitamin E</option>
                                    <option value="Vitamin K" <?php if($nutrient['name'] == 'Vitamin K') echo 'selected'; ?>>Vitamin K</option>
                                    <option value="Zinc" <?php if($nutrient['name'] == 'Zinc') echo 'selected'; ?>>Zinc</option>
                                </select>
                            </div>

                            <div class="col-3">
                                <input type="number" name="nutrient_amounts[]" class="form-control" value="<?php echo $nutrient['amount']; ?>">
                            </div>
                            <div class="col-3">
                                <select name="nutrient_units[]" class="form-control">
                                    <option value="mg" <?php if($nutrient['measurement'] == 'mg') echo 'selected'; ?>>mg</option>
                                    <option value="mcg" <?php if($nutrient['measurement'] == 'mcg') echo 'selected'; ?>>mcg</option>
                                    <option value="%" <?php if($nutrient['measurement'] == '%') echo 'selected'; ?>>% daily value</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <button type="button" class="remove-nutrient btn btn-danger">X</button>
                            </div>
                        </div>
                    <?php } ?>
                </div>


                <button type="button" id="add-nutrient" class="btn btn-primary">Add Nutrient</button>

            </div>
        </div><br>

        <div class="row">
            <div class="col">
                <label for="tags" style="color:#fff;">Tags</label>
                <div id="tags">
                    <?php foreach($tags as $tag) { ?>
                        <div class="row tag-group">
                            <div class="col-9">
                                <input type="text" class="tag form-control" name="tags[]" value="<?php echo $tag; ?>">
                            </div>
                            <div class="col-3">
                                <button type="button" class="remove-tag btn btn-danger">X</button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" id="add-tag" class="btn btn-primary">Add Tag</button>

            </div>
        </div><br>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <script src="/engine/javascript/manage_food_v1.js"></script>

<?php include '../../engine/footer.php'; ?>

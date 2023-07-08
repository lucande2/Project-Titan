<?php
// Start the session
session_start();

if (!isset($_SESSION['username'])) {
    // User not logged in. Redirect them back to the login.php page.
    header('Location: ../login.php');
    exit;
}
?>

<?php include '../../engine/header.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<h1>Add Food</h1>
<form action="../../engine/processes/add_food_process.php" method="post">
    <div class="row">
        <div class="col">
            <label for="name" style="color:#fff;">Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="col">
            <label for="brand" style="color:#fff;">Brand <span class="required">*</span></label>
            <input type="text" id="brand" name="brand" class="form-control" required>
        </div>
        <div class="col">
            <label for="food_group" style="color:#fff;">Food Group <span class="required">*</span></label>
            <select id="food_group" name="food_group" class="form-control" required>
                <option value="Grains">Grains</option>
                <option value="Fruits">Fruits</option>
                <option value="Vegetables">Vegetables</option>
                <option value="Proteins">Proteins</option>
                <option value="Dairy">Dairy</option>
                <option value="Oils">Oils</option>
                <option value="Supplement">Supplement</option>
                <option value="Unaligned">Unaligned</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="serving_size" style="color:#fff;">Serving Size <span class="required">*</span></label>
            <input type="text" id="serving_size" name="serving_size" class="form-control" required>
        </div>
        <div class="col">
            <label for="serving_measurement" style="color:#fff;">Measurement Type</label>
            <select id="serving_measurement" name="serving_measurement" class="form-control">
                <option value="Cups">Cups</option>
                <option value="Ounces">Ounces</option>
                <option value="Pieces">Pieces</option>
                <option value="Container">Container</option>
                <option value="Package">Package</option>
            </select>
        </div>
        <div class="col">
            <label for="calories" style="color:#fff;">Calories <span class="required">*</span></label>
            <input type="number" id="calories" name="calories" class="form-control" required>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="ingredients" style="color:#fff;">Ingredients</label>
            <div id="ingredients">
                <div class="ingredient-group">
                    <!-- <input type="text" class="ingredient form-control" name="ingredients[]">
                    <button type="button" class="remove-ingredient btn btn-danger">X</button> -->
                </div>
            </div>
            <button type="button" id="add-ingredient" class="btn btn-primary">Add Ingredient</button>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="total_fats" style="color:#fff;">Total Fats <span class="required">*</span></label>
            <input type="number" id="total_fats" name="total_fats" class="form-control" required>
        </div>
        <div class="col">
            <label for="saturated_fats" style="color:#fff;">Saturated Fats <span class="required">*</span></label>
            <input type="number" id="saturated_fats" name="saturated_fats" class="form-control" required>
        </div>
        <div class="col">
            <label for="trans_fats" style="color:#fff;">Trans Fats <span class="required">*</span></label>
            <input type="number" id="trans_fats" name="trans_fats" class="form-control" required>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="cholesterol" style="color:#fff;">Cholesterol <span class="required">*</span></label>
            <input type="number" id="cholesterol" name="cholesterol" class="form-control" required>
        </div>
        <div class="col">
            <label for="dietary_fibres" style="color:#fff;">Dietary Fibres <span class="required">*</span></label>
            <input type="number" id="dietary_fibres" name="dietary_fibres" class="form-control" required>
        </div>
        <div class="col">
            <label for="proteins" style="color:#fff;">Proteins <span class="required">*</span></label>
            <input type="number" id="proteins" name="proteins" class="form-control" required>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="sodium" style="color:#fff;">Sodium<span class="required">*</span></label>
            <input type="number" id="sodium" name="sodium" class="form-control">
        </div>
        <div class="col">
            <label for="total_sugars" style="color:#fff;">Total Sugars<span class="required">*</span></label>
            <input type="number" id="total_sugars" name="total_sugars" class="form-control">
        </div>
        <div class="col">
            <p> </p>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="nutrients" style="color:#fff;">Nutrients</label>
                <div id="nutrients">
                <!-- Initial nutrient removed -->
            </div>
            <button type="button" id="add-nutrient" class="btn btn-primary">Add Nutrient</button>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="tags" style="color:#fff;">Tags</label>
            <div id="tags">
                <div class="tag-group">
                    <!-- <input type="text" class="tag form-control" name="tags[]">
                    <button type="button" class="remove-tag btn btn-danger">X</button> -->
                </div>
            </div>
            <button type="button" id="add-tag" class="btn btn-primary">Add Tag</button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>

<script src="../../engine/javascript/add_food_v1.js"></script>

<?php include '../../engine/footer.php'; ?>

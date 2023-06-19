<?php
// Start the session
session_start();

if(!isset($_SESSION['username'])) {
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
            <label for="name">Name</label>
            <input type="text" id="name" name="name" class="form-control">
        </div>
        <div class="col">
            <label for="brand">Brand</label>
            <input type="text" id="brand" name="brand" class="form-control">
        </div>
        <div class="col">
            <label for="food_group">Food Group</label>
            <select id="food_group" name="food_group" class="form-control">
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
            <label for="serving_size">Serving Size</label>
            <input type="text" id="serving_size" name="serving_size" class="form-control">
        </div>
        <div class="col">
            <label for="serving_measurement">Measurement Type</label>
            <select id="serving_measurement" name="serving_measurement" class="form-control">
                <option value="Cups">Cups</option>
                <option value="Ounces">Ounces</option>
                <option value="Pieces">Pieces</option>
                <option value="Container">Container</option>
                <option value="Package">Package</option>
            </select>
        </div>
        <div class="col">
            <label for="calories">Calories</label>
            <input type="number" id="calories" name="calories" class="form-control">
        </div>
    </div>


    <div class="row">
        <div class="col">
            <label for="ingredients">Ingredients</label>
            <div id="ingredients">
                <div class="ingredient-group">
                    <!-- <input type="text" class="ingredient form-control" name="ingredients[]">
                    <button type="button" class="remove-ingredient btn btn-danger">X</button> -->
                </div>
            </div>
            <button type="button" id="add-ingredient">Add Ingredient</button>
        </div>
    </div>


    <div class="row">
        <div class="col">
            <label for="total_fats">Total Fats</label>
            <input type="number" id="total_fats" name="total_fats" class="form-control">
        </div>
        <div class="col">
            <label for="saturated_fats">Saturated Fats</label>
            <input type="number" id="saturated_fats" name="saturated_fats" class="form-control">
        </div>
        <div class="col">
            <label for="trans_fats">Trans Fats</label>
            <input type="number" id="trans_fats" name="trans_fats" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="cholesterol">Cholesterol</label>
            <input type="number" id="cholesterol" name="cholesterol" class="form-control">
        </div>
        <div class="col">
            <label for="dietary_fibres">Dietary Fibres</label>
            <input type="number" id="dietary_fibres" name="dietary_fibres" class="form-control">
        </div>
        <div class="col">
            <label for="proteins">Proteins</label>
            <input type="number" id="proteins" name="proteins" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="sodium">Sodium</label>
            <input type="number" id="sodium" name="sodium" class="form-control">
        </div>
        <div class="col">
            <label for="total_sugars">Total Sugars</label>
            <input type="number" id="total_sugars" name="total_sugars" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col">
            <label for="nutrients">Nutrients</label>
                <div id="nutrients">
                <!-- Initial nutrient removed -->
            </div>
            <button type="button" id="add-nutrient">Add Nutrient</button>
        </div>
    </div>



    <div class="row">
        <div class="col">
            <label for="tags">Tags</label>
            <div id="tags">
                <div class="tag-group">
                    <!-- <input type="text" class="tag form-control" name="tags[]">
                    <button type="button" class="remove-tag btn btn-danger">X</button> -->
                </div>
            </div>
            <button type="button" id="add-tag">Add Tag</button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>

<script src="../../engine/javascript/add_food.js"></script>

<?php include '../../engine/footer.php'; ?>

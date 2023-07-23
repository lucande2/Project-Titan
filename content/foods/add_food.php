<?php
/*
    content/foods/.php    VERSION 1.3
    THe page where food can be added to the database.
    Reviewed 7/12/2023
*/

// Start the session
session_start();

if (!isset($_SESSION['username'])) {
    // User not logged in. Redirect them back to the login.php page.
    header('Location: ../login.php');
    exit;
}
?>

<!-- Page Starts -->
<?php include '../../engine/header.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<h1>Add Food</h1>
<p>This page allows you to add a food item into the database.  Any user can then use this entry for their meal
tracking.  Thus, it is imperative that you use real and reliable information.  If you make a mistake, you can
edit this food item later.</p>
<br><br>
<h2>Food Information</h2>
<p>For the name, specify what it is "Ketchup", and for the brand, who makes the food "Heinz".  There is no
decimals or fractions permitted at this time, so round to the nearest whole number.  For the food group,
if unsure, use "unaligned".</p>
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
    <br><br><br>

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
                <option value="Package">Tablespoons</option>
                <option value="Package">Teaspoons</option>
            </select>
        </div>
        <div class="col">
            <label for="calories" style="color:#fff;">Calories <span class="required">*</span></label>
            <input type="number" id="calories" name="calories" class="form-control" required>
        </div>
    </div>
    <br><br><br><br>

    <div class="row">
        <div class="col">
            <h2 style="margin-left:7px;">Ingredients</h2>
            <p>These ingredients are typically labeled on the food item itself.  You can add a new text field
            by clicking "add ingredient" or by hitting the "TAB" key while in the last text field.</p>
            <label for="ingredients" style="color:#fff;"></label>
            <button type="button" id="add-ingredient" class="button-link" style="margin-bottom:15px;">Add Ingredient</button>
            <div id="ingredients">
                <div class="ingredient-group">
                    <!-- <input type="text" class="ingredient form-control" name="ingredients[]">
                    <button type="button" class="remove-ingredient btn btn-danger">X</button> -->
                </div>
            </div>
        </div>
    </div>
    <br><br><br><br>
    <h2 style="margin-left:7px;">Additional Information</h2>
    <p>These values are macronutrients found on the top part of the food label.  If the value is not present
    on the food label, type a "0" into the spot.  If there is a decimal, please round it up.</p>

    <div class="row">
        <div class="col">
            <label for="total_fats" style="color:#fff;">Total Fats <span class="required">*</span></label>
            <div class="input-group">
                <input type="number" id="total_fats" name="total_fats" class="form-control" required>
                <div class="input-group-append">
                    <span class="input-group-text">g</span>
                </div>
            </div>
        </div>
        <div class="col">
            <label for="saturated_fats" style="color:#fff;">Saturated Fats <span class="required">*</span></label>
            <div class="input-group">
                <input type="number" id="saturated_fats" name="saturated_fats" class="form-control" required>
                <div class="input-group-append">
                    <span class="input-group-text">g</span>
                </div>
            </div>
        </div>
        <div class="col">
            <label for="trans_fats" style="color:#fff;">Trans Fats <span class="required">*</span></label>
            <div class="input-group">
                <input type="number" id="trans_fats" name="trans_fats" class="form-control" required>
                <div class="input-group-append">
                    <span class="input-group-text">g</span>
                </div>
            </div>
        </div>
    </div>

    <br><br><br>

    <div class="row">
        <div class="col">
            <label for="cholesterol" style="color:#fff;">Cholesterol <span class="required">*</span></label>
            <div class="input-group">
                <input type="number" id="cholesterol" name="cholesterol" class="form-control" required>
                <div class="input-group-append">
                    <span class="input-group-text">g</span>
                </div>
            </div>
        </div>
        <div class="col">
            <label for="dietary_fibres" style="color:#fff;">Dietary Fibres <span class="required">*</span></label>
            <div class="input-group">
                <input type="number" id="dietary_fibres" name="dietary_fibres" class="form-control" required>
                <div class="input-group-append">
                    <span class="input-group-text">g</span>
                </div>
            </div>
        </div>
        <div class="col">
            <label for="proteins" style="color:#fff;">Proteins <span class="required">*</span></label>
            <div class="input-group">
                <input type="number" id="proteins" name="proteins" class="form-control" required>
                <div class="input-group-append">
                    <span class="input-group-text">g</span>
                </div>
            </div>
        </div>
    </div>

    <br><br><br>
    <div class="row">
        <div class="col">
            <label for="sodium" style="color:#fff;">Sodium<span class="required">*</span></label>
            <div class="input-group">
                <input type="number" id="sodium" name="sodium" class="form-control">
                <div class="input-group-append">
                    <span class="input-group-text">g</span>
                </div>
            </div>
        </div>
        <div class="col">
            <label for="total_sugars" style="color:#fff;">Total Sugars<span class="required">*</span></label>
            <div class="input-group">
                <input type="number" id="total_sugars" name="total_sugars" class="form-control">
                <div class="input-group-append">
                    <span class="input-group-text">g</span>
                </div>
            </div>
        </div>
        <div class="col">
            <p> </p>
        </div>
    </div>

    <br><br><br>

    <h2 style="margin-left:7px;">Nutrients</h2>

    <p>These micro-nutrients are typically specified on the bottom part of the food label.  You can add a
        new nutrient field by clicking "add nutrient" or by hitting the "TAB" key while in the last text
        field.</p>
    <div class="row">
        <div class="col">
            <button type="button" id="add-nutrient" class="button-link" style="margin-bottom:15px;">Add Nutrient</button>
            <label for="nutrients" style="color:#fff;"></label>
                <div id="nutrients">
                <!-- Initial nutrient removed -->
            </div>


        </div>
    </div>

    <br>


        <h2 style="margin-left:7px;">Tags</h2>
        <p>Tags are un-implemeneted right now, but will allow a user to search for foods by using the tags here.  They
            are not required. You can add a new tag field by clicking "add tag" or by hitting the "TAB" key while in
            the last text field.</p>

        <div class="row">
            <div class="col">
                <label for="tags" style="color:#fff;"></label>
                <button type="button" id="add-tag" class="button-link" style="margin-bottom:15px;">Add Tag</button>
                <div id="tags">
                    <div class="tag-group">
                        <!-- <input type="text" class="tag form-control" name="tags[]">
                        <button type="button" class="remove-tag btn btn-danger">X</button> -->
                    </div>
                </div>
            </div>
        </div>



    <br><br><p>When finished, submit the food into the database.  You can make changes later.</p>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

<script src="../../engine/javascript/add_food_v1.4.js"></script>

<?php include '../../engine/footer.php'; ?>

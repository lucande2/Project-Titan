<?php
include_once '../engine/header.php';
//session_start();

$user_id = $_SESSION['user-id'];

// Fetch user data
$query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>

<form action="../engine/processes/update_account_settings.php" method="post">
    <div class="data-section">
        <h2>Account Information</h2>
        <div class="row">
            <p class="text-dark">
                <label for="user_id" class="text-dark">User ID:</label>
                <input type="text" id="user_id" name="user_id" value="<?php echo $_SESSION['user-id']; ?>" readonly class="form-control bg-secondary text-dark">
            </p>
            <p class="text-dark">
                <label for="username" class="text-dark">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" class="form-control">
            </p>
        </div>
        <div class="row">
            <p class="text-dark">
                <label for="password" class="text-dark">New Password:</label>
                <input type="password" id="password" name="password" class="form-control">
            </p>
            <p class="text-dark">
                <label for="confirm_password" class="text-dark">Confirm</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
            </p>
        </div>
    </div>
    <div class="data-section">
        <h3>Profile Adjustments</h3>
        <p class="text-dark">These options will make adjustments to the analyser centre and your recommendations.
            You can still make custom adjustments to those values.</p>
        <div class="row">
            <p class="text-dark">
                <label for="health_focus" class="text-dark">Health Focus:</label>
                <select id="health_focus" name="health_focus" class="form-control">
                    <option value="healthy" <?php echo $user['health_focus'] == 'healthy' ? 'selected' : ''; ?>>Unaligned</option>
                    <option value="athlete" <?php echo $user['health_focus'] == 'athlete' ? 'selected' : ''; ?>>Athlete</option>
                    <option value="weight_loss" <?php echo $user['health_focus'] == 'weight_loss' ? 'selected' : ''; ?>>Weight loss</option>
                </select>
            </p>
            <p class="text-dark">
                <label for="dietary_restrictions" class="text-dark">Dietary Restrictions:</label>
                <select id="dietary_restrictions" name="dietary_restrictions" class="form-control">
                    <option value="Unrestricted" <?php echo $user['dietary_restrictions'] == 'Unrestricted' ? 'selected' : ''; ?>>Unrestricted</option>
                    <option value="Vegetarian" <?php echo $user['dietary_restrictions'] == 'Vegetarian' ? 'selected' : ''; ?>>Vegetarian</option>
                    <option value="Vegan" <?php echo $user['dietary_restrictions'] == 'Vegan' ? 'selected' : ''; ?>>Vegan</option>
                    <option value="Nut Allergy" <?php echo $user['dietary_restrictions'] == 'Nut Allergy' ? 'selected' : ''; ?>>Nut Allergy</option>
                    <option value="Lactose Intolerance" <?php echo $user['dietary_restrictions'] == 'Lactose Intolerance' ? 'selected' : ''; ?>>Lactose Intolerance</option>
                    <option value="Other" <?php echo $user['dietary_restrictions'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </p>
        </div>
        <div class="row">
            <p class="text-dark">
                <label for="biological_sex" class="text-dark">Biological Sex:</label>
                <select id="biological_sex" name="biological_sex" class="form-control">
                    <option value="male" <?php echo $user['biological_sex'] == 'male' ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo $user['biological_sex'] == 'female' ? 'selected' : ''; ?>>Female</option>
                </select>

            </p>
                <p class="text-dark">
                    <label for="site_theme" class="text-dark">Site Colour:</label>
                    <select id="site_theme" name="site_theme" class="form-control">
                        <option value="blueberry" <?php echo $user['site_theme'] == 'blueberry' ? 'selected' : ''; ?>>Blueberry</option>
                        <option value="pumpkin" <?php echo $user['site_theme'] == 'pumpkin' ? 'selected' : ''; ?>>Pumpkin Coffee</option>
                        <option value="pizza" <?php echo $user['site_theme'] == 'pizza' ? 'selected' : ''; ?>>Pizza</option>
                        <option value="lemon" <?php echo $user['site_theme'] == 'lemon' ? 'selected' : ''; ?>>Lemonade</option>

                        <!-- Add other themes options here -->
                    </select>
                </p>
        </div>
    </div>
    <div class="data-section">
        <div class="row">
            <h3> Privacy Settings:</h3>
        </div>
        <div class="row">
            <p class="text-dark">Check this box to hide your profile from others.</p>
            <input type="checkbox" id="profile_privacy" name="profile_privacy" <?php echo $user['profile_privacy'] ? 'checked' : ''; ?>>        </div>
        <br>
    </div>

    <div class="data-section">
        <div class="row">
            <h3> Tracked Values:</h3>
        </div>
        <div class="row">
            <p class="text-dark">Check a value to have it tracked in the analysis centre.</p>
        </div>
        <div class="row">
            <table class="table-custom">
                <?php
                $query = "SELECT ac_nutrient_id, nutrient_name FROM ac_nutrients";
                $stmt = $conn->prepare($query);
                $stmt->execute();

                $result = $stmt->get_result();
                $nutrients = $result->fetch_all(MYSQLI_ASSOC);

                foreach ($nutrients as $i => $nutrient):
                    $query = "SELECT * FROM ac_user_tracking WHERE user_id = ? AND nutrient_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('ii', $_SESSION['user-id'], $nutrient['ac_nutrient_id']);
                    $stmt->execute();

                    $result = $stmt->get_result();
                    $isTracked = $result->num_rows > 0;

                    if ($i % 3 == 0) echo '<tr>';
                    ?>
                    <td>
                        <input type="checkbox" name="tracked_nutrients[]" value="<?= $nutrient['ac_nutrient_id'] ?>" <?= $isTracked ? 'checked' : '' ?> ><?= $nutrient['nutrient_name'] ?>
                    </td>
                    <?php
                    if ($i % 3 == 2) echo '</tr>';
                endforeach;

                if (count($nutrients) % 3 != 0) echo '</tr>';
                ?>
            </table>
            <button class="btn btn-primary" type="button" onclick="checkAll()">Check all</button> <p> </p>
            <button class="btn btn-primary" type="button" onclick="checkNone()">Check none</button> <p> </p>
            <button class="btn btn-primary" type="button" onclick="checkRecommended()">Check recommended</button>
        </div>
    </div>


    <div class="row">
        <button type="submit" class="btn btn-primary">Update Account</button>
    </div>


</form>

<script>
    function checkAll() {
        var checkboxes = document.getElementsByName('tracked_nutrients[]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = true;
        }
    }

    function checkNone() {
        var checkboxes = document.getElementsByName('tracked_nutrients[]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = false;
        }
    }

    function checkRecommended() {
        var recommendedNutrients = [1, 7, 8, 11, 12, 13, 15, 16, 17, 23, 24, 25, 26, 28, 29, 30, 32, 34];

        var checkboxes = document.getElementsByName('tracked_nutrients[]');
        for (var i = 0; i < checkboxes.length; i++) {
            if (recommendedNutrients.includes(parseInt(checkboxes[i].value))) {
                checkboxes[i].checked = true;
            } else {
                checkboxes[i].checked = false;
            }
        }
    }

    // Prevent form submission when clicking the buttons
    var buttons = document.querySelectorAll('button[type="button"]');
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener('click', function(event) {
            event.preventDefault();
        });
    }
</script>

<?php
include_once '../engine/footer.php';
?>

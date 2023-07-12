<?php
/*
    content/analysis/edit_values.php    VERSION 1.3
    Allows a user to modify their values.
    Reviewed 7/12/2023
*/

include_once '../../engine/header.php';
include_once '../../engine/dbConnect.php';
include_once '../../engine/processes/analysis_values.php';

$userId = $_GET['id'];
$userValues = getUserValues2($userId, $conn);

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="../../engine/javascript/preset_values.js"></script>
<script src="https://kit.fontawesome.com/0bd93e423d.js" crossorigin="anonymous"></script>

<!--Page Starts-->
<h2>Analysis Centre</h2>
<ul class="centre-menu">
    <!-- Adding the links to the centre menu -->
    <li class="menu-item"><a href="values.php?id=<?php echo $userId; ?>">Your<br>Values</a></li>
    <li class="menu-item"><a href="week_look.php?id=<?php echo $userId; ?>">Weekly<br>Progress</a></li>
    <li class="menu-item"><a href="custom_look.php?id=<?php echo $userId; ?>">Custom Range<br>Analysis</a></li>
</ul>
<br>
<h1>Edit Your Values</h1>
<p>You can edit your current values here.</p>
<h2>Values</h2>
<form action="../../engine/processes/analysis_values.php" method="POST">
    <input type="hidden" name="user_id" value="<?= $userId ?>">
    <div class="table-container">
        <table id="preset-values-table" class="table-custom">
            <thead>
            <tr>
                <th>Name</th>
                <th>Current Amount/Measurement</th>
                <th>New Amount</th>
                <th>Tracked</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($userValues as $value): ?>
                <tr>
                    <td><?= htmlspecialchars($value['nutrient_name']) ?></td>
                    <td><?= htmlspecialchars($value['ac_amount']) ?> <?= htmlspecialchars($value['measurement_name']) ?></td>
                    <td><input type="text" name="new_amount[<?= $value['ac_nutrient_id'] ?>]" /></td>
                    <td><?= $value['is_tracked'] ? '<i class="fa-solid fa-check" style="color: #00b528;"></i>'
                            : '<i class="fa-solid fa-xmark" style="color: #ff0035;"></i>' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <input type="submit" value="Submit Changes">
</form>
<p>If you make a mistake or want to just change it to the defaults for your gender and health focus, click the button.</p>
<button id="reset-btn" class="btn btn-primary" data-user-id="<?php echo $userId; ?>">Default Values</button>


<script src="../../engine/javascript/edit_values_v2.js"></script>

<?php
include_once '../../engine/footer.php';
?>

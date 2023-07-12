<?php
/*
    content/analysis/values.php    VERSION 1.3
    Allows a user to view their recommended values.
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

<!-- Page Starts-->
<h2>Analysis Centre</h2>
<ul class="centre-menu">
    <!-- Adding the links to the center menu -->
    <li class="menu-item"><a href="values.php?id=<?php echo $userId; ?>">Your<br>Values</a></li>
    <li class="menu-item"><a href="week_look.php?id=<?php echo $userId; ?>">Weekly<br>Progress</a></li>
    <li class="menu-item"><a href="custom_look.php?id=<?php echo $userId; ?>">Custom Range<br>Analysis</a></li>
</ul>
<br>
<h1>View Your Values</h1>
<p>These are your current values.  You can make
    <a href="edit_values.php?id=<?php echo $userId; ?>">changes to your values</a>.</p>
<h2>Values</h2>
<div class="table-container">
    <table id="preset-values-table" class="table-custom">
        <thead>
        <tr>
            <th>Name</th>
            <th>Amount/Measurement</th>
            <th>Tracked</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($userValues as $value): ?>
            <tr>
                <td><?= htmlspecialchars($value['nutrient_name']) ?></td>
                <td><?= htmlspecialchars($value['ac_amount']) ?> <?= htmlspecialchars($value['measurement_name']) ?></td>
                <td><?= $value['is_tracked'] ? '<i class="fa-solid fa-check" style="color: #00b528;"></i>'
                        : '<i class="fa-solid fa-xmark" style="color: #ff0035;"></i>' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include_once '../../engine/footer.php';
?>

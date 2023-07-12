<?php
/*
    content/analysis/portal.php    VERSION 1.3
    Homepage for the Analysis Centre
    Reviewed 7/12/2023
*/

// Include header file and connection
include('../../engine/header.php');
include('../../engine/dbConnect.php');

// Store user id in a variable
$userId = $_SESSION["user-id"];

?>

<!--Page Starts-->
<h2>Analysis Centre</h2>
    <ul class="centre-menu">
        <!-- Adding the links to the center menu -->
        <li class="menu-item"><a href="values.php?id=<?php echo $userId; ?>">Your<br>Values</a></li>
        <li class="menu-item"><a href="week_look.php?id=<?php echo $userId; ?>">Weekly<br>Progress</a></li>
        <li class="menu-item"><a href="custom_look.php?id=<?php echo $userId; ?>">Custom Range<br>Analysis</a></li>
    </ul>
<br>
<h3>Home</h3>
<p>Welcome to the analysis centre.  You can view and make changes to the nutrients that you want to make a priority for
tracking.  You can also view a cumulative analysis, where you can at a quick glance, see your overall summary of your
nutritional intake.  Finally, you can see your current week progress.</p>
<p>This site should not replace the advice you receive from a professional dietician.</p>

<?php
// Include footer
include('../../engine/footer.php');
?>

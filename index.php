<?php
/*
    index.php    VERSION 1.3
    Site homepage.
    Reviewed 7/8/2023
*/

define('ROOT_PATH', __DIR__ . '/');
include_once ROOT_PATH . 'engine/header.php';
?>

<h1>Welcome to Titan!</h1>
<p>The food and meal tracker for IST 440W.</p>
<br><br>
<h2>Advice</h2>
<p>Before the analysis center will track any nutrients, you must configure them in your profile.</p>
<br><br>
<h2>Materials Used</h2>
<p>Blueberry image by Ylanite Koppens on Pexels.</p>
<p>Pumpkin image by Jessica Lewis Creative on Pexels.</p>
<p>Pizza and Lemonade image by Creative on Pexels.</p>

<?php
include_once ROOT_PATH . 'engine/footer.php';
?>

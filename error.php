<?php
include_once ROOT_PATH . 'engine/header.php';
?>

<h1>Error</h1>
<p>Sorry, an error occurred.</p>

<?php
// Display error information if available
if (isset($_GET['code'])) {
    $errorCode = $_GET['code'];
    echo "<p>Error code: " . htmlentities($errorCode) . "</p>";
}

if (isset($_GET['message'])) {
    $errorMessage = $_GET['message'];
    echo "<p>Error message: " . htmlentities($errorMessage) . "</p>";
}
?>

<p>Please contact the administrator for assistance.</p>

<?php
include_once ROOT_PATH . 'engine/footer.php';
?>
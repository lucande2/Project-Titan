<?php
/*
    missing_page.php    VERSION 1.3
    Accessed when nginx/apache server returns 404 error (if site configured for such).
    Reviewed 7/8/2023
*/

include_once ROOT_PATH . 'engine/header.php';
?>

    <h1>Page not found!</h1>

    <p>Sorry, an error occurred.  Please contact the administrator.</p>

<?php
include_once ROOT_PATH . 'engine/footer.php';
?>

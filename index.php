<!DOCTYPE html>
<html lang="en">
<body>

<h1>Hello World!</h1><br>
<p>This is the new homepage, and should tell the browser if you can connect or not!</p>

<?php
include 'engine/dbConnect.php';

if($connect){
    echo "Connection to the database is successful!";
} else {
    echo "There was a problem connecting to the database.";
}
?>

</body>
</html>

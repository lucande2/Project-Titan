<?php
include_once '../engine/dbConnect.php';
include_once '../engine/processes/analysis_preset.php';

// Initialise variables
$username = "";
$email = "";
$password = "";
$biological_sex = "";
$health_focus = "";
$errors = [];
$registrationSuccessful = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $biological_sex = mysqli_real_escape_string($conn, $_POST['biological_sex']);
    $health_focus = mysqli_real_escape_string($conn, $_POST['health_focus']);

    // Check if username is empty
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    // Check if email is empty or invalid
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }

    // Check if password is empty or too short
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password is required and should be at least 6 characters long.";
    }

    // If there are no errors, attempt to register the user
    if (empty($errors)) {
        // Check if username or email already exists
        $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            // The username or email is already taken.
            $errors[] = "The username or email is already taken.";
        } else {
            // The username and email are available, hash the password.
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user record
            $sql = "INSERT INTO users (username, email, password, biological_sex, health_focus) VALUES ('$username', '$email', '$hashed_password', '$biological_sex', '$health_focus')";
            if (mysqli_query($conn, $sql)) {
                $registrationSuccessful = true;
                $userId = mysqli_insert_id($conn);
                initialiseUserNutrientValues($userId);
            } else {
                $errors[] = "Error: " . mysqli_error($conn);
            }
        }
    }
}
include_once '../engine/header.php';
?>
<?php if ($registrationSuccessful): ?>
    <p>Your account <?php echo htmlspecialchars($username); ?> has been created.</p>
<?php else: ?>
<div class="data-section">
    <form action="register.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>"><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br>
        <label for="biological_sex">Biological Sex:</label><br>
        <select id="biological_sex" name="biological_sex">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select><br>
        <label for="health_focus">Health Focus:</label><br>
        <select id="health_focus" name="health_focus">
            <option value="Unaligned">Unaligned</option>
            <option value="Athlete">Athlete</option>
            <option value="Weight loss">Weight loss</option>
        </select><br>
        <input type="submit" value="Register">
    </form>
</div>
    <?php
    // Display error messages
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
    ?>
<?php endif; ?>

<?php
include_once '../engine/footer.php';
?>
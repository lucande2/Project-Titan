<?php
include_once '../engine/dbConnect.php';

// Initialize variables
$username = "";
$email = "";
$password = "";
$errors = [];
$registrationSuccessful = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

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
            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            if (mysqli_query($conn, $sql)) {
                $registrationSuccessful = true;
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
    <form action="register.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>"><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password"><br>
        <input type="submit" value="Register">
    </form>

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

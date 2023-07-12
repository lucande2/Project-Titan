<?php
/*
    content/login.php    VERSION 1.3
    Allows a user to sign into an account.
    Reviewed 7/12/2023
*/

include '../engine/dbConnect.php';
include '../engine/header.php';

// Initialize variables
$username = "";
$password = "";
$remember_me = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $remember_me = isset($_POST['remember_me']);

    // Check if username or password is empty
    if (empty($username) || empty($password)) {
        $errors[] = "Username and password are required.";
    } else {
        // Query database for user
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                session_start();
                $_SESSION['username'] = $username;
                $_SESSION["role"] = $user["role"]; // Storing user role in session
                $_SESSION["user-id"] = $user["id"]; // Storing user-id in session

                // Check if "Remember me" is checked
                if ($remember_me) {
                    // Set a cookie that expires in two weeks
                    setcookie('username', $username, time() + (86400 * 14), "/");
                }

                header("Location: ../index.php");
                exit();
            } else {
                $errors[] = "Invalid username or password.";
            }
        } else {
            $errors[] = "Invalid username or password.";
        }
    }
}
?>
    <!-- Page Starts -->
<h1>Login!</h1>
<p>Please enter your account details.  You can also stay logged in for two weeks if you check "remember me", but do
    not do this on a public computer!</p>

    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<p class="error">' . $error . '</p>';
        }
    }
    ?>
    <form action="login.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <input type="checkbox" id="remember_me" name="remember_me">
        <label for="remember_me">Remember me</label><br>
        <input type="submit" value="Login">
    </form>

<?php
include '../engine/footer.php';
?>

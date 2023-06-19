<?php
include_once '../engine/header.php';
include_once '../engine/dbConnect.php';

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../content/login.php');
    exit;
}

if (!isset($_GET['username'])) {
    header('Location: error.php');
    exit();
}

// Fetching user data
$username = $_GET['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if(!$user) {
    header('Location: error.php');
    exit();
}

// Post request for updating user data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        // Delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE username=?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
    } else {
        // Update user
        $username = trim($_POST['username']);
        $password = $_POST['password']; // Will be hashed before storing
        $email = trim($_POST['email']);
        $role = trim($_POST['role']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hashing the password

        // Prepared statement to update user
        $stmt = $conn->prepare("UPDATE users SET username=?, password=?, email=?, role=? WHERE username=?");
        $stmt->bind_param('sssss', $username, $hashed_password, $email, $role, $username);
        $stmt->execute();
    }

    header("Location: manage_users.php");
    exit();
}

?>

<h1>Manage User: <?php echo $user['username']; ?></h1>

<form action="manage_user.php?username=<?php echo $user['username']; ?>" method="post">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" value="" placeholder="Enter new password"><br>
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required><br>
    <label for="role">Role:</label><br>
    <select name="role" id="role" required>
        <option value="user" <?php echo ($user['role'] == 'user' ? 'selected' : ''); ?>>User</option>
        <option value="admin" <?php echo ($user['role'] == 'admin' ? 'selected' : ''); ?>>Admin</option>
    </select><br>
    <input type="submit" value="Update">
    <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
</form>

<?php include_once '../engine/footer.php'; ?>

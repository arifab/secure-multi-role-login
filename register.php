<?php
session_start();
require_once "config/Database.php";
require_once "classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(
        !empty($_POST['username']) &&
        !empty($_POST['email']) &&
        !empty($_POST['password']) &&
        !empty($_POST['confirm_password'])
    ) {
        if($_POST['password'] === $_POST['confirm_password']) {
            $user->username = $_POST['username'];
            $user->email = $_POST['email'];
            $user->password = $_POST['password'];

            if($user->create()) {
                $message = "Registration successful! Please login.";
            } else {
                $message = "Registration failed. Username or email might already exist.";
            }
        } else {
            $message = "Passwords do not match.";
        }
    } else {
        $message = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h2>Register</h2>
    <?php if($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <div>
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>
        </div>
        <div>
            <input type="submit" value="Register">
        </div>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
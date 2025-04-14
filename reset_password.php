<?php
session_start();
require_once "config/Database.php";
require_once "classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = "";
$token = isset($_GET['token']) ? $_GET['token'] : '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
        if($_POST['password'] === $_POST['confirm_password']) {
            if($user->resetPassword($token, $_POST['password'])) {
                $message = "Password has been reset successfully. You can now login.";
            } else {
                $message = "Invalid or expired reset token.";
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
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h2>Reset Password</h2>
    <?php if($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div>
            <label>New Password:</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required>
        </div>
        <div>
            <input type="submit" value="Reset Password">
        </div>
    </form>
    <p><a href="login.php">Back to Login</a></p>
</body>
</html>
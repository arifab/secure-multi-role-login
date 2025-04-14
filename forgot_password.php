<?php
session_start();
require_once "config/Database.php";
require_once "classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!empty($_POST['email'])) {
        $token = $user->requestPasswordReset($_POST['email']);
        
        if($token) {
            // In a production environment, you would send this via email
            $message = "Password reset link has been sent to your email.";
            
            // For demonstration purposes:
            $reset_link = "http://yourdomain.com/reset_password.php?token=" . $token;
            $message .= "<br>Reset link: " . $reset_link;
        } else {
            $message = "Email not found.";
        }
    } else {
        $message = "Please enter your email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h2>Forgot Password</h2>
    <?php if($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <input type="submit" value="Reset Password">
        </div>
    </form>
    <p><a href="login.php">Back to Login</a></p>
</body>
</html>
<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "config/Database.php";
require_once "classes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$is_admin = ($_SESSION['role_id'] == 1);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    
    <?php if($is_admin): ?>
        <div>
            <h3>Admin Panel</h3>
            <!-- Add admin-specific features here -->
            <ul>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="system_settings.php">System Settings</a></li>
            </ul>
        </div>
    <?php endif; ?>

    <div>
        <h3>User Panel</h3>
        <!-- Add user features here -->
        <ul>
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="change_password.php">Change Password</a></li>
        </ul>
    </div>

    <p><a href="logout.php">Logout</a></p>
</body>
</html>
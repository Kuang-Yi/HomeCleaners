<?php
session_start();

// If user is already logged in, redirect based on role
if (isset($_SESSION['user'])) {
    switch ($_SESSION['user']['user_type']) {
        case 'C':
            header('Location: boundary/dashboard_cleaner.php');
            break;
        case 'H':
            header('Location: boundary/dashboard_homeowner.php');
            break;
        case 'A':
            header('Location: boundary/dashboard_admin.php');
            break;
        case 'P':
            header('Location: boundary/dashboard_platform.php');
            break;
        default:
            echo "❌ Unknown user role.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Cleaners Platform</title>
</head>
<body>
    <h1>Welcome to Home Cleaners</h1>
    <p>Please <a href="boundary/login.php">Login</a> or <a href="boundary/register.php">Register</a> to continue.</p>
</body>
</html>

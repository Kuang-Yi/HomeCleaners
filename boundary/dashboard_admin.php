<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'A') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['email']) ?>!</p>

    <ul>
        <li><a href="manage_users.php">Manage Users</a></li>
    </ul>

    <p><a href="../logout.php">Logout</a></p>
</body>
</html>

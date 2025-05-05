<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'P') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Platform Manager Dashboard</title>
</head>
<body>
    <h2>Platform Manager Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['email']) ?>!</p>

    <ul>
        <li><a href="manage_categories.php">Manage Cleaning Categories</a></li>
        <li><a href="#">View Reports</a> (coming soon)</li>
    </ul>

    <p><a href="../logout.php">Logout</a></p>
</body>
</html>

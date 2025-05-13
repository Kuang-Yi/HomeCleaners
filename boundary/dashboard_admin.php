<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'A') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="container">
    <h2>Admin Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['email']) ?>!</p>

    <ul>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>
</body>
</html>

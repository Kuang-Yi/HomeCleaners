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
	<link rel="stylesheet" href="../css/dashboard_platform.css">

</head>
<body>

    <div class="dashboard-main">
    <h2>Platform Manager Dashboard</h2>
    <p class="welcome">Welcome back, Platform Manager!</p>

    <div class="notification">
        You have access to platform-wide reports and category management.
    </div>

    <div class="dashboard-links">
        <a href="manage_categories.php">Manage Categories</a>
        <a href="generate_report.php">Generate Reports</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'H') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Homeowner Dashboard</title>
</head>
<body>
    <h2>Homeowner Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['email']) ?>!</p>

    <ul>
	    <li><a href="view_services.php">Browse Available Services</a></li>
        <li><a href="#">Search for Cleaners</a> (coming soon)</li>
        <li><a href="view_bookings.php">View My Bookings</a></li>
		<li><a href="view_shortlist.php">View My Shortlist</a></li>


    </ul>

    <p><a href="../logout.php">Logout</a></p>
</body>
</html>

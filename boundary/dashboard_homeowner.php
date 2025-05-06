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
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/homeowner.css">
</head>
<body>
<div class="dashboard-layout">

    <!-- Sidebar -->
    <aside class="sidebar homeowner-sidebar">
        <div class="sidebar-brand">HomeCleaners</div>
        <nav class="sidebar-links">
            <a href="dashboard_homeowner.php" class="active">Dashboard</a>
            <a href="view_services.php">Browse Services</a>
            <a href="#">Search for Cleaners</a>
            <a href="view_bookings.php">My Bookings</a>
            <a href="view_shortlist.php">My Shortlist</a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="dashboard-main">
        <h2>Homeowner Dashboard</h2>
        <p>Welcome, <?= htmlspecialchars($_SESSION['user']['email']) ?>!</p>
    </main>

</div>
</body>
</html>

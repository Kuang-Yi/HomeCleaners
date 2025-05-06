<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'C') {
    header('Location: login.php');
    exit();
}

$cleaner_id = $_SESSION['user']['id'];

// Count bookings with status = 'confirmed'
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM bookings
    WHERE status = 'confirmed'
    AND service_id IN (SELECT id FROM services WHERE cleaner_id = ?)
");
$stmt->execute([$cleaner_id]);
$pending_review_count = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cleaner Dashboard</title>
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/dashboard_cleaner.css">
</head>
<body>
<div class="dashboard-layout">

    <aside class="sidebar">
        <div class="sidebar-brand">HomeCleaners</div>
        <nav class="sidebar-links">
            <a href="dashboard_cleaner.php" class="active">Dashboard</a>
            <a href="add_service.php">Add Service</a>
            <a href="manage_services.php">My Services</a>
            <a href="view_cleaner_bookings.php">Bookings</a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </nav>
    </aside>

    <main class="dashboard-main">
        <h2>Cleaner Dashboard</h2>
        <p class="welcome">Welcome, <?= htmlspecialchars($_SESSION['user']['email']) ?>!</p>

        <?php if ($pending_review_count > 0): ?>
            <div class="notification">
                🔔 You have <?= $pending_review_count ?> new booking<?= $pending_review_count > 1 ? 's' : '' ?> awaiting response!
            </div>
        <?php endif; ?>
    </main>

</div>
</body>
</html>

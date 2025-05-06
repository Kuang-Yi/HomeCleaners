<?php
session_start();
require_once '../config/db.php';
require_once '../control/HomeownerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'H') {
    header('Location: login.php');
    exit();
}

$homeowner_id = $_SESSION['user']['id'];
$shortlisted = HomeownerController::getShortlistedServices($homeowner_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'] ?? null;

    if ($service_id) {
        if (isset($_POST['remove'])) {
            HomeownerController::removeFromShortlist($homeowner_id, $service_id);
        }

        if (isset($_POST['book']) && !empty($_POST['booking_datetime'])) {
            HomeownerController::bookService($homeowner_id, $service_id, $_POST['booking_datetime']);
        }

        header("Location: view_shortlist.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Shortlisted Services</title>
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/homeowner.css">
    <link rel="stylesheet" href="../css/view_shortlist.css">
</head>
<body>
<div class="dashboard-layout">

    <!-- Sidebar -->
    <aside class="sidebar homeowner-sidebar">
        <div class="sidebar-brand">HomeCleaners</div>
        <nav class="sidebar-links">
            <a href="dashboard_homeowner.php">Dashboard</a>
            <a href="view_services.php">Browse Services</a>
            <a href="#">Search for Cleaners</a>
            <a href="view_bookings.php">My Bookings</a>
            <a href="view_shortlist.php" class="active">My Shortlist</a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="dashboard-main">
        <h2>My Shortlisted Services</h2>

        <?php if (empty($shortlisted)): ?>
            <p>You have no services in your shortlist.</p>
        <?php else: ?>
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Cleaner Email</th>
                        <th>Pricing</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shortlisted as $i => $s): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($s['title']) ?></td>
                            <td><?= nl2br(htmlspecialchars($s['description'])) ?></td>
                            <td><?= htmlspecialchars($s['category_name']) ?></td>
                            <td><?= htmlspecialchars($s['cleaner_email']) ?></td>
                            <td><?= $s['pricing_type'] === 'per_job' ? 'Per Job' : 'Per Hour' ?></td>
                            <td><?= number_format($s['price'], 2) ?> SGD</td>
                            <td>
                                <form method="post" class="action-form">
                                    <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                                    <input type="datetime-local" name="booking_datetime" required min="<?= date('Y-m-d\TH:i') ?>"><br>
                                    <div class="action-buttons">
                                        <button type="submit" name="book" class="btn btn-green">Book</button>
                                        <button type="submit" name="remove" class="btn btn-red" onclick="return confirm('Remove from shortlist?')">Remove</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

</div>
</body>
</html>

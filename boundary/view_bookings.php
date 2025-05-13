<?php
session_start();
require_once '../config/db.php';
require_once '../control/HomeownerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'H') {
    header('Location: login.php');
    exit();
}

$homeowner_id = $_SESSION['user']['id'];
$service_search = $_GET['service_search'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$bookings = HomeownerController::filterBookings($homeowner_id, $service_search, $start_date, $end_date);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/homeowner.css">
    <link rel="stylesheet" href="../css/view_bookings.css">
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
            <a href="view_bookings.php" class="active">My Bookings</a>
            <a href="view_shortlist.php">My Shortlist</a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="dashboard-main">
        <h2>My Bookings</h2>

        <form method="get" class="filter-form">
            <div class="form-row">
                <label for="service_search">Search Service Title:</label>
                <input type="text" name="service_search" placeholder="Enter service title..." value="<?= htmlspecialchars($service_search) ?>">
            </div>

            <div class="form-row">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
            </div>

            <div class="form-row">
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-blue">Search</button>
            </div>
        </form>

        <?php if (empty($bookings)): ?>
            <p>No bookings found.</p>
        <?php else: ?>
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Service</th>
                        <th>Category</th>
                        <th>Cleaner</th>
                        <th>Price</th>
                        <th>Pricing Type</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $i => $b): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($b['title']) ?></td>
                            <td><?= htmlspecialchars($b['category_name']) ?></td>
                            <td><?= htmlspecialchars($b['cleaner_email']) ?></td>
                            <td><?= number_format($b['price'], 2) ?> SGD</td>
                            <td><?= $b['pricing_type'] === 'per_job' ? 'Per Job' : 'Per Hour' ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($b['booking_datetime'])) ?></td>
                            <td><?= htmlspecialchars($b['status']) ?></td>
                            <td>
                                <?php if (strtolower($b['status']) === 'pending'): ?>
                                    <form method="post" action="cancel_booking.php" onsubmit="return confirm('Cancel this booking?')">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn btn-red">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
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

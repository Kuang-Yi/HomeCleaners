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
</head>
<body>
    <h2>My Bookings</h2>

    <form method="get">
        <label for="service_search">Search Service Title:</label>
        <input type="text" name="service_search" placeholder="Enter service title..." value="<?= htmlspecialchars($service_search) ?>">

        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">

        <button type="submit">Search</button>
    </form>
    <br>

    <?php if (empty($bookings)): ?>
        <p>No bookings found.</p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Service</th>
                <th>Category</th>
                <th>Cleaner</th>
                <th>Price</th>
                <th>Pricing Type</th>
                <th>Scheduled</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['title']) ?></td>
                    <td><?= htmlspecialchars($b['category_name']) ?></td>
                    <td><?= htmlspecialchars($b['cleaner_email']) ?></td>
                    <td><?= number_format($b['price'], 2) ?> SGD</td>
                    <td><?= $b['pricing_type'] === 'per_job' ? 'Per Job' : 'Per Hour' ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($b['booking_datetime'])) ?></td>
                    <td><?= htmlspecialchars($b['status']) ?></td>
                    <td>
                        <?php if ($b['status'] === 'Pending'): ?>
                            <form method="post" action="cancel_booking.php">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <button type="submit" onclick="return confirm('Cancel this booking?')">Cancel</button>
                            </form>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="dashboard_homeowner.php">← Back to Dashboard</a></p>
</body>
</html>

<?php
session_start();
require_once '../config/db.php';
require_once '../control/CleanerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'C') {
    header('Location: login.php');
    exit();
}

$cleaner_id = $_SESSION['user']['id'];

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['action'])) {
    $booking_id = intval($_POST['booking_id']);
    $action = $_POST['action'];
    $status_map = [
        'accept' => 'pending',
        'reject' => 'rejected',
        'start' => 'in_progress',
        'complete' => 'completed'
    ];

    if (isset($status_map[$action])) {
        CleanerController::updateBookingStatus($cleaner_id, $booking_id, $status_map[$action]);
        header("Location: view_cleaner_bookings.php?message=" . urlencode("✅ Status updated."));
        exit();
    }
}

// Fetch all bookings
$bookings = CleanerController::getBookings($cleaner_id);

// Apply filters
$service_filter = strtolower(trim($_GET['service'] ?? ''));
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$filtered = array_filter($bookings, function ($b) use ($service_filter, $start_date, $end_date) {
    $match_service = empty($service_filter) || strpos(strtolower($b['title']), $service_filter) !== false;
    $match_start = empty($start_date) || strtotime($b['booking_datetime']) >= strtotime($start_date . ' 00:00:00');
    $match_end = empty($end_date) || strtotime($b['booking_datetime']) <= strtotime($end_date . ' 23:59:59');
    return $match_service && $match_start && $match_end;
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cleaner - My Bookings</title>
</head>
<body>
    <h2>My Bookings</h2>

    <?php if (!empty($_GET['message'])): ?>
        <p><?= htmlspecialchars($_GET['message']) ?></p>
    <?php endif; ?>

    <form method="get">
        <label>Search by Service Title:</label>
        <input type="text" name="service" value="<?= htmlspecialchars($service_filter) ?>">

        <label>Start Date:</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">

        <label>End Date:</label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">

        <button type="submit">Apply Filters</button>
        <a href="view_cleaner_bookings.php">Reset</a>
    </form>

    <br>

    <?php if (empty($filtered)): ?>
        <p>No bookings match your filters.</p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Service</th>
                <th>Category</th>
                <th>Homeowner</th>
                <th>Price</th>
                <th>Pricing</th>
                <th>Scheduled</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($filtered as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['title']) ?></td>
                    <td><?= htmlspecialchars($b['category_name']) ?></td>
                    <td><?= htmlspecialchars($b['homeowner_email']) ?></td>
                    <td><?= number_format($b['price'], 2) ?> SGD</td>
                    <td><?= $b['pricing_type'] === 'per_job' ? 'Per Job' : 'Per Hour' ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($b['booking_datetime'])) ?></td>
                    <td><?= ucfirst($b['status']) ?></td>
                    <td>
                        <?php if ($b['status'] === 'confirmed'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <button type="submit" name="action" value="accept">Accept</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <button type="submit" name="action" value="reject">Reject</button>
                            </form>
                        <?php elseif ($b['status'] === 'pending'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <button type="submit" name="action" value="start">Start</button>
                            </form>
                        <?php elseif ($b['status'] === 'in_progress'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <button type="submit" name="action" value="complete">Complete</button>
                            </form>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="dashboard_cleaner.php">← Back</a></p>
</body>
</html>

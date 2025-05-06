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
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/view_cleaner_bookings.css">
</head>
<body>
<div class="dashboard-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">HomeCleaners</div>
        <nav class="sidebar-links">
            <a href="dashboard_cleaner.php">Dashboard</a>
            <a href="add_service.php">Add Service</a>
            <a href="manage_services.php">My Services</a>
            <a href="view_cleaner_bookings.php" class="active">Bookings</a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="dashboard-main">
        <h2>My Bookings</h2>

        <?php if (!empty($_GET['message'])): ?>
            <div class="message"><?= htmlspecialchars($_GET['message']) ?></div>
        <?php endif; ?>

        <form method="get" class="filter-form">
            <div class="form-row">
                <label>Search by Service Title:</label>
                <input type="text" name="service" value="<?= htmlspecialchars($service_filter) ?>">
            </div>

            <div class="form-row">
                <label>Start Date:</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
            </div>

            <div class="form-row">
                <label>End Date:</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
            </div>

            <div class="form-actions">
                <button type="submit">Apply Filters</button>
                <a href="view_cleaner_bookings.php" class="reset-link">Reset</a>
            </div>
        </form>

        <?php if (empty($filtered)): ?>
            <p>No bookings match your filters.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Service</th>
                            <th>Category</th>
                            <th>Homeowner</th>
                            <th>Price</th>
                            <th>Pricing</th>
                            <th>Scheduled</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filtered as $i => $b): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
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
                                            <button type="submit" name="action" value="accept" class="btn btn-green">Accept</button>
                                        </form>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <button type="submit" name="action" value="reject" class="btn btn-red">Reject</button>
                                        </form>

                                    <?php elseif ($b['status'] === 'pending'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <button type="submit" name="action" value="start" class="btn btn-blue">Start</button>
                                        </form>

                                    <?php elseif ($b['status'] === 'in_progress'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <button type="submit" name="action" value="complete" class="btn btn-green">Complete</button>
                                        </form>

                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>

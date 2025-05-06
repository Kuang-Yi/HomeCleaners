<?php
session_start();
require_once '../config/db.php';
require_once '../control/HomeownerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'H') {
    header('Location: login.php');
    exit();
}

$homeowner_id = $_SESSION['user']['id'];
$search = $_GET['search'] ?? '';
$services = HomeownerController::searchServices($search);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'] ?? null;

    if ($service_id) {
        HomeownerController::incrementServiceView($service_id);

        if (isset($_POST['shortlist'])) {
            HomeownerController::shortlistService($homeowner_id, $service_id);
        }

        if (isset($_POST['book']) && !empty($_POST['booking_datetime'])) {
            HomeownerController::bookService($homeowner_id, $service_id, $_POST['booking_datetime']);
        }

        header("Location: view_services.php?search=" . urlencode($search));
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Cleaning Services</title>
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/homeowner.css">
    <link rel="stylesheet" href="../css/view_services.css">
</head>
<body>
<div class="dashboard-layout">

    <!-- Sidebar -->
    <aside class="sidebar homeowner-sidebar">
        <div class="sidebar-brand">HomeCleaners</div>
        <nav class="sidebar-links">
            <a href="dashboard_homeowner.php">Dashboard</a>
            <a href="view_services.php" class="active">Browse Services</a>
            <a href="#">Search for Cleaners</a>
            <a href="view_bookings.php">My Bookings</a>
            <a href="view_shortlist.php">My Shortlist</a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="dashboard-main">
        <h2>Browse Cleaning Services</h2>

        <form method="get" class="service-search-form">
            <input type="text" name="search" placeholder="Search title, category, or cleaner..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-blue">Search</button>
        </form>

        <div class="table-wrapper">
            <table class="service-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Cleaner Email</th>
                        <th>Pricing</th>
                        <th>Price (SGD)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['title']) ?></td>
                            <td><?= nl2br(htmlspecialchars($s['description'])) ?></td>
                            <td><?= htmlspecialchars($s['category_name']) ?></td>
                            <td><?= htmlspecialchars($s['cleaner_email']) ?></td>
                            <td><?= $s['pricing_type'] === 'per_job' ? 'Per Job' : 'Per Hour' ?></td>
                            <td><?= number_format($s['price'], 2) ?></td>
                            <td>
                                <form method="post" class="action-form">
                                    <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                    <input type="datetime-local" name="booking_datetime" required min="<?= date('Y-m-d\TH:i') ?>">
                                    <div class="action-buttons">
                                        <button type="submit" name="book" class="btn btn-green">Book</button>
                                        <button type="submit" name="shortlist" class="btn btn-blue">Shortlist</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>

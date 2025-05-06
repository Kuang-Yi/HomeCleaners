<?php
session_start();
require_once '../config/db.php';
require_once '../control/CleanerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'C') {
    header('Location: login.php');
    exit();
}

$cleaner_id = $_SESSION['user']['id'];
$services = CleanerController::getCleanerServices($cleaner_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage My Services</title>
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/manage_services.css">
</head>
<body>
<div class="dashboard-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">HomeCleaners</div>
        <nav class="sidebar-links">
            <a href="dashboard_cleaner.php">Dashboard</a>
            <a href="add_service.php">Add Service</a>
            <a href="manage_services.php" class="active">My Services</a>
            <a href="view_cleaner_bookings.php">Bookings</a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">
        <h2>Manage My Services</h2>

        <div class="services-header">
            <a href="add_service.php" class="add-link">+ Add New Service</a>
        </div>

        <?php if (empty($services)): ?>
            <p>You haven't listed any services yet.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Pricing</th>
                            <th>Price (SGD)</th>
                            <th>Views</th>
                            <th>Shortlists</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $i => $s): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($s['title']) ?></td>
                                <td><?= nl2br(htmlspecialchars($s['description'])) ?></td>
                                <td><?= htmlspecialchars($s['category_name']) ?></td>
                                <td><?= $s['pricing_type'] === 'per_job' ? 'Per Job' : 'Per Hour' ?></td>
                                <td><?= number_format($s['price'], 2) ?></td>
                                <td><?= (int) $s['view_count'] ?></td>
                                <td><?= (int) $s['shortlist_count'] ?></td>
                                <td>
                                    <a href="edit_service.php?id=<?= $s['id'] ?>">Edit</a> |
                                    <a href="delete_service.php?id=<?= $s['id'] ?>" onclick="return confirm('Delete this service?')">Delete</a>
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

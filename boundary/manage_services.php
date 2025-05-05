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
</head>
<body>
    <h2>Manage My Services</h2>

    <p><a href="add_service.php">+ Add New Service</a></p>

    <?php if (empty($services)): ?>
        <p>You haven't listed any services yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Category</th>
                <th>Pricing</th>
                <th>Price (SGD)</th>
                <th>Views</th>
                <th>Shortlists</th>
                <th>Action</th>
            </tr>
            <?php foreach ($services as $s): ?>
                <tr>
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
        </table>
    <?php endif; ?>

    <p><a href="dashboard_cleaner.php">← Back to Dashboard</a></p>
</body>
</html>

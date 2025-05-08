<?php
session_start();
require_once '../config/db.php';
require_once '../control/HomeownerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'H') {
    header('Location: login.php');
    exit();
}

$homeowner_id = $_SESSION['user']['id'];
$shortlisted_services = HomeownerController::getShortlistedServices($homeowner_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Shortlisted Services</title>
</head>
<body>
    <h2>My Shortlist</h2>

    <?php if (empty($shortlisted_services)): ?>
        <p>You have not shortlisted any services yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Category</th>
                <th>Cleaner Email</th>
                <th>Pricing</th>
                <th>Price (SGD)</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($shortlisted_services as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($s['description'])) ?></td>
                    <td><?= htmlspecialchars($s['category_name']) ?></td>
                    <td><?= htmlspecialchars($s['cleaner_email']) ?></td>
                    <td><?= $s['pricing_type'] === 'per_job' ? 'Per Job' : 'Per Hour' ?></td>
                    <td><?= number_format($s['price'], 2) ?></td>
                    <td>
                        <!-- Book -->
                        <form method="post" action="book_shortlist.php" style="margin-bottom: 5px;">
                            <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                            <input type="datetime-local" name="booking_datetime" required>
                            <button type="submit">Book</button>
                        </form>

                        <!-- Remove -->
                        <form method="post" action="remove_shortlist.php" onsubmit="return confirm('Remove from shortlist?');">
                            <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="dashboard_homeowner.php">← Back to Dashboard</a></p>
</body>
</html>

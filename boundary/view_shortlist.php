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
    <link rel="stylesheet" href="../css/view_shortlist.css?v=2">
</head>
<body>
<div class="container">
    <h2>My Shortlist</h2>

    <?php if (empty($shortlisted_services)): ?>
        <p>You have not shortlisted any services yet.</p>
    <?php else: ?>
        <table>
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
                        <form method="post" action="book_shortlist.php" onsubmit="return confirm('Proceed with booking?');">
                            <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                            <table class="action-table">
                                <tr>
                                    <td colspan="2">
                                        <input type="datetime-local" name="booking_datetime" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td><button type="submit" name="book">Book</button></td>
                                    <td><button type="submit" name="remove" formaction="remove_shortlist.php">Remove</button></td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <a href="dashboard_homeowner.php">← Back to Dashboard</a>
</div>
</body>
</html>

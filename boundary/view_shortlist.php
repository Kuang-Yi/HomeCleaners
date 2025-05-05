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
</head>
<body>
    <h2>My Shortlisted Services</h2>

    <?php if (empty($shortlisted)): ?>
        <p>You have no services in your shortlist.</p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Category</th>
                <th>Cleaner Email</th>
                <th>Pricing</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
            <?php foreach ($shortlisted as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($s['description'])) ?></td>
                    <td><?= htmlspecialchars($s['category_name']) ?></td>
                    <td><?= htmlspecialchars($s['cleaner_email']) ?></td>
                    <td><?= $s['pricing_type'] === 'per_job' ? 'Per Job' : 'Per Hour' ?></td>
                    <td><?= number_format($s['price'], 2) ?> SGD</td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                            <input type="datetime-local" name="booking_datetime"><br>
                            <button type="submit" name="book">Book</button>
                            <button type="submit" name="remove" onclick="return confirm('Remove from shortlist?')">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="dashboard_homeowner.php">← Back to Dashboard</a></p>
</body>
</html>

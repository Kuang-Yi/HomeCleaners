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

        if (isset($_POST['book']) && !empty($_POST['booking_datetime'])) {
            HomeownerController::bookService($homeowner_id, $service_id, $_POST['booking_datetime']);
            header("Location: view_services.php?search=" . urlencode($search));
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Cleaning Services</title>
    <link rel="stylesheet" href="../css/view_services.css">
</head>
<body>
    <h2>Browse Cleaning Services</h2>

    <form method="get">
        <input type="text" name="search" placeholder="Search title, category, or cleaner..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

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
        <?php foreach ($services as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['title']) ?></td>
                <td><?= nl2br(htmlspecialchars($s['description'])) ?></td>
                <td><?= htmlspecialchars($s['category_name']) ?></td>
                <td><?= htmlspecialchars($s['cleaner_email']) ?></td>
                <td><?= $s['pricing_type'] === 'per_job' ? 'Per Job' : 'Per Hour' ?></td>
                <td><?= number_format($s['price'], 2) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                        <input type="datetime-local" name="booking_datetime" required>
                        <button type="submit" name="book">Book</button>
                    </form>

                    <form method="post" action="shortlist_service.php">
                        <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                        <button type="submit">Shortlist</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="dashboard_homeowner.php">← Back to Dashboard</a>
</body>
</html>


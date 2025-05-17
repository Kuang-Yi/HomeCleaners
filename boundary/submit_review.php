<?php
session_start();
require_once '../config/db.php';
require_once '../control/HomeownerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'H') {
    header('Location: login.php');
    exit();
}

$homeownerId = $_SESSION['user']['id'];
$message = '';
$bookings = HomeownerController::getCompletedBookingsWithoutReview($homeownerId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['rating'])) {
    $bookingId = $_POST['booking_id'];
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment'] ?? '');

    $result = HomeownerController::submitReview($bookingId, $rating, $comment);
    if ($result === true) {
        $message = "Review submitted successfully.";
        $bookings = HomeownerController::getCompletedBookingsWithoutReview($homeownerId);
    } else {
        $message = $result;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Review</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/submit_review.css">
</head>
<body>
<div class="container">
    <h2>Submit Review for Completed Services</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <p>You have no completed services to review.</p>
    <?php else: ?>
        <form method="post">
            <label for="booking_id">Select a completed service:</label><br>
            <select name="booking_id" required>
                <?php foreach ($bookings as $b): ?>
                    <option value="<?= $b['id'] ?>">
                        <?= htmlspecialchars($b['title']) ?> (<?= $b['booking_datetime'] ?>)
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="rating">Rating (1-5):</label><br>
            <select name="rating" required>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select><br><br>

            <label for="comment">Comment (optional):</label><br>
            <textarea name="comment" rows="4" cols="50" placeholder="Write your feedback here..."></textarea><br><br>

            <button type="submit">Submit Review</button>
        </form>
    <?php endif; ?>

    <p><a href="dashboard_homeowner.php">← Back to Dashboard</a></p>
</div>
</body>
</html>

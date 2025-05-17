<?php
session_start();
require_once '../config/db.php';
require_once '../entity/Review.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'C') {
    header('Location: login.php');
    exit();
}

$cleanerId = $_SESSION['user']['id'];
$reviews = Review::getByCleanerId($pdo, $cleanerId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Service Reviews</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/view_reviews.css">
</head>
<body>
<div class="container">
    <h2>My Service Reviews</h2>

    <?php if (empty($reviews)): ?>
        <p>You have not received any reviews yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Service</th>
                <th>Homeowner</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Date</th>
            </tr>
            <?php foreach ($reviews as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['homeowner_email']) ?></td>
                    <td><?= $r['rating'] ?>/5</td>
                    <td><?= nl2br(htmlspecialchars($r['comment'])) ?></td>
                    <td><?= $r['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="dashboard_cleaner.php">← Back to Dashboard</a></p>
</div>
</body>
</html>

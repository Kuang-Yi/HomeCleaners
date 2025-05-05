<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'C') {
    header('Location: login.php');
    exit();
}

$cleaner_id = $_SESSION['user']['id'];

// Count bookings with status = 'confirmed'
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM bookings
    WHERE status = 'confirmed'
    AND service_id IN (SELECT id FROM services WHERE cleaner_id = ?)
");
$stmt->execute([$cleaner_id]);
$pending_review_count = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cleaner Dashboard</title>
</head>
<body>
    <h2>Cleaner Dashboard</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['email']) ?>!</p>
	
	<?php if ($pending_review_count > 0): ?>
    <p style="color: red; font-weight: bold;">
        🔔 You have <?= $pending_review_count ?> new booking<?= $pending_review_count > 1 ? 's' : '' ?> awaiting response!
    </p>
<?php endif; ?>


    <ul>
        <li><a href="add_service.php">Add New Cleaning Service</a></li>
		<li><a href="manage_services.php">Manage My Services</a></li>
		<li><a href="view_cleaner_bookings.php">View My Bookings</a></li>
    </ul>

    <p><a href="../logout.php">Logout</a></p>
</body>
</html>

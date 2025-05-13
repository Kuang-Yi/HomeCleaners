<?php
session_start();
require_once '../config/db.php';
require_once '../control/CleanerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'C') {
    header('Location: login.php');
    exit();
}

$cleaner_id = $_SESSION['user']['id'];
$service_id = $_GET['id'] ?? null;

if (!$service_id) {
    header('Location: manage_services.php');
    exit();
}

$service = CleanerController::getServiceById($service_id, $cleaner_id);
if (!$service) {
    echo "<p>Service not found or not owned by you.</p><p><a href='manage_services.php'>Back</a></p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $pricing_type = $_POST['pricing_type'];
    $price = $_POST['price'];

    CleanerController::updateService($service_id, $cleaner_id, $title, $description, $pricing_type, $price);
    header("Location: manage_services.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Service</title>
	<link rel="stylesheet" href="../css/edit_service.css">
</head>
<body>
<div class="container">
    <h2>Edit My Service</h2>

    <form method="post">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($service['title']) ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($service['description']) ?></textarea>

        <label>Pricing Type:</label>
        <input type="radio" name="pricing_type" value="per_job" <?= $service['pricing_type'] === 'per_job' ? 'checked' : '' ?>> Per Job
        <input type="radio" name="pricing_type" value="per_hour" <?= $service['pricing_type'] === 'per_hour' ? 'checked' : '' ?>> Per Hour

        <label>Price (SGD):</label>
        <input type="number" name="price" value="<?= $service['price'] ?>" step="0.01" required>

        <button type="submit">Update Service</button>
    </form>

    <a href="manage_services.php">← Back to My Services</a>
</div>
</body>
</html>

<?php
session_start();
require_once '../config/db.php';
require_once '../control/CleanerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'C') {
    header('Location: login.php');
    exit();
}

$cleaner_id = $_SESSION['user']['id'];
$service_id = intval($_GET['id'] ?? 0);
$service = CleanerController::getServiceById($cleaner_id, $service_id);
if (!$service) { die("Service not found or unauthorized."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = CleanerController::updateService(
        $cleaner_id,
        $service_id,
        $_POST['title'],
        $_POST['description'],
        $_POST['pricing_type'],
        floatval($_POST['price'])
    );

    if ($success) {
        header("Location: manage_services.php?message=" . urlencode("✅ Service updated."));
        exit();
    } else {
        $error = "❌ Update failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Service</title></head>
<body>
    <h2>Edit Service</h2>
    <?php if (!empty($error)): ?><p><?= $error ?></p><?php endif; ?>

    <form method="post">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($service['title']) ?>" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required><?= htmlspecialchars($service['description']) ?></textarea><br><br>

        <label>Pricing Type:</label><br>
        <input type="radio" name="pricing_type" value="per_job" <?= $service['pricing_type'] === 'per_job' ? 'checked' : '' ?>> Per Job
        <input type="radio" name="pricing_type" value="per_hour" <?= $service['pricing_type'] === 'per_hour' ? 'checked' : '' ?>> Per Hour<br><br>

        <label>Price:</label><br>
        <input type="number" name="price" value="<?= $service['price'] ?>" step="0.01" required><br><br>

        <label>Category:</label><br>
        <input type="text" value="<?= htmlspecialchars($service['category_name']) ?>" readonly><br><br>

        <button type="submit">Update</button>
    </form>

    <p><a href="manage_services.php">← Back</a></p>
</body>
</html>

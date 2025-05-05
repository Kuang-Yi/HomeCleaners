<?php
session_start();
require_once '../config/db.php';
require_once '../control/CleanerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'C') {
    header('Location: login.php');
    exit();
}

$cleaner_id = $_SESSION['user']['id'];

// Fetch categories for dropdown
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $pricing_type = $_POST['pricing_type'];
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);

    $success = CleanerController::addService($cleaner_id, $category_id, $title, $description, $pricing_type, $price);
    if ($success) {
        header("Location: manage_services.php?message=" . urlencode("✅ Service added."));
        exit();
    } else {
        $error = "❌ Failed to add service.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Add New Service</title></head>
<body>
    <h2>Create New Service</h2>
    <?php if (!empty($error)): ?><p><?= $error ?></p><?php endif; ?>

    <form method="post">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Pricing Type:</label><br>
        <input type="radio" name="pricing_type" value="per_job" checked> Per Job
        <input type="radio" name="pricing_type" value="per_hour"> Per Hour<br><br>

        <label>Price (SGD):</label><br>
        <input type="number" step="0.01" name="price" required><br><br>

        <label>Category:</label><br>
        <select name="category_id" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Add Service</button>
    </form>

    <p><a href="dashboard_cleaner.php">← Back</a></p>
</body>
</html>

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
<head>
    <title>Add New Service</title>
    <link rel="stylesheet" href="../css/add_service.css">
    <link rel="stylesheet" href="../css/layout.css">
</head>
<body>
<div class="dashboard-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">HomeCleaners</div>
        <nav class="sidebar-links">
            <a href="dashboard_cleaner.php">Dashboard</a>
            <a href="add_service.php" class="active">Add Service</a>
            <a href="manage_services.php">My Services</a>
            <a href="view_cleaner_bookings.php">Bookings</a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">
        <div class="form-container">
            <h2>Create New Service</h2>

            <?php if (!empty($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <form method="post">
                <label>Title:</label>
                <input type="text" name="title" required>

                <label>Description:</label>
                <textarea name="description" required></textarea>

                <label>Pricing Type:</label>
                <div class="radio-group">
                    <input type="radio" name="pricing_type" value="per_job" checked> Per Job
                    <input type="radio" name="pricing_type" value="per_hour"> Per Hour
                </div>

                <label>Price (SGD):</label>
                <input type="number" step="0.01" name="price" required>

                <label>Category:</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Add Service</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>

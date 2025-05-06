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
<head>
    <title>Edit Service</title>
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/edit_service.css">
</head>
<body>
<div class="dashboard-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">HomeCleaners</div>
        <nav class="sidebar-links">
            <a href="dashboard_cleaner.php">Dashboard</a>
            <a href="add_service.php">Add Service</a>
            <a href="manage_services.php" class="active">My Services</a>
            <a href="view_cleaner_bookings.php">Bookings</a>
            <a href="../logout.php" class="logout-link">Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">
        <h2>Edit Service</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="post" class="form-box">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($service['title']) ?>" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?= htmlspecialchars($service['description']) ?></textarea>

            <label>Pricing Type:</label>
            <div class="radio-group">
                <label><input type="radio" name="pricing_type" value="per_job" <?= $service['pricing_type'] === 'per_job' ? 'checked' : '' ?>> Per Job</label>
                <label><input type="radio" name="pricing_type" value="per_hour" <?= $service['pricing_type'] === 'per_hour' ? 'checked' : '' ?>> Per Hour</label>
            </div>

            <label for="price">Price (SGD):</label>
            <input type="number" name="price" id="price" value="<?= $service['price'] ?>" step="0.01" required>

            <label>Category:</label>
            <input type="text" value="<?= htmlspecialchars($service['category_name']) ?>" readonly>

            <button type="submit" class="btn btn-green">Update</button>
        </form>
		
		<a href="manage_services.php">Return to Services</a>
		
    </main>
</div>
</body>
</html>

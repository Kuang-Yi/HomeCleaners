<?php
session_start();
require_once '../config/db.php';
require_once '../control/PlatformController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'P') {
    header('Location: login.php');
    exit();
}

$message = '';
$categories = PlatformController::getAllCategoriesWithServiceCount();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['category_name'])) {
        $name = trim($_POST['category_name']);
        if (PlatformController::addCategory($name)) {
            $message = "Category '$name' added successfully.";
        } else {
            $message = "Failed to add category. It may already exist.";
        }
        $categories = PlatformController::getAllCategoriesWithServiceCount(); // Refresh list
    } elseif (!empty($_POST['delete_category_id'])) {
        $id = $_POST['delete_category_id'];
        if (PlatformController::deleteCategory($id)) {
            $message = "Category deleted successfully.";
        } else {
            $message = "Failed to delete category.";
        }
        $categories = PlatformController::getAllCategoriesWithServiceCount(); // Refresh list
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Cleaning Categories</title>
    <link rel="stylesheet" href="../css/manage_categories.css">
</head>
<body>
<div class="container">
    <h2>Manage Cleaning Categories</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add category form -->
    <form method="post">
        <label>Category Name:</label>
        <input type="text" name="category_name" required>
        <button type="submit">Add Category</button>
    </form>

    <!-- Existing categories list -->
    <h3>Existing Categories</h3>
    <?php if (!empty($categories)): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Services Using</th>
                <th>Action</th>
            </tr>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><?= $cat['service_count'] ?></td>
                    <td>
                        <?php if ($cat['service_count'] == 0): ?>
                            <form method="post" class="inline">
                                <input type="hidden" name="delete_category_id" value="<?= $cat['id'] ?>">
                                <button type="submit" onclick="return confirm('Delete this category?')">Delete</button>
                            </form>
                        <?php else: ?>
                            <span style="color: gray;">In use</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="alert">No categories found.</p>
    <?php endif; ?>

    <p><a href="dashboard_platform.php">← Back to Dashboard</a></p>
</div>
</body>
</html>

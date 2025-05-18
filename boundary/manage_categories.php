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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['category_name'])) {
        $name = trim($_POST['category_name']);
        if (PlatformController::addCategory($name)) {
            $message = "Category '$name' added successfully.";
        } else {
            $message = "Failed to add category. It may already exist.";
        }
        $categories = PlatformController::getAllCategoriesWithServiceCount();
    } elseif (!empty($_POST['edit_category_id'])) {
        $id = $_POST['edit_category_id'];
        $newName = trim($_POST['new_category_name'] ?? '');
    
        if (empty($newName)) {
            $message = "Name is required.";
        } else {
            $result = PlatformController::updateCategory($id, $newName);
            if ($result === true) {
                $message = "Category updated successfully.";
            } else {
                $message = $result;
            }
        }
        $categories = PlatformController::getAllCategoriesWithServiceCount();
    } elseif (!empty($_POST['delete_category_id'])) {
        $id = $_POST['delete_category_id'];
        if (PlatformController::deleteCategory($id)) {
            $message = "Category deleted successfully.";
        } else {
            $message = "Failed to delete category. It may be in use.";
        }
        $categories = PlatformController::getAllCategoriesWithServiceCount();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/manage_categories.css">
</head>
<body>
<div class="container">
    <h2>Manage Categories</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="category_name" placeholder="New category name" required>
        <button type="submit">Add Category</button>
    </form>

    <h3>Existing Categories</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Name</th>
            <th># of Services</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($categories as $c): ?>
            <tr>
                <td>
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="edit_category_id" value="<?= $c['id'] ?>">
                        <input type="text" name="new_category_name" value="<?= htmlspecialchars($c['name']) ?>" required>
                        <button type="submit">Update</button>
                    </form>
                </td>
                <td><?= $c['service_count'] ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Delete this category? This cannot be undone.');" style="display:inline-block;">
                        <input type="hidden" name="delete_category_id" value="<?= $c['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="dashboard_platform.php">← Back to Dashboard</a></p>
</div>
</body>
</html>
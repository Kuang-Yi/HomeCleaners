<?php
session_start();
require_once '../config/db.php';

// ✅ Only allow Platform Managers
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'P') {
    header('Location: login.php');
    exit();
}

$message = "";

// ✅ Handle adding a new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $category_name = trim($_POST['category_name']);

    if ($category_name !== "") {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$category_name]);
            $message = "✅ Category added successfully.";
        } catch (PDOException $e) {
            $message = ($e->getCode() == 23000)
                ? "⚠️ Category already exists."
                : "❌ Error: " . $e->getMessage();
        }
    } else {
        $message = "❌ Category name cannot be empty.";
    }
}

// ✅ Handle deleting a category (only if unused)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category_id'])) {
    $category_id = intval($_POST['delete_category_id']);

    // Check if category is used in any services
    $check = $pdo->prepare("SELECT COUNT(*) FROM services WHERE category_id = ?");
    $check->execute([$category_id]);
    $count = $check->fetchColumn();

    if ($count == 0) {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        $message = "✅ Category deleted.";
    } else {
        $message = "❌ Cannot delete category — it is used by $count service(s).";
    }
}

// ✅ Fetch all categories with service count
$query = "
    SELECT c.id, c.name, COUNT(s.id) AS service_count
    FROM categories c
    LEFT JOIN services s ON c.id = s.category_id
    GROUP BY c.id, c.name
    ORDER BY c.name
";
$stmt = $pdo->query($query);
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Cleaning Categories</title>
</head>
<body>
    <h2>Manage Cleaning Categories</h2>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Add category form -->
    <form method="post">
        Category Name:
        <input type="text" name="category_name" required>
        <button type="submit">Add Category</button>
    </form>

    <!-- Existing categories list -->
    <h3>Existing Categories</h3>
    <table border="1" cellpadding="8">
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
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_category_id" value="<?= $cat['id'] ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                        </form>
                    <?php else: ?>
                        <span style="color:gray;">In use</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="dashboard_platform.php">← Back to Dashboard</a></p>
</body>
</html>

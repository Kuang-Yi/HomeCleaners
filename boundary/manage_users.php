<?php
session_start();
require_once '../config/db.php';
require_once '../control/AdminController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'A') {
    header('Location: login.php');
    exit();
}

$action = $_GET['action'] ?? '';
$edit_id = $_GET['edit'] ?? null;
$users = AdminController::getAllUsers();
$edit_user = $edit_id ? AdminController::getUserById($edit_id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        AdminController::createUser($_POST['email'], $_POST['password'], $_POST['user_type']);
        header("Location: manage_users.php");
        exit();
    }

    if (isset($_POST['update'])) {
        AdminController::updateUser($_POST['id'], $_POST['email'], $_POST['user_type']);
        header("Location: manage_users.php");
        exit();
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    AdminController::deleteUser($_GET['id']);
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="container">
    <h2>Manage Users</h2>

    <h3><?= $edit_user ? 'Edit User' : 'Create New User' ?></h3>
    <form method="post">
        <?php if ($edit_user): ?>
            <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
        <?php endif; ?>

        <label>Email</label>
        <input type="email" name="email" required placeholder="Email" value="<?= $edit_user['email'] ?? '' ?>">

        <?php if (!$edit_user): ?>
            <label>Password</label>
            <input type="password" name="password" required placeholder="Password">
        <?php endif; ?>

        <label>User Type</label>
        <select name="user_type" required>
            <option value="C" <?= (isset($edit_user) && $edit_user['user_type'] === 'C') ? 'selected' : '' ?>>Cleaner</option>
            <option value="H" <?= (isset($edit_user) && $edit_user['user_type'] === 'H') ? 'selected' : '' ?>>Homeowner</option>
        </select>

        <button type="submit" name="<?= $edit_user ? 'update' : 'create' ?>" class="btn">
            <?= $edit_user ? 'Update' : 'Create' ?>
        </button>
    </form>

    <h3 class="mt-20">Existing Users</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['user_type'] === 'C' ? 'Cleaner' : 'Homeowner' ?></td>
                    <td>
                        <a href="?edit=<?= $u['id'] ?>" class="btn">Edit</a>
                        <a href="?action=delete&id=<?= $u['id'] ?>" class="btn" onclick="return confirm('Delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="dashboard_admin.php" class="btn">← Back to Dashboard</a>
</div>
</body>
</html>

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
$message = '';

if (isset($_POST['export_users'])) {
    AdminController::exportUserReportPDF();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        AdminController::createUser($_POST['email'], $_POST['password'], $_POST['user_type']);
        header("Location: manage_users.php");
        exit();
    }

    if (isset($_POST['update'])) {
        $result = AdminController::updateUser($_POST['id'], $_POST['email'], $_POST['user_type']);
        if ($result === true) {
            header("Location: manage_users.php");
            exit();
        } else {
            $message = $result;
        }
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    AdminController::deleteUser($_GET['id']);
    header("Location: manage_users.php");
    exit();
}

if ($action === 'suspend' && isset($_GET['id'])) {
    AdminController::updateUserStatus($_GET['id'], 0);
    header("Location: manage_users.php");
    exit();
}

if ($action === 'unsuspend' && isset($_GET['id'])) {
    AdminController::updateUserStatus($_GET['id'], 1);
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

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h3><?= $edit_user ? 'Edit User' : 'Create New User' ?></h3>
    <form method="post">
        <?php if ($edit_user): ?>
            <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
        <?php endif; ?>
        <input type="email" name="email" required placeholder="Email" value="<?= $edit_user['email'] ?? '' ?>"><br>
        <?php if (!$edit_user): ?>
            <input type="password" name="password" required placeholder="Password"><br>
        <?php endif; ?>
        <select name="user_type" required>
            <option value="C" <?= (isset($edit_user) && $edit_user['user_type'] === 'C') ? 'selected' : '' ?>>Cleaner</option>
            <option value="H" <?= (isset($edit_user) && $edit_user['user_type'] === 'H') ? 'selected' : '' ?>>Homeowner</option>
        </select><br>
        <button type="submit" name="<?= $edit_user ? 'update' : 'create' ?>">
            <?= $edit_user ? 'Update' : 'Create' ?>
        </button>
    </form>

    <form method="post" style="margin-top: 20px;">
        <button type="submit" name="export_users">Export All Users</button>
    </form>

    <h3>Existing Users</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>User Type</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= $u['user_type'] === 'C' ? 'Cleaner' : 'Homeowner' ?></td>
                <td><?= $u['account_status'] == 1 ? 'Active' : 'Suspended' ?></td>
                <td>
                    <a href="?edit=<?= $u['id'] ?>">Edit</a> |
                    <a href="?action=delete&id=<?= $u['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a> |
                    <?php if ($u['account_status'] == 1): ?>
                        <a href="?action=suspend&id=<?= $u['id'] ?>" onclick="return confirm('Suspend this account?')">Suspend</a>
                    <?php else: ?>
                        <a href="?action=unsuspend&id=<?= $u['id'] ?>" onclick="return confirm('Unsuspend this account?')">Unsuspend</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="dashboard_admin.php">← Back to Dashboard</a></p>
	</div>
</body>
</html>

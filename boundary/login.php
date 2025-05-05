<?php
require_once '../config/db.php';
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

        // Redirect based on user type
        switch ($user['user_type']) {
            case 'C':
                header('Location: dashboard_cleaner.php');
                break;
            case 'H':
                header('Location: dashboard_homeowner.php');
                break;
            case 'A':
                header('Location: dashboard_admin.php');
                break;
            case 'P':
                header('Location: dashboard_platform.php');
                break;
            default:
                $message = "❌ Invalid user role.";
        }
        exit();
    } else {
        $message = "❌ Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>
</body>
</html>

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-container">
        <a href="../index.php" class="back-btn">← Back to Home</a>

        <div class="login-card">
            <h2>Log In</h2>

            <?php if ($message): ?>
                <div class="error"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn-submit">Log In</button>
                <a href="register.php" class="register-link">Don’t have an account? Register</a>
            </form>

            <a href="#" class="forgot-link">Forgot Password?</a>
        </div>
    </div>
</body>
</html>

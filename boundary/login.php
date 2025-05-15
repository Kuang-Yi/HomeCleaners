<?php
require_once '../control/AuthController.php';
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $result = AuthController::login($email, $password);

    if ($result === "suspended") {
        $message = "⚠️ This account is suspended. Please contact admin@gmail.com.";
    } elseif (is_array($result)) {
        $_SESSION['user'] = $result;

        switch ($result['user_type']) {
            case 'C': header('Location: dashboard_cleaner.php'); break;
            case 'H': header('Location: dashboard_homeowner.php'); break;
            case 'A': header('Location: dashboard_admin.php'); break;
            case 'P': header('Location: dashboard_platform.php'); break;
            default: $message = "❌ Invalid user role."; exit();
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

            <?php if (!empty($message)): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn-submit">Log In</button>
                <a href="register.php" class="register-link">Don’t have an account? Register</a>
            </form>

        </div>
    </div>
</body>


</html>

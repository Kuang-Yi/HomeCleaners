<?php
require_once '../control/AuthController.php';
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    $message = AuthController::register($email, $password, $user_type);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>
<div class="register-container">
    <a href="../index.php" class="back-btn">← Back to Home</a>

    <div class="register-card">
        <h2>Sign Up</h2>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="user_type" required>
                <option value="" disabled selected>Select Role</option>
                <option value="C">Cleaner</option>
                <option value="H">Homeowner</option>
            </select>
            <button type="submit" class="btn-submit">Create Account</button>
        </form>

        <a href="login.php" class="login-link">Already have an account? Login</a>
    </div>
</div>

</body>
</html>

<?php
require_once '../config/db.php';
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_type = $_POST['user_type'];

    // Only allow 'C' or 'H'
    if (!in_array($user_type, ['C', 'H'])) {
        $message = "❌ Invalid user type.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (email, password, user_type) VALUES (?, ?, ?)");
            $stmt->execute([$email, $password, $user_type]);
            $message = "✅ Registration successful. <a href='login.php'>Login here</a>";
        } catch (PDOException $e) {
            $message = ($e->getCode() == 23000) ? "⚠️ Email already registered." : "❌ Error: " . $e->getMessage();
        }
    }
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

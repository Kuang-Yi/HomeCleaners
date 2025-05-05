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
</head>
<body>
    <h2>Register</h2>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        Account Type:
        <select name="user_type" required>
            <option value="C">Cleaner</option>
            <option value="H">Homeowner</option>
        </select><br><br>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</body>
</html>

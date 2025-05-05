<?php
session_start();

// Redirect logged-in users
if (isset($_SESSION['user'])) {
    switch ($_SESSION['user']['user_type']) {
        case 'C': header('Location: boundary/dashboard_cleaner.php'); break;
        case 'H': header('Location: boundary/dashboard_homeowner.php'); break;
        case 'A': header('Location: boundary/dashboard_admin.php'); break;
        case 'P': header('Location: boundary/dashboard_platform.php'); break;
        default: echo "❌ Unknown user role."; exit();
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Cleaners Platform</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

    <header class="hero">
        <h1>Welcome to HomeCleaners</h1>
        <p>Connecting home owners with reliable freelance cleaners, easily and securely.</p>
        <div class="hero-buttons">
            <a href="boundary/register.php" class="btn">Sign Up</a>
            <a href="boundary/login.php" class="btn">Login</a>
        </div>
    </header>

    <section class="features">
        <div class="feature-card">
            <h3>🔍 Search & Match</h3>
            <p>Find cleaners based on services, ratings, and availability.</p>
        </div>
        <div class="feature-card">
            <h3>🧾 Manage Services</h3>
            <p>Cleaners can list, track, and manage their services and bookings.</p>
        </div>
        <div class="feature-card">
            <h3>📊 View History & Insights</h3>
            <p>Track who viewed you, who shortlisted you, and your past matches.</p>
        </div>
    </section>

    <footer class="footer">
        &copy; 2025 HomeCleaners | CSIT314 GP02
    </footer>

</body>
</html>

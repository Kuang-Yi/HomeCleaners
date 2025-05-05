<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page
header('Location: boundary/login.php');
exit();

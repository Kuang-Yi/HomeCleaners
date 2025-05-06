<?php
session_start();
require_once '../config/db.php';
require_once '../control/CleanerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'C') {
    header('Location: login.php');
    exit();
}

$cleaner_id = $_SESSION['user']['id'];
$service_id = $_GET['id'] ?? null;

if (!$service_id) {
    header('Location: manage_services.php');
    exit();
}

// Verify ownership of the service
$service = CleanerController::getServiceById($service_id, $cleaner_id);
if (!$service) {
    echo "<p>Invalid request: service not found or does not belong to you.</p>";
    echo "<p><a href='manage_services.php'>Go back</a></p>";
    exit();
}

// Proceed with deletion
CleanerController::deleteService($service_id, $cleaner_id);

// Redirect back to service management page
header('Location: manage_services.php');
exit();

<?php
session_start();
require_once '../config/db.php';
require_once '../control/HomeownerController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'H') {
    header('Location: login.php');
    exit();
}

$homeowner_id = $_SESSION['user']['id'];
$service_id = $_POST['service_id'] ?? null;

if ($service_id) {
    HomeownerController::shortlistService($homeowner_id, $service_id);
}

header('Location: view_services.php');
exit();

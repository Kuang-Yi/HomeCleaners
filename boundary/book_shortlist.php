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
$datetime = $_POST['booking_datetime'] ?? null;

if ($service_id && $datetime) {
    HomeownerController::bookService($homeowner_id, $service_id, $datetime);
}

header('Location: view_shortlist.php');
exit();

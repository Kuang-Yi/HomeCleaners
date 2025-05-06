<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../entity/Booking.php';
require_once __DIR__ . '/../entity/Service.php';

class CleanerController {
    public static function updateBookingStatus($cleaner_id, $booking_id, $status) {
        global $pdo;
        return Booking::updateStatus($pdo, $booking_id, $status, $cleaner_id);
    }

    public static function getBookings($cleaner_id) {
        global $pdo;
        return Booking::getByCleaner($pdo, $cleaner_id);
    }

    public static function getCleanerServices($cleaner_id) {
        global $pdo;
        return Service::getByCleaner($pdo, $cleaner_id);
    }

    public static function getServiceById($service_id, $cleaner_id) {
    global $pdo;
    return Service::getById($pdo, $service_id, $cleaner_id);
    }


    public static function updateService($cleaner_id, $service_id, $title, $desc, $pricing_type, $price) {
        global $pdo;
        return Service::update($pdo, $service_id, $cleaner_id, $title, $desc, $pricing_type, $price);
    }

    public static function deleteServiceById($cleaner_id, $service_id) {
        global $pdo;
        return Service::delete($pdo, $service_id, $cleaner_id);
    }
	
	public static function addService($cleaner_id, $category_id, $title, $description, $pricing_type, $price) {
    global $pdo;
    return Service::create($pdo, $cleaner_id, $category_id, $title, $description, $pricing_type, $price);
    }

    public static function deleteService($service_id, $cleaner_id) {
    global $pdo;
    return Service::delete($pdo, $service_id, $cleaner_id);
    }

}

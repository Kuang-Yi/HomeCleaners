<?php
require_once __DIR__ . '/../entity/Service.php';
require_once __DIR__ . '/../config/db.php';

class ServiceController {

    // Handle adding a new service
    public static function handleAddService($formData, $userId) {
        global $pdo;

        // Clean and validate data
        $title = trim($formData['title']);
        $description = trim($formData['description']);
        $price = floatval($formData['price']);
        $category_id = intval($formData['category_id']);
        $pricing_type = $formData['pricing_type'];

        // Optionally add further validation here

        // Call entity method to insert into DB
        return Service::create(
            $pdo,
            $userId,
            $title,
            $description,
            $price,
            $category_id,
            $pricing_type
        );
    }

    // Handle fetching all services for a cleaner (for "View My Services")
    public static function getServicesByCleaner($cleanerId) {
        global $pdo;
        return Service::getByCleaner($pdo, $cleanerId);
    }

    // (Optional) Handle deleting a service (future functionality)
    public static function deleteService($serviceId, $cleanerId) {
        global $pdo;
        return Service::deleteByCleaner($pdo, $serviceId, $cleanerId);
    }
}

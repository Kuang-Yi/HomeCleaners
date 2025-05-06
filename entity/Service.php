<?php
class Service {
    // Cleaner: Get all services by this cleaner
    public static function getByCleaner($pdo, $cleaner_id) {
        $stmt = $pdo->prepare("
            SELECT s.*, c.name AS category_name, s.view_count, s.shortlist_count
            FROM services s
            JOIN categories c ON s.category_id = c.id
            WHERE s.cleaner_id = ?
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$cleaner_id]);
        return $stmt->fetchAll();
    }

    // Cleaner: Get a specific service by ID and cleaner
    public static function getById($pdo, $service_id, $cleaner_id) {
    $stmt = $pdo->prepare("
        SELECT s.*, c.name AS category_name
        FROM services s
        JOIN categories c ON s.category_id = c.id
        WHERE s.id = ? AND s.cleaner_id = ?
    ");
    $stmt->execute([$service_id, $cleaner_id]);
    return $stmt->fetch();
}



    // Cleaner: Update service details
    public static function update($pdo, $service_id, $cleaner_id, $title, $description, $pricing_type, $price) {
        $stmt = $pdo->prepare("
            UPDATE services
            SET title = ?, description = ?, pricing_type = ?, price = ?
            WHERE id = ? AND cleaner_id = ?
        ");
        return $stmt->execute([$title, $description, $pricing_type, $price, $service_id, $cleaner_id]);
    }

    // Cleaner: Delete a service
    public static function delete($pdo, $service_id, $cleaner_id) {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ? AND cleaner_id = ?");
        return $stmt->execute([$service_id, $cleaner_id]);
    }

    // Cleaner: Create a new service
    public static function create($pdo, $cleaner_id, $category_id, $title, $description, $pricing_type, $price) {
        $stmt = $pdo->prepare("
            INSERT INTO services (cleaner_id, category_id, title, description, pricing_type, price)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$cleaner_id, $category_id, $title, $description, $pricing_type, $price]);
    }

    // Homeowner: Get all services with category name
    public static function getAllWithCategory($pdo) {
        $stmt = $pdo->query("
            SELECT s.*, c.name AS category_name
            FROM services s
            JOIN categories c ON s.category_id = c.id
        ");
        return $stmt->fetchAll();
    }

    // Homeowner: Get full details of a service
    public static function getDetailsById($pdo, $service_id) {
        $stmt = $pdo->prepare("
            SELECT s.*, c.name AS category_name, u.email AS cleaner_email
            FROM services s
            JOIN categories c ON s.category_id = c.id
            JOIN users u ON s.cleaner_id = u.id
            WHERE s.id = ?
        ");
        $stmt->execute([$service_id]);
        return $stmt->fetch();
    }

    // Increment view count
    public static function incrementViewCount($pdo, $service_id) {
        $stmt = $pdo->prepare("UPDATE services SET view_count = view_count + 1 WHERE id = ?");
        return $stmt->execute([$service_id]);
    }
	
	public static function searchWithCategory($pdo, $search) {
    $stmt = $pdo->prepare("
        SELECT s.*, c.name AS category_name, u.email AS cleaner_email
        FROM services s
        JOIN categories c ON s.category_id = c.id
        JOIN users u ON s.cleaner_id = u.id
        WHERE s.title LIKE :search
           OR s.description LIKE :search
           OR c.name LIKE :search
           OR u.email LIKE :search
    ");
    $stmt->execute(['search' => '%' . $search . '%']);
    return $stmt->fetchAll();
    }

}

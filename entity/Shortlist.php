<?php
class Shortlist {
    // Get all services shortlisted by a homeowner
    public static function getAllByHomeowner($pdo, $homeowner_id) {
        $stmt = $pdo->prepare("
            SELECT s.*, c.name AS category_name, u.email AS cleaner_email
            FROM shortlists sh
            JOIN services s ON sh.service_id = s.id
            JOIN categories c ON s.category_id = c.id
            JOIN users u ON s.cleaner_id = u.id
            WHERE sh.homeowner_id = ?
        ");
        $stmt->execute([$homeowner_id]);
        return $stmt->fetchAll();
    }

    // Add a service to the homeowner's shortlist (and update counter)
    public static function add($pdo, $homeowner_id, $service_id) {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO shortlists (homeowner_id, service_id)
            VALUES (?, ?)
        ");
        $success = $stmt->execute([$homeowner_id, $service_id]);

        // Only increment count if a new row was added
        if ($success && $stmt->rowCount() > 0) {
            $pdo->prepare("UPDATE services SET shortlist_count = shortlist_count + 1 WHERE id = ?")
                ->execute([$service_id]);
        }

        return $success;
    }

    // Remove a service from the shortlist
    public static function remove($pdo, $homeowner_id, $service_id) {
        $stmt = $pdo->prepare("
            DELETE FROM shortlists
            WHERE homeowner_id = ? AND service_id = ?
        ");
        return $stmt->execute([$homeowner_id, $service_id]);
    }
}

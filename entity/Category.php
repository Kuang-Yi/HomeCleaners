<?php
class Category {
    public static function create($pdo, $name) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public static function getAll($pdo) {
        $stmt = $pdo->query("
            SELECT c.*, COUNT(s.id) AS service_count
            FROM categories c
            LEFT JOIN services s ON c.id = s.category_id
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll();
    }

    public static function deleteIfEmpty($pdo, $category_id) {
        $stmt = $pdo->prepare("
            DELETE FROM categories
            WHERE id = ?
            AND NOT EXISTS (
                SELECT 1 FROM services WHERE category_id = ?
            )
        ");
        return $stmt->execute([$category_id, $category_id]);
    }
}

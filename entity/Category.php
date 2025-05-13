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
	
	public static function getAllWithServiceCount($pdo) {
        $stmt = $pdo->query("
            SELECT c.*, 
            (SELECT COUNT(*) FROM services s WHERE s.category_id = c.id) AS service_count
            FROM categories c ORDER BY c.name ASC
        ");
        return $stmt->fetchAll();
    }

    public static function add($pdo, $name) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public static function delete($pdo, $id) {
        // Only delete if no services use it
        $check = $pdo->prepare("SELECT COUNT(*) FROM services WHERE category_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() == 0) {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            return $stmt->execute([$id]);
        }
        return false;
    }
}

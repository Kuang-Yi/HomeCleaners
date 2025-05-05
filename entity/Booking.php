<?php
class Booking {
    public static function create($pdo, $homeowner_id, $service_id, $datetime) {
        $stmt = $pdo->prepare("INSERT INTO bookings (homeowner_id, service_id, booking_datetime, status) VALUES (?, ?, ?, 'confirmed')");
        return $stmt->execute([$homeowner_id, $service_id, $datetime]);
    }

    public static function updateStatus($pdo, $booking_id, $status, $cleaner_id) {
        $stmt = $pdo->prepare("
            UPDATE bookings SET status = ?
            WHERE id = ? AND service_id IN (
                SELECT id FROM services WHERE cleaner_id = ?
            )
        ");
        return $stmt->execute([$status, $booking_id, $cleaner_id]);
    }

    public static function getByCleaner($pdo, $cleaner_id) {
        $stmt = $pdo->prepare("
            SELECT b.*, s.title, s.price, s.pricing_type, c.name AS category_name,
                   u.email AS homeowner_email
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            JOIN categories c ON s.category_id = c.id
            JOIN users u ON b.homeowner_id = u.id
            WHERE s.cleaner_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$cleaner_id]);
        return $stmt->fetchAll();
    }

    public static function getByHomeowner($pdo, $homeowner_id) {
        $stmt = $pdo->prepare("
            SELECT b.*, s.title, s.price, s.pricing_type, c.name AS category_name,
                   u.email AS cleaner_email
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            JOIN categories c ON s.category_id = c.id
            JOIN users u ON s.cleaner_id = u.id
            WHERE b.homeowner_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$homeowner_id]);
        return $stmt->fetchAll();
    }
	
	public static function getDistinctTitles($pdo, $homeowner_id) {
    $stmt = $pdo->prepare("
        SELECT DISTINCT s.title
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        WHERE b.homeowner_id = ?
    ");
    $stmt->execute([$homeowner_id]);
    return $stmt->fetchAll();
    }

    public static function filter($pdo, $homeowner_id, $service, $start, $end) {
    $query = "
        SELECT b.*, s.title, s.pricing_type, s.price, c.name AS category_name, u.email AS cleaner_email
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users u ON s.cleaner_id = u.id
        JOIN categories c ON s.category_id = c.id
        WHERE b.homeowner_id = ?
    ";

    $params = [$homeowner_id];

    if (!empty($service)) {
        $query .= " AND s.title LIKE ?";
        $params[] = '%' . $service . '%';
    }

    if (!empty($start)) {
        $query .= " AND b.booking_datetime >= ?";
        $params[] = $start . ' 00:00:00';
    }

    if (!empty($end)) {
        $query .= " AND b.booking_datetime <= ?";
        $params[] = $end . ' 23:59:59';
    }

    $query .= " ORDER BY b.booking_datetime DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}


}

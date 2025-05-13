<?php
require_once '../config/db.php';

class Report {
    public static function getAvailableBookingPeriods() {
        global $pdo;
        $days = $pdo->query("
            SELECT DATE(booking_datetime) AS day
            FROM bookings
            GROUP BY day
            HAVING COUNT(*) > 0
            ORDER BY day DESC
        ")->fetchAll(PDO::FETCH_COLUMN);

        $weeks = $pdo->query("
            SELECT DATE_FORMAT(booking_datetime, '%x-W%v') AS week
            FROM bookings
            GROUP BY week
            HAVING COUNT(*) > 0
            ORDER BY week DESC
        ")->fetchAll(PDO::FETCH_COLUMN);

        $months = $pdo->query("
            SELECT DATE_FORMAT(booking_datetime, '%Y-%m') AS month
            FROM bookings
            GROUP BY month
            HAVING COUNT(*) > 0
            ORDER BY month DESC
        ")->fetchAll(PDO::FETCH_COLUMN);

        return [
            'daily' => $days,
            'weekly' => $weeks,
            'monthly' => $months,
        ];
    }

    public static function fetchBookingsByPeriod($type, $value) {
        global $pdo;
        switch ($type) {
            case 'daily':
                $start = $value . " 00:00:00";
                $end = $value . " 23:59:59";
                break;
            case 'weekly':
                [$year, $week] = explode('-W', $value);
                $start = date('Y-m-d', strtotime($year . "W" . $week));
                $end = date('Y-m-d', strtotime($start . " +6 days"));
                break;
            case 'monthly':
                $start = $value . "-01 00:00:00";
                $end = date("Y-m-t", strtotime($value)) . " 23:59:59";
                break;
            default:
                return [];
        }

        $stmt = $pdo->prepare("
            SELECT b.*, 
                   s.title, s.pricing_type, s.price, 
                   c.name AS category_name, 
                   u.email AS cleaner_email, 
                   h.email AS homeowner_email
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            JOIN categories c ON s.category_id = c.id
            JOIN users u ON s.cleaner_id = u.id
            JOIN users h ON b.homeowner_id = h.id
            WHERE b.booking_datetime BETWEEN ? AND ?
            ORDER BY b.booking_datetime DESC
        ");
        $stmt->execute([$start, $end]);
        return $stmt->fetchAll();
    }
}

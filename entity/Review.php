<?php
class Review {
    // Create a new review
    public static function create($pdo, $bookingId, $rating, $comment) {
        $stmt = $pdo->prepare("INSERT INTO reviews (booking_id, rating, comment) VALUES (?, ?, ?)");
        return $stmt->execute([$bookingId, $rating, $comment]);
    }

    // Check if a review already exists for the given booking
    public static function existsForBooking($pdo, $bookingId) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE booking_id = ?");
        $stmt->execute([$bookingId]);
        return $stmt->fetchColumn() > 0;
    }

    // Get all reviews for services owned by a given cleaner
    public static function getByCleanerId($pdo, $cleanerId) {
        $stmt = $pdo->prepare("
            SELECT r.*, b.service_id, s.title, u.email AS homeowner_email
            FROM reviews r
            JOIN bookings b ON r.booking_id = b.id
            JOIN services s ON b.service_id = s.id
            JOIN users u ON b.homeowner_id = u.id
            WHERE s.cleaner_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$cleanerId]);
        return $stmt->fetchAll();
    }
}

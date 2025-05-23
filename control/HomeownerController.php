<?php
require_once __DIR__ . '/../entity/Service.php';
require_once __DIR__ . '/../entity/Shortlist.php';
require_once __DIR__ . '/../entity/Booking.php';
require_once __DIR__ . '/../entity/Review.php';

class HomeownerController {
    // Get all available services
    public static function getAllServices() {
        global $pdo;
        return Service::getAllWithCategory($pdo);
    }

    // Get details for a specific service (optional for future use)
    public static function getServiceDetails($service_id) {
        global $pdo;
        return Service::getDetailsById($pdo, $service_id);
    }

    // Shortlist a service
    public static function shortlistService($homeowner_id, $service_id) {
    global $pdo;
    return Shortlist::add($pdo, $homeowner_id, $service_id);
    }


    // Book a service
    public static function bookService($homeowner_id, $service_id, $datetime) {
        global $pdo;
        return Booking::create($pdo, $homeowner_id, $service_id, $datetime);
    }

    // Cancel an existing booking
    public static function cancelBooking($homeowner_id, $booking_id) {
        global $pdo;
        return Booking::cancel($pdo, $homeowner_id, $booking_id);
    }

    // Get all bookings by the homeowner
    public static function getBookings($homeowner_id) {
        global $pdo;
        return Booking::getByHomeowner($pdo, $homeowner_id);
    }

    // Get all shortlisted services
    public static function getShortlistedServices($homeowner_id) {
        global $pdo;
        return Shortlist::getAllByHomeowner($pdo, $homeowner_id);
    }

    // Remove a service from the shortlist
    public static function removeFromShortlist($homeowner_id, $service_id) {
        global $pdo;
        return Shortlist::remove($pdo, $homeowner_id, $service_id);
    }

    // Increment the view count for a service
    public static function incrementServiceView($service_id) {
        global $pdo;
        return Service::incrementViewCount($pdo, $service_id);
    }
	
	public static function searchServices($search) {
    global $pdo;
    return Service::searchWithCategory($pdo, $search);
}

    public static function filterBookings($homeowner_id, $service, $start, $end) {
    global $pdo;
    return Booking::filter($pdo, $homeowner_id, $service, $start, $end);
}

    public static function getBookingServiceOptions($homeowner_id) {
    global $pdo;
    return Booking::getDistinctTitles($pdo, $homeowner_id);
}

    public static function removeShortlist($homeowner_id, $service_id) {
    global $pdo;
    return Shortlist::remove($pdo, $homeowner_id, $service_id);
}

    // ✅ NEW: Get completed bookings that haven't been reviewed
    public static function getCompletedBookingsWithoutReview($homeownerId) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT b.id, s.title, b.booking_datetime
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            WHERE b.homeowner_id = ?
              AND b.status = 'completed'
              AND NOT EXISTS (
                  SELECT 1 FROM reviews r WHERE r.booking_id = b.id
              )
            ORDER BY b.booking_datetime DESC
        ");
        $stmt->execute([$homeownerId]);
        return $stmt->fetchAll();
    }

    // ✅ NEW: Submit a review
    public static function submitReview($bookingId, $rating, $comment) {
        global $pdo;
        if (Review::existsForBooking($pdo, $bookingId)) {
            return "Review already exists for this booking.";
        }
        return Review::create($pdo, $bookingId, $rating, $comment);
    }

}

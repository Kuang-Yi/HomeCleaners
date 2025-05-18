<?php
require_once '../config/db.php';
require_once '../lib/fpdf/fpdf.php';

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


    public static function generatePDF($type, $value, $bookings) {
        require_once '../lib/fpdf/fpdf.php';
    
        $pdf = new FPDF('L', 'mm', 'A4'); // Landscape mode
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Booking Report - ' . ucfirst($type) . ' (' . $value . ')', 0, 1, 'C');
    
        // Define headers and column widths
        $headers = ['Service', 'Cleaner Email', 'Homeowner Email', 'Category', 'Price', 'Pricing', 'Status', 'Order Date & Time'];
        $widths  = [32,       45,              45,                32,         20,      25,        25,        58];
    
        // Header row
        $pdf->SetFont('Arial', 'B', 9);
        foreach ($headers as $i => $h) {
            $pdf->Cell($widths[$i], 10, $h, 1);
        }
        $pdf->Ln();
    
        // Data rows
        $pdf->SetFont('Arial', '', 9);
        foreach ($bookings as $b) {
            $pdf->Cell($widths[0], 10, substr($b['title'], 0, 30), 1);
            $pdf->Cell($widths[1], 10, substr($b['cleaner_email'], 0, 40), 1);
            $pdf->Cell($widths[2], 10, substr($b['homeowner_email'], 0, 40), 1);
            $pdf->Cell($widths[3], 10, $b['category_name'], 1);
            $pdf->Cell($widths[4], 10, number_format($b['price'], 2), 1);
            $pdf->Cell($widths[5], 10, $b['pricing_type'], 1);
            $pdf->Cell($widths[6], 10, $b['status'], 1);
            $pdf->Cell($widths[7], 10, substr($b['booking_datetime'], 0, 16), 1);
            $pdf->Ln();
        }
    
        return $pdf;
    }
}

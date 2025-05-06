<?php
session_start();
require_once '../config/db.php';
require_once '../lib/fpdf/fpdf.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'P') {
    header('Location: login.php');
    exit();
}

function getAvailableBookingPeriods($pdo) {
    $stmt = $pdo->query("SELECT booking_datetime FROM bookings ORDER BY booking_datetime DESC");
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $days = [];
    $weeks = [];
    $months = [];

    foreach ($dates as $datetime) {
        $ts = strtotime($datetime);
        $days[] = date('Y-m-d', $ts);
        $weeks[] = date('o-\WW', $ts);
        $months[] = date('Y-m', $ts);
    }

    return [
        'daily' => array_unique($days),
        'weekly' => array_unique($weeks),
        'monthly' => array_unique($months),
    ];
}

function fetchBookingsByPeriod($pdo, $type, $value) {
    switch ($type) {
        case 'daily':
            $start = $value . " 00:00:00";
            $end = $value . " 23:59:59";
            break;
        case 'weekly':
            [$year, $week] = explode('-W', $value);
            $start = date('Y-m-d', strtotime($year . "W" . $week));
            $end = date('Y-m-d', strtotime($start . " +6 days"));
            $start .= " 00:00:00";
            $end .= " 23:59:59";
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['time_type'];
    $value = $_POST['time_value'];

    $bookings = fetchBookingsByPeriod($pdo, $type, $value);

    // Landscape mode
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,'Booking Report - '.ucfirst($type) . ' (' . $value . ')',0,1,'C');

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(40,10,'Service',1);
    $pdf->Cell(50,10,'Cleaner Email',1);
    $pdf->Cell(50,10,'Homeowner Email',1);
    $pdf->Cell(30,10,'Category',1);
    $pdf->Cell(20,10,'Price',1);
    $pdf->Cell(25,10,'Pricing',1);
    $pdf->Cell(55,10,'Order Date & Time',1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',9);
    foreach ($bookings as $b) {
        $pdf->Cell(40,10,substr($b['title'], 0, 30),1);
        $pdf->Cell(50,10,substr($b['cleaner_email'], 0, 40),1);
        $pdf->Cell(50,10,substr($b['homeowner_email'], 0, 40),1);
        $pdf->Cell(30,10,$b['category_name'],1);
        $pdf->Cell(20,10,$b['price'],1);
        $pdf->Cell(25,10,$b['pricing_type'],1);
        $pdf->Cell(55,10,substr($b['booking_datetime'], 0, 16),1);
        $pdf->Ln();
    }

    $filename = "Booking_Report_" . $type . "_" . str_replace(['-', 'W'], '_', $value) . ".pdf";
    $pdf->Output('D', $filename);
    exit();
}

$options = getAvailableBookingPeriods($pdo);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Booking Report</title>
    <script>
    function updateDropdown() {
        const selectedType = document.querySelector('input[name="time_type"]:checked').value;
        const allOptions = document.querySelectorAll('#time_value option');

        allOptions.forEach(opt => {
            opt.style.display = (opt.dataset.group === selectedType) ? 'block' : 'none';
        });

        const visible = Array.from(allOptions).find(opt => opt.style.display === 'block');
        if (visible) {
            document.getElementById('time_value').value = visible.value;
        }
    }

    window.onload = updateDropdown;
    </script>
</head>
<body>
    <h2>Generate Booking Report</h2>

    <form method="post">
        <label><input type="radio" name="time_type" value="daily" checked onclick="updateDropdown()"> Daily</label>
        <label><input type="radio" name="time_type" value="weekly" onclick="updateDropdown()"> Weekly</label>
        <label><input type="radio" name="time_type" value="monthly" onclick="updateDropdown()"> Monthly</label>
        <br><br>

        <label for="time_value">Select Period:</label><br>
        <select name="time_value" id="time_value" required>
            <?php foreach ($options['daily'] as $val): ?>
                <option data-group="daily" value="<?= $val ?>"><?= $val ?></option>
            <?php endforeach; ?>
            <?php foreach ($options['weekly'] as $val): ?>
                <option data-group="weekly" value="<?= $val ?>" style="display:none"><?= $val ?></option>
            <?php endforeach; ?>
            <?php foreach ($options['monthly'] as $val): ?>
                <option data-group="monthly" value="<?= $val ?>" style="display:none"><?= $val ?></option>
            <?php endforeach; ?>
        </select>

        <br><br>
        <button type="submit">Generate PDF Report</button>
    </form>

    <p><a href="dashboard_platform.php">← Back to Dashboard</a></p>
</body>
</html>

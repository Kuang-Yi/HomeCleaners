<?php
session_start();
require_once '../config/db.php';
require_once '../lib/fpdf/fpdf.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'P') {
    header('Location: login.php');
    exit();
}

// === Helper functions ===
function getAvailableBookingPeriods($pdo) {
    $days = [];
    $weeks = [];
    $months = [];

    // Group daily
    $stmt = $pdo->query("
        SELECT DATE(booking_datetime) AS day
        FROM bookings
        GROUP BY day
        HAVING COUNT(*) > 0
        ORDER BY day DESC
    ");
    $days = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Group weekly using ISO year-week
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(booking_datetime, '%x-W%v') AS week
        FROM bookings
        GROUP BY week
        HAVING COUNT(*) > 0
        ORDER BY week DESC
    ");
    $weeks = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Group monthly
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(booking_datetime, '%Y-%m') AS month
        FROM bookings
        GROUP BY month
        HAVING COUNT(*) > 0
        ORDER BY month DESC
    ");
    $months = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return [
        'daily' => $days,
        'weekly' => $weeks,
        'monthly' => $months,
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

// === Form handling ===
$previewBookings = [];
$options = getAvailableBookingPeriods($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['time_type'];
    $value = $_POST['time_value'];

    $previewBookings = fetchBookingsByPeriod($pdo, $type, $value);

    if (isset($_POST['download_pdf'])) {
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
        $pdf->Cell(30,10,'Status',1);
        $pdf->Cell(45,10,'Order Date & Time',1);
        $pdf->Ln();

        $pdf->SetFont('Arial','',9);
        foreach ($previewBookings as $b) {
            $pdf->Cell(40,10,substr($b['title'], 0, 30),1);
            $pdf->Cell(50,10,substr($b['cleaner_email'], 0, 40),1);
            $pdf->Cell(50,10,substr($b['homeowner_email'], 0, 40),1);
            $pdf->Cell(30,10,$b['category_name'],1);
            $pdf->Cell(20,10,$b['price'],1);
            $pdf->Cell(25,10,$b['pricing_type'],1);
            $pdf->Cell(30,10,$b['status'],1);
            $pdf->Cell(45,10,substr($b['booking_datetime'], 0, 16),1);
            $pdf->Ln();
        }

        $filename = "Booking_Report_" . $type . "_" . str_replace(['-', 'W'], '_', $value) . ".pdf";
        $pdf->Output('D', $filename);
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Booking Report</title>
    <link rel="stylesheet" href="../css/generate_report.css">
    <script>
        function updateDropdown() {
            const selectedType = document.querySelector('input[name="time_type"]:checked').value;
            const allOptions = document.querySelectorAll('#time_value option');

            allOptions.forEach(opt => {
                opt.style.display = (opt.dataset.group === selectedType) ? 'block' : 'none';
            });

            const firstVisible = Array.from(allOptions).find(opt => opt.style.display === 'block');
            if (firstVisible) {
                document.getElementById('time_value').value = firstVisible.value;
            }
        }

        window.onload = updateDropdown;
    </script>
</head>
<body>
<div class="container">
    <h2>Generate Booking Report</h2>

    <div class="notice">Select a period and time range to preview and download a report.</div>

    <form method="post">
        <label><input type="radio" name="time_type" value="daily" checked onclick="updateDropdown()"> Daily</label>
        <label><input type="radio" name="time_type" value="weekly" onclick="updateDropdown()"> Weekly</label>
        <label><input type="radio" name="time_type" value="monthly" onclick="updateDropdown()"> Monthly</label>
        <br><br>

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

        <button type="submit" name="preview">Preview Bookings</button>
        <?php if (!empty($previewBookings)): ?>
            <button type="submit" name="download_pdf">Download PDF</button>
        <?php endif; ?>
    </form>

    <?php if (!empty($previewBookings)): ?>
        <h3>Booking Preview</h3>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Service</th>
                <th>Cleaner Email</th>
                <th>Homeowner Email</th>
                <th>Category</th>
                <th>Price</th>
                <th>Pricing</th>
                <th>Status</th>
                <th>Order Date & Time</th>
            </tr>
            <?php foreach ($previewBookings as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['title']) ?></td>
                    <td><?= htmlspecialchars($b['cleaner_email']) ?></td>
                    <td><?= htmlspecialchars($b['homeowner_email']) ?></td>
                    <td><?= htmlspecialchars($b['category_name']) ?></td>
                    <td><?= number_format($b['price'], 2) ?></td>
                    <td><?= $b['pricing_type'] ?></td>
                    <td><?= htmlspecialchars($b['status']) ?></td>
                    <td><?= htmlspecialchars($b['booking_datetime']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <a href="dashboard_platform.php" class="back-link">← Back to Dashboard</a>
</div>
</body>
</html>

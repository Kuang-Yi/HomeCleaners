<?php
session_start();
require_once '../config/db.php';
require_once '../control/PlatformController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'P') {
    header('Location: login.php');
    exit();
}

$previewBookings = [];
$options = PlatformController::getReportOptions();

$selectedType = $_POST['time_type'] ?? 'daily';
$selectedValue = $_POST['time_value'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $previewBookings = PlatformController::getReportData($selectedType, $selectedValue);

    if (isset($_POST['download_pdf'])) {
        $pdf = PlatformController::generateBookingReportPDF($selectedType, $selectedValue, $previewBookings);
        $filename = "Booking_Report_" . $selectedType . "_" . str_replace(['-', 'W'], '_', $selectedValue) . ".pdf";
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
        }

        window.onload = updateDropdown;
    </script>
</head>
<body>
<div class="container">
    <h2>Generate Booking Report</h2>
    <div class="notice">Select a period and time range to preview and download a report.</div>

    <form method="post">
        <label>
            <input type="radio" name="time_type" value="daily"
                <?= $selectedType === 'daily' ? 'checked' : '' ?>
                onclick="updateDropdown()"> Daily
        </label>
        <label>
            <input type="radio" name="time_type" value="weekly"
                <?= $selectedType === 'weekly' ? 'checked' : '' ?>
                onclick="updateDropdown()"> Weekly
        </label>
        <label>
            <input type="radio" name="time_type" value="monthly"
                <?= $selectedType === 'monthly' ? 'checked' : '' ?>
                onclick="updateDropdown()"> Monthly
        </label>
        <br><br>

        <select name="time_value" id="time_value" required>
            <?php foreach ($options['daily'] as $val): ?>
                <option data-group="daily" value="<?= $val ?>" <?= ($selectedValue === $val) ? 'selected' : '' ?>>
                    <?= $val ?>
                </option>
            <?php endforeach; ?>
            <?php foreach ($options['weekly'] as $val): ?>
                <option data-group="weekly" value="<?= $val ?>" <?= ($selectedValue === $val) ? 'selected' : '' ?> style="display:none">
                    <?= $val ?>
                </option>
            <?php endforeach; ?>
            <?php foreach ($options['monthly'] as $val): ?>
                <option data-group="monthly" value="<?= $val ?>" <?= ($selectedValue === $val) ? 'selected' : '' ?> style="display:none">
                    <?= $val ?>
                </option>
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

<?php
require_once(__DIR__ . '/TCPDF-main/tcpdf.php'); // Include TCPDF

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vehicle_data'])) {
    $today_date = $_POST['date'];
    $vehicleData = unserialize(base64_decode($_POST['vehicle_data']));

    // Create PDF instance
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Vehicle Report');
    $pdf->SetTitle('Daily Vehicle Report - ' . $today_date);
    $pdf->AddPage();

    // Add title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Daily Vehicle Report - ' . $today_date, 0, 1, 'C');
    $pdf->Ln(5);

    // Add total vehicles count
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Total Vehicles Detected: ' . count($vehicleData), 0, 1, 'C');
    $pdf->Ln(5);

    // Table Header
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 10, 'Number Plate', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Timestamp', 1, 1, 'C');

    // Table Data
    $pdf->SetFont('helvetica', '', 12);
    if (!empty($vehicleData)) {
        foreach ($vehicleData as $data) {
            $pdf->Cell(60, 10, htmlspecialchars($data['plate_text']), 1, 0, 'C');
            $pdf->Cell(60, 10, htmlspecialchars($data['timestamp']), 1, 1, 'C');
        }
    } else {
        $pdf->Cell(120, 10, 'No records found for today.', 1, 1, 'C');
    }

    // Output PDF
    $pdf->Output('daily_report_' . $today_date . '.pdf', 'D'); // 'D' for download
    exit;
} else {
    echo "Invalid Request!";
}
?>

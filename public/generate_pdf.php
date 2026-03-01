<?php
require_once('TCPDF-main/tcpdf.php');

// Check if dates are set
if (!isset($_POST['from_date']) || !isset($_POST['to_date'])) {
    die("Invalid date range.");
}

$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];

// Firebase Realtime Database URL
$firebase_url = "https://notes-pro-f2823-default-rtdb.firebaseio.com/vehicles.json";

// Fetch Data from Firebase
function fetchVehicleData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true) ?? [];
}

$vehicleData = fetchVehicleData($firebase_url);

// Filter data based on date range
$filteredData = array_filter($vehicleData, function($data) use ($from_date, $to_date) {
    if (isset($data['timestamp'])) {
        $date = date("Y-m-d", strtotime($data['timestamp']));
        return ($date >= $from_date && $date <= $to_date);
    }
    return false;
});

// Create PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Vehicle Report');
$pdf->SetHeaderData('', 0, 'Vehicle Number Plate Report', "From: $from_date To: $to_date");

// Set margins
$pdf->SetMargins(10, 20, 10);
$pdf->AddPage();

// Table Header
$html = '<h2>Vehicle Number Plate Report</h2>';
$html .= '<table border="1" cellpadding="5">
            <tr>
                <th>Number Plate</th>
                <th>Timestamp</th>
            </tr>';

// Add Data
foreach ($filteredData as $data) {
    if (isset($data['plate_text'], $data['timestamp'])) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($data['plate_text']) . '</td>
                    <td>' . htmlspecialchars($data['timestamp']) . '</td>
                  </tr>';
    }
}

$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('Vehicle_Report.pdf', 'D');
?>

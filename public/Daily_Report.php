<?php
// Firebase Realtime Database URL
$firebase_url = "https://notes-pro-f2823-default-rtdb.firebaseio.com/vehicles.json";

// Fetch data from Firebase
function fetchVehicleData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Decode JSON response
    $data = json_decode($response, true);
    return is_array($data) ? $data : [];
}

// Get today's date in YYYY-MM-DD format
date_default_timezone_set('Asia/Kolkata'); // Set timezone if needed
$today_date = date('Y-m-d');

// Get vehicle data
$vehicleData = fetchVehicleData($firebase_url);
$todayData = [];

// Filter today's records
foreach ($vehicleData as $data) {
    if (isset($data['timestamp'])) {
        $recordDate = date('Y-m-d', strtotime($data['timestamp']));
        if ($recordDate === $today_date) {
            $todayData[] = $data;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report - Vehicle Records</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #007bff;
            --primary-gradient: linear-gradient(to right, #0078ff, #5b6af0);
            --secondary-color: #6c757d;
            --background-color: #f5f5f5;
            --text-color: #333333;
            --border-color: #dee2e6;
            --hover-color: #0056b3;
            --card-background: #ffffff;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: var(--card-background);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            padding: 30px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        h2 {
            color: var(--primary-color);
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
        }

        .summary {
            background: var(--primary-gradient);
            color: #ffffff;
            padding: 20px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            text-align: center;
            font-size: 1.2rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .summary:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .summary strong {
            font-size: 2rem;
            display: block;
            margin-top: 8px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--card-background);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--primary-color);
            color: #ffffff;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .no-results {
            text-align: center;
            font-size: 1.2rem;
            color: var(--warning-color);
            padding: 20px;
            font-style: italic;
        }

        .generate-pdf {
            text-align: center;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--success-color);
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            position: relative;
            font-family: 'Poppins', sans-serif;
            box-shadow: var(--shadow-sm);
        }

        .btn:hover {
            background-color: #0d9669;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Back to Home Button Styles */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--primary-gradient);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .btn-back:hover {
            box-shadow: 0 6px 20px rgba(0, 120, 255, 0.3);
            transform: translateY(-3px);
        }

        .btn-back i {
            transition: transform 0.3s ease;
        }

        .btn-back:hover i {
            transform: translateX(-4px);
        }

        .btn-back:hover span {
            transform: translateX(2px);
        }

        .btn-back span {
            transition: transform 0.3s ease;
        }

        /* Loading Spinner Styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s, visibility 0.3s;
            backdrop-filter: blur(5px);
        }

        .loading-overlay.active {
            visibility: visible;
            opacity: 1;
        }

        .spinner {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 6px solid transparent;
            border-top-color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            animation: spin 1.5s linear infinite;
        }

        .spinner:before, .spinner:after {
            content: '';
            position: absolute;
            border-radius: 50%;
        }

        .spinner:before {
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 6px solid transparent;
            border-top-color: var(--hover-color);
            border-bottom-color: var(--hover-color);
            animation: spin 2s linear infinite;
        }

        .spinner:after {
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 6px solid transparent;
            border-top-color: var(--success-color);
            border-bottom-color: var(--success-color);
            animation: spin 1s linear infinite;
        }

        .loading-text {
            position: absolute;
            top: calc(50% + 50px);
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Button with spinner */
        .btn-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            position: absolute;
            top: calc(50% - 10px);
            left: calc(50% - 10px);
        }

        button.loading {
            color: transparent;
        }

        button.loading .btn-spinner {
            display: block;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .header-container {
                flex-direction: column;
                align-items: flex-start;
            }

            table {
                font-size: 0.9rem;
            }

            th, td {
                padding: 12px;
            }

            .btn-back {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="spinner"></div>
        <div class="loading-text">Generating PDF...</div>
    </div>

    <div class="container">
        <!-- Header with title and back button -->
        <div class="header-container">
            <h2>Daily Vehicle Report</h2>
            <a href="admin_dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Home</span>
            </a>
        </div>

        <div class="summary">
            <p>Date: <?php echo $today_date; ?></p>
            <p>Total Vehicles Detected Today: <strong><?php echo count($todayData); ?></strong></p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Number Plate</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($todayData)) {
                    foreach ($todayData as $data) {
                        echo "<tr><td>" . htmlspecialchars($data['plate_text']) . "</td>
                                  <td>" . htmlspecialchars($data['timestamp']) . "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='2' class='no-results'>No records found for today.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="generate-pdf">
            <form action="daily_report_pdf.php" method="POST" id="pdf-form">
                <input type="hidden" name="date" value="<?php echo $today_date; ?>">
                <input type="hidden" name="vehicle_data" value="<?php echo base64_encode(serialize($todayData)); ?>">
                <button type="submit" id="generate-pdf-btn" class="btn">
                    Generate PDF
                    <span class="btn-spinner"></span>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadingOverlay = document.getElementById('loading-overlay');
            const pdfForm = document.getElementById('pdf-form');
            const pdfBtn = document.getElementById('generate-pdf-btn');

            // Function to show loading overlay
            function showLoading() {
                loadingOverlay.classList.add('active');
            }

            // Add loading spinner to PDF form submission
            pdfForm.addEventListener('submit', function(e) {
                // Show loading and button spinner
                pdfBtn.classList.add('loading');
                showLoading();
                
                // Allow form submission to continue
                setTimeout(function() {
                    // If PDF generation takes too long, hide the overlay
                    loadingOverlay.classList.remove('active');
                }, 10000); // 10 seconds timeout
            });
        });
    </script>
</body>
</html>
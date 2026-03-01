<?php
// Firebase Realtime Database URL
$firebase_url = "https://notes-pro-f2823-default-rtdb.firebaseio.com/vehicles.json";

// Fetch data from Firebase
function fetchVehicleData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification issues
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Decode JSON response
    return json_decode($response, true) ?: [];
}

// Get vehicle data
$vehicleData = fetchVehicleData($firebase_url);
$filteredData = [];

// Process date filter if applied
if (isset($_POST['from_date']) && isset($_POST['to_date'])) {
    $from_date = strtotime($_POST['from_date'] . " 00:00:00");
    $to_date = strtotime($_POST['to_date'] . " 23:59:59");
    
    foreach ($vehicleData as $data) {
        if (isset($data['timestamp'])) {
            $recordDate = strtotime($data['timestamp']);
            if ($recordDate >= $from_date && $recordDate <= $to_date) {
                $filteredData[] = $data;
            }
        }
    }
} else {
    $filteredData = $vehicleData;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specific Date Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #007bff;
            --primary-gradient: linear-gradient(to right, #0078ff, #5b6af0);
            --secondary-color: #6c757d;
            --background-color: #f8f9fa;
            --text-color: #333333;
            --border-color: #dee2e6;
            --hover-color: #0056b3;
            --card-background: #ffffff;
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
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-color);
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: var(--card-background);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        h2 {
            color: var(--primary-color);
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
        }

        .filter-form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        label {
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary-color);
        }

        input[type="date"] {
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
            width: 100%;
        }

        input[type="date"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        button {
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
            font-weight: 500;
            position: relative;
            font-family: 'Poppins', sans-serif;
        }

        button:hover {
            background-color: var(--hover-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 30px;
            background-color: var(--card-background);
            box-shadow: var(--shadow-sm);
            border-radius: var(--radius);
            overflow: hidden;
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
            color: var(--secondary-color);
            text-align: center;
            padding: 20px;
            font-style: italic;
        }

        .pdf-form {
            margin-top: 30px;
            text-align: center;
        }

        /* Loading Overlay Styles */
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
            border-top-color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
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

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .form-group {
                width: 100%;
            }

            input[type="date"], button {
                width: 100%;
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
        <div class="loading-text">Processing your request...</div>
    </div>

    <div class="container">
        <!-- Header with title and back button -->
        <div class="header-container">
            <h2>Generate Report</h2>
            <a href="admin_dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Home</span>
            </a>
        </div>

        <form method="POST" id="report-form" class="filter-form">
            <div class="form-group">
                <label for="from_date">From Date:</label>
                <input type="date" id="from_date" name="from_date" required>
            </div>
            <div class="form-group">
                <label for="to_date">To Date:</label>
                <input type="date" id="to_date" name="to_date" required>
            </div>
            <button type="submit" id="generate-report-btn">
                Generate Report
                <span class="btn-spinner"></span>
            </button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Number Plate</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($filteredData)) {
                    foreach ($filteredData as $data) {
                        echo "<tr><td>" . htmlspecialchars($data['plate_text']) . "</td>
                                  <td>" . htmlspecialchars($data['timestamp']) . "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='2' class='no-results'>No records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <form method="POST" action="generate_pdf.php" id="pdf-form" class="pdf-form">
            <input type="hidden" name="from_date" value="<?php echo isset($_POST['from_date']) ? $_POST['from_date'] : ''; ?>">
            <input type="hidden" name="to_date" value="<?php echo isset($_POST['to_date']) ? $_POST['to_date'] : ''; ?>">
            <button type="submit" id="generate-pdf-btn">
                Generate PDF
                <span class="btn-spinner"></span>
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadingOverlay = document.getElementById('loading-overlay');
            const reportForm = document.getElementById('report-form');
            const pdfForm = document.getElementById('pdf-form');
            const reportBtn = document.getElementById('generate-report-btn');
            const pdfBtn = document.getElementById('generate-pdf-btn');

            // Function to show loading overlay
            function showLoading() {
                loadingOverlay.classList.add('active');
            }

            // Add loading spinner to report form submission
            reportForm.addEventListener('submit', function(e) {
                // Validate dates
                const fromDate = document.getElementById('from_date').value;
                const toDate = document.getElementById('to_date').value;
                
                if (!fromDate || !toDate) {
                    alert('Please select both From and To dates');
                    e.preventDefault();
                    return;
                }
                
                if (new Date(fromDate) > new Date(toDate)) {
                    alert('From date cannot be later than To date');
                    e.preventDefault();
                    return;
                }
                
                // Show loading and button spinner
                reportBtn.classList.add('loading');
                showLoading();
            });

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
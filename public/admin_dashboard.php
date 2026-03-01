<?php
// session_start();
// // Check if admin is logged in
// if (!isset($_SESSION['admin_email'])) {
//     header("Location: admin_dashboard.php");
//     exit();
// }

// Firebase Realtime Database URL for system status messages
$firebase_url = "https://notes-pro-f2823-default-rtdb.firebaseio.com/system_status.json";

// Function to fetch System Status Messages from Firebase
function fetchSystemStatus($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification issues
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response
    $data = json_decode($response, true);
    return is_array($data) ? $data : [];
}

// Get system status messages
$systemStatus = fetchSystemStatus($firebase_url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2 class="sidebar-title">ANPR System</h2>
        </div>
        
        <div class="profile">
            <div class="profile-container">
                <div class="profile-image" id="profileIcon">
                    <img src="Admin.png" alt="Admin Profile">
                    <span class="status-indicator"></span>
                </div>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="My_Profile.html" class="dropdown-item"><i class="fas fa-user"></i> My Profile</a>
                    <a href="setting.html" class="dropdown-item"><i class="fas fa-cog"></i> Settings</a>
                    <hr>
                    <a href="logout.php" class="dropdown-item logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <h4 class="profile-name">Admin User</h4>
            <div class="profile-email">abc@gmail.com</div>
        </div>
        
        <div class="nav-links">
            <a href="#" class="nav-link active">
                <i class="fas fa-home"></i>
                <span>Admin Dashboard</span>
            </a>
            <a href="Vehicle_Details.php" class="nav-link">
                <i class="fas fa-car"></i>
                <span>Vehicle Details</span>
            </a>
            <a href="Registered_user.php" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Registered Users</span>
            </a>
            <a href="Daily_Report.php" class="nav-link">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
        
        <div class="social-icons">
            <a href="https://www.facebook.com/">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://www.instagram.com/">
                <i class="fab fa-instagram"></i>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <img src="logo2.jpg" alt="Logo">
            </div>
            <div class="title">
                <h1>AI Based Number Plate Recognition System</h1>
            </div>
            <div class="header-actions">
            <button class="notification-btn" onclick="window.location.href='notification.php'">
                <i class="fas fa-bell"></i>
            </button>
                <div class="user-dropdown">
                    <button class="user-dropdown-btn">
                        <span>Admin</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-container">
            <div class="welcome-card">
                <h2>Welcome to AI Based Number Plate Recognition System</h2>
                <p>This is your dashboard. Manage vehicle access control, check logs, and configure settings.</p>
            </div>

            <div class="card-container">
                <!-- Card 1 -->
                <a href="Vehicle_Details.php" class="card-link">
                    <div class="card">
                        <div class="card-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <h2>Vehicle Details</h2>
                        <p>View detailed information of detected vehicles.</p>
                    </div>
                </a>

                <!-- Card 2 -->
                <a href="Daily_Report.php" class="card-link">
                    <div class="card">
                        <div class="card-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h2>Daily Reports</h2>
                        <p>View detection reports by day.</p>
                    </div>
                </a>
                <!-- Card 3 -->
                <a href="Specific_Report.php" class="card-link">
                    <div class="card">
                        <div class="card-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h2>Specific Report</h2>
                        <p>View detection reports by day or month.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const profileIcon = document.getElementById("profileIcon");
            const dropdownMenu = document.getElementById("dropdownMenu");

            profileIcon.addEventListener("click", function(event) {
                event.stopPropagation();
                dropdownMenu.classList.toggle("active");
            });

            // Close dropdown when clicking outside
            document.addEventListener("click", function(event) {
                if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove("active");
                }
            });
        });


    </script>

</body>
</html>
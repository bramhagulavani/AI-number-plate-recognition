<?php
session_start();
include __DIR__ . '/../config/db_connect.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION["user_email"])) {
    echo "<script>alert('Please log in first!'); window.location.href='user_login.php';</script>";
    exit();
}

// Fetch user details from the database
$email = $_SESSION["user_email"];

$sql = "SELECT name, mobile, email, address, pincode, created_at FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "<script>alert('User not found!'); window.location.href='user_login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <style>
        :root {
            --primary-color: #0078ff;
            --secondary-color: #e0f7ff;
            --text-color: #333333;
            --background-color: #f8f9fa;
            --card-background: #ffffff;
            --border-color: #e0e0e0;
            --success-color: #10b981;
            --error-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
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
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: var(--card-background);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
            font-weight: 600;
        }

        .profile-info {
            display: grid;
            gap: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: var(--secondary-color);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .info-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-right: 15px;
            width: 30px;
            text-align: center;
        }

        .info-content {
            flex-grow: 1;
        }

        .info-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .info-value {
            color: var(--text-color);
        }

        .btn-back {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 25px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
            text-align: center;
        }

        .btn-back:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Custom Toastify Styles */
        .toastify {
            padding: 16px 20px;
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            backdrop-filter: blur(10px);
        }

        .toast-icon {
            font-size: 24px;
            flex-shrink: 0;
        }

        .toast-close {
            margin-left: auto;
            cursor: pointer;
            font-size: 16px;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .toast-message {
            flex-grow: 1;
        }

        .toast-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            border-left: 5px solid #059669;
        }

        .toast-error {
            background: linear-gradient(135deg, var(--error-color), #b91c1c);
            border-left: 5px solid #b91c1c;
        }

        .toast-warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
            border-left: 5px solid #d97706;
        }

        .toast-info {
            background: linear-gradient(135deg, var(--info-color), #1d4ed8);
            border-left: 5px solid #1d4ed8;
        }

        /* Action buttons in toast */
        .toast-action {
            padding: 4px 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            margin-left: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: background 0.3s;
        }

        .toast-action:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 20px;
            }

            h2 {
                font-size: 2rem;
            }

            .info-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-icon {
                margin-bottom: 10px;
            }

            .toastify {
                max-width: 90%;
                padding: 12px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Profile</h2>
        <div class="profile-info">
            <div class="info-item">
                <i class="fas fa-user info-icon"></i>
                <div class="info-content">
                    <div class="info-label">Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['name']); ?></div>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-mobile-alt info-icon"></i>
                <div class="info-content">
                    <div class="info-label">Mobile</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['mobile']); ?></div>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-envelope info-icon"></i>
                <div class="info-content">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-map-marker-alt info-icon"></i>
                <div class="info-content">
                    <div class="info-label">Address</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['address']); ?></div>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-map-pin info-icon"></i>
                <div class="info-content">
                    <div class="info-label">Pincode</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['pincode']); ?></div>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-calendar-alt info-icon"></i>
                <div class="info-content">
                    <div class="info-label">Created At</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['created_at']); ?></div>
                </div>
            </div>
        </div>
        <a href="Home.html" class="btn-back" id="back-btn">Back to Home</a>
    </div>

    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show welcome toast when page loads
            showToast({
                message: "Welcome back, <?php echo htmlspecialchars($user['name']); ?>!",
                type: "success",
                duration: 5000
            });

            // Add click event to back button to show a toast
            document.getElementById('back-btn').addEventListener('click', function(e) {
                e.preventDefault();
                
                showToast({
                    message: "Redirecting to home page...",
                    type: "info",
                    duration: 3000,
                    callback: function() {
                        window.location.href = "Home.html";
                    }
                });
            });

            // Function to show custom toasts
            function showToast({ message, type = "info", duration = 5000, callback = null }) {
                let icon, className;
                
                switch(type) {
                    case "success":
                        icon = '<i class="fas fa-check-circle toast-icon"></i>';
                        className = "toast-success";
                        break;
                    case "error":
                        icon = '<i class="fas fa-exclamation-circle toast-icon"></i>';
                        className = "toast-error";
                        break;
                    case "warning":
                        icon = '<i class="fas fa-exclamation-triangle toast-icon"></i>';
                        className = "toast-warning";
                        break;
                    case "info":
                    default:
                        icon = '<i class="fas fa-info-circle toast-icon"></i>';
                        className = "toast-info";
                        break;
                }

                Toastify({
                    text: `
                        <div class="toast-content">
                            ${icon}
                            <span class="toast-message">${message}</span>
                            <span class="toast-close"><i class="fas fa-times"></i></span>
                        </div>
                    `,
                    duration: duration,
                    gravity: "top",
                    position: "right",
                    className: className,
                    escapeMarkup: false,
                    onClick: function() {
                        this.hideToast();
                    },
                    callback: callback
                }).showToast();
            }

            // Example of how to show different types of toasts
            // Uncomment these to test different toast types
            
            /*
            // Success toast example
            setTimeout(() => {
                showToast({
                    message: "Profile loaded successfully!",
                    type: "success"
                });
            }, 1000);
            
            // Error toast example
            setTimeout(() => {
                showToast({
                    message: "Failed to update profile!",
                    type: "error"
                });
            }, 3000);
            
            // Warning toast example
            setTimeout(() => {
                showToast({
                    message: "Your session will expire soon!",
                    type: "warning"
                });
            }, 5000);
            
            // Info toast example with action
            setTimeout(() => {
                showToast({
                    message: "You have new notifications!",
                    type: "info"
                });
            }, 7000);
            */
        });
    </script>
</body>
</html>


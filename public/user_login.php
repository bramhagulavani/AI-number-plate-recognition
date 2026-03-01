<?php
session_start();
include __DIR__ . '/../config/db_connect.php'; // Database connection

// Variable to hold alert message and type
$alertMessage = "";
$alertType = "";
$showSpinner = false; // Default: Spinner should be hidden

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Default admin credentials
    $admin_email = "abc@gmail.com";
    $admin_password = "1234"; // Not hashed for simplicity

    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION["user_email"] = $email;
        $alertMessage = "Admin Login Successful!";
        $alertType = "success";
        $showSpinner = true; // Show spinner only for successful login
        echo "<script>
                setTimeout(function() {
                    window.location.href='admin_dashboard.php';
                }, 3000);
              </script>";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashed_password = $row["password"];

            if (password_verify($password, $hashed_password)) {
                $_SESSION["user_email"] = $email;
                $alertMessage = "User Login Successful!";
                $alertType = "success";
                $showSpinner = true; // Show spinner only for successful login
                echo "<script>
                        setTimeout(function() {
                            window.location.href='Home2.html';
                        }, 3000);
                      </script>";
            } else {
                $alertMessage = "Incorrect password!";
                $alertType = "error";
            }
        } else {
            $alertMessage = "Email not registered!";
            $alertType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/user.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <style>
        /* ... (Keep your existing styles) ... */

        /* Spinner Styles */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .spinner-grow {
            width: 3rem;
            height: 3rem;
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
            background: linear-gradient(135deg, #10b981, #059669);
            border-left: 5px solid #059669;
        }

        .toast-error {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border-left: 5px solid #b91c1c;
        }

        .toast-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-left: 5px solid #d97706;
        }

        .toast-info {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-left: 5px solid #1d4ed8;
        }
    </style>
</head>
<body>

<div class="container-box" id="loginForm">
    <div class="left-box"></div>
    <div class="right-box">
        <h2>Login</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">EMAIL</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">PASSWORD</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-login btn-block">Sign In</button>
            <div class="remember-me mt-2">
                <label for="rememberMe">Don't have an account? </label>
                <a href="user_registration.php" class="forgot-password ml-auto">User Registration</a>
            </div>
        </form>
    </div>
</div>

<!-- Loading Spinner -->
<div class="spinner-overlay" id="loadingSpinner" style="display:none;">
    <div class="spinner-grow text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Toastify JS -->
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    $(document).ready(function() {
        var showSpinner = <?php echo json_encode($showSpinner); ?>;
        var alertMessage = <?php echo json_encode($alertMessage); ?>;
        var alertType = <?php echo json_encode($alertType); ?>;
        
        if (showSpinner) {
            $('#loginForm').fadeOut();
            $('#loadingSpinner').fadeIn();
        }

        if (alertMessage) {
            showToast({
                message: alertMessage,
                type: alertType,
                duration: 5000
            });
        }

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
    });
</script>

</body>
</html>
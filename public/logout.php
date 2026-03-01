<?php
// We only destroy the session if the form is submitted
if (isset($_POST['confirm_logout']) && $_POST['confirm_logout'] === 'yes') {
    session_start();
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    
    // Set a flag to show the success toast
    $show_success = true;
} else {
    $show_success = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
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
        .toast-message {
            flex-grow: 1;
        }
        .toast-info {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-left: 5px solid #1d4ed8;
        }
        
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal-container {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            transform: translateY(0);
            animation: modal-appear 0.3s ease-out;
        }
        
        @keyframes modal-appear {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-icon {
            background-color: #fee2e2;
            color: #ef4444;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
        }
        
        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        
        .modal-content {
            margin-bottom: 25px;
            color: #4b5563;
            font-size: 15px;
            line-height: 1.5;
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-cancel {
            background-color: #f3f4f6;
            color: #4b5563;
        }
        
        .btn-cancel:hover {
            background-color: #e5e7eb;
        }
        
        .btn-confirm {
            background-color: #ef4444;
            color: white;
        }
        
        .btn-confirm:hover {
            background-color: #dc2626;
        }
    </style>
</head>
<body>
    <?php if (!$show_success): ?>
    <!-- Confirmation Modal -->
    <div class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h3 class="modal-title">Confirm Logout</h3>
            </div>
            <div class="modal-content">
                Are you sure you want to logout from admin? You will need to login again to access the admin/user panel.
            </div>
            <div class="modal-actions">
                <form method="post" style="display: flex; gap: 12px;">
                    <button type="button" class="btn btn-cancel" onclick="window.history.back()">Cancel</button>
                    <button type="submit" name="confirm_logout" value="yes" class="btn btn-confirm">Yes, Logout</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($show_success): ?>
            Toastify({
                text: `
                    <div class="toast-content">
                        <i class="fas fa-sign-out-alt toast-icon"></i>
                        <span class="toast-message">You have been successfully logged out.</span>
                    </div>
                `,
                duration: 3000,
                gravity: "top",
                position: "right",
                className: "toast-info",
                escapeMarkup: false,
                close: true,
                callback: function() {
                    window.location.href = "user_login.php";
                }
            }).showToast();

            // Redirect after the toast duration
            setTimeout(function() {
                window.location.href = "user_login.php";
            }, 3000);
            <?php endif; ?>
        });
    </script>
</body>
</html>
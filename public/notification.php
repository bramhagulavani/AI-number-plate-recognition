<?php
// Check if the request is a POST request for deleting a notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // Get the notification key from the POST data
    $notificationKey = isset($_POST['key']) ? $_POST['key'] : null;
    
    if ($notificationKey) {
        // Firebase Realtime Database URL for the specific notification
        $firebase_url = "https://notes-pro-f2823-default-rtdb.firebaseio.com/system_status/{$notificationKey}.json";
        
        // Initialize cURL session
        $ch = curl_init();
        
        // Set cURL options for DELETE request
        curl_setopt($ch, CURLOPT_URL, $firebase_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        // Execute cURL session
        $response = curl_exec($ch);
        
        // Check for errors
        if (curl_errno($ch)) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . curl_error($ch)]);
        } else {
            // Check HTTP status code
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode >= 200 && $httpCode < 300) {
                echo json_encode(['success' => true, 'message' => 'Notification deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete notification. HTTP Code: ' . $httpCode]);
            }
        }
        
        // Close cURL session
        curl_close($ch);
    } else {
        echo json_encode(['success' => false, 'message' => 'Notification key is required']);
    }
    
    // End execution after handling the delete request
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Notifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <style>
        :root {
            --primary-color: #4a6cf7;
            --secondary-color: #f5f8ff;
            --text-color: #333;
            --light-text: #6e6e6e;
            --danger-color: #ff4757;
            --success-color: #2ed573;
            --warning-color: #ffa502;
            --info-color: #4a6cf7;
            --border-radius: 8px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafc;
            color: var(--text-color);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
        }

        h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            position: relative;
            padding-bottom: 10px;
        }

        h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: var(--primary-color);
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: var(--secondary-color);
        }

        .no-notifications {
            color: var(--light-text);
            font-size: 18px;
            text-align: center;
            margin: 40px 0;
            padding: 20px;
            background: var(--secondary-color);
            border-radius: var(--border-radius);
        }

        .back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            border: none;
            background: var(--primary-color);
            color: white;
            cursor: pointer;
            font-size: 16px;
            border-radius: var(--border-radius);
            transition: var(--transition);
            text-decoration: none;
            font-weight: 500;
        }

        .back-btn:hover {
            background: #3a5bd9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
        }

        /* Delete button with animation */
        .delete-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .delete-btn:hover {
            background-color: rgba(255, 71, 87, 0.1);
        }

        .delete-btn i {
            color: var(--danger-color);
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .delete-btn:hover i {
            transform: scale(1.1);
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
            background: linear-gradient(135deg, var(--success-color), #20c997);
            border-left: 5px solid #20c997;
        }

        .toast-error {
            background: linear-gradient(135deg, var(--danger-color), #e03e52);
            border-left: 5px solid #e03e52;
        }

        .toast-warning {
            background: linear-gradient(135deg, var(--warning-color), #f39c12);
            border-left: 5px solid #f39c12;
        }

        .toast-info {
            background: linear-gradient(135deg, var(--info-color), #3a5bd9);
            border-left: 5px solid #3a5bd9;
        }

        /* Delete confirmation modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 450px;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .modal-overlay.active .modal-container {
            transform: translateY(0);
        }

        .modal-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .modal-header i {
            color: var(--danger-color);
            font-size: 24px;
            margin-right: 10px;
        }

        .modal-header h3 {
            color: var(--text-color);
            margin: 0;
            font-weight: 600;
        }

        .modal-content {
            margin-bottom: 20px;
            color: var(--text-color);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-btn-cancel {
            background-color: #e0e0e0;
            color: var(--text-color);
        }

        .modal-btn-cancel:hover {
            background-color: #d0d0d0;
        }

        .modal-btn-delete {
            background-color: var(--danger-color);
            color: white;
        }

        .modal-btn-delete:hover {
            background-color: #e03e52;
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
                margin: 20px auto;
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
    <h2>System Notifications</h2> <button class="back-btn" onclick="window.history.back();">Back to Dashboard</button>
    <div id="notification-table"></div>
    <!-- <button class="back-btn" onclick="window.history.back();">Back to Dashboard</button> -->
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Confirm Deletion</h3>
        </div>
        <div class="modal-content">
            <p>Are you sure you want to delete this notification?</p>
            <p>This action cannot be undone.</p>
        </div>
        <div class="modal-actions">
            <button id="cancel-delete" class="modal-btn modal-btn-cancel">Cancel</button>
            <button id="confirm-delete" class="modal-btn modal-btn-delete">
                <span id="delete-spinner" class="spinner" style="display: none;"></span>
                <span id="delete-text">Delete</span>
            </button>
        </div>
    </div>
</div>

<!-- Toastify JS -->
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const firebaseURL = "https://notes-pro-f2823-default-rtdb.firebaseio.com/system_status.json";
    let currentNotificationKey = null;
    
    // Modal elements
    const modal = document.getElementById('delete-modal');
    const cancelBtn = document.getElementById('cancel-delete');
    const confirmBtn = document.getElementById('confirm-delete');
    const deleteSpinner = document.getElementById('delete-spinner');
    const deleteText = document.getElementById('delete-text');
    
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

    // Function to fetch notifications
    function fetchNotifications() {
        fetch(firebaseURL)
            .then(response => response.json())
            .then(data => {
                renderNotifications(data);
            })
            .catch(error => {
                console.error("Error fetching notifications:", error);
                showToast({
                    message: "Failed to load notifications. Please try again.",
                    type: "error"
                });
            });
    }

    // Function to render notifications
    function renderNotifications(data) {
        const container = document.getElementById('notification-table');
        container.innerHTML = "";

        if (!data || Object.keys(data).length === 0) {
            container.innerHTML = '<p class="no-notifications">No new notifications available</p>';
            return;
        }

        const sorted = Object.entries(data).sort((a, b) => {
            return new Date(b[1].timestamp) - new Date(a[1].timestamp);
        });

        let table = `<table>
            <tr>
                <th>#</th>
                <th>Message</th>
                <th>Timestamp</th>
                <th>Action</th>
            </tr>`;

        let count = 1;
        for (const [key, value] of sorted) {
            if (value.status_message && value.timestamp) {
                table += `
                    <tr data-key="${key}">
                        <td>${count++}</td>
                        <td>${value.status_message}</td>
                        <td>${value.timestamp}</td>
                        <td>
                            <button class="delete-btn" onclick="confirmDelete('${key}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>`;
            }
        }

        table += "</table>";
        container.innerHTML = table;
    }

    // Function to confirm deletion
    window.confirmDelete = function(key) {
        currentNotificationKey = key;
        modal.classList.add('active');
    };

    // Function to delete notification
    function deleteNotification(key) {
        // Show loading spinner
        deleteSpinner.style.display = 'inline-block';
        deleteText.textContent = 'Deleting...';
        confirmBtn.disabled = true;
        
        // Create form data
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('key', key);
        
        // Send delete request
        fetch('notification.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Hide modal
            modal.classList.remove('active');
            
            // Reset button state
            deleteSpinner.style.display = 'none';
            deleteText.textContent = 'Delete';
            confirmBtn.disabled = false;
            
            if (data.success) {
                // Remove the row from the table
                const row = document.querySelector(`tr[data-key="${key}"]`);
                if (row) {
                    // Add fade-out animation
                    row.style.transition = 'opacity 0.5s ease';
                    row.style.opacity = '0';
                    
                    setTimeout(() => {
                        row.remove();
                        
                        // Show success toast
                        showToast({
                            message: "Notification deleted successfully!",
                            type: 'success',
                            duration: 3000
                        });
                        
                        // Check if table is empty and refresh
                        const tbody = document.querySelector('table');
                        if (!tbody || tbody.rows.length <= 1) {
                            fetchNotifications();
                        }
                    }, 500);
                }
            } else {
                // Show error toast
                showToast({
                    message: `Failed to delete notification: ${data.message}`,
                    type: 'error',
                    duration: 5000
                });
            }
        })
        .catch(error => {
            // Hide modal
            modal.classList.remove('active');
            
            // Reset button state
            deleteSpinner.style.display = 'none';
            deleteText.textContent = 'Delete';
            confirmBtn.disabled = false;
            
            // Show error toast
            showToast({
                message: `Error: ${error.message}`,
                type: 'error',
                duration: 5000
            });
        });
    }

    // Close modal when cancel button is clicked
    cancelBtn.addEventListener('click', function() {
        modal.classList.remove('active');
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });

    // Set up the confirm button to delete the notification
    confirmBtn.addEventListener('click', function() {
        if (currentNotificationKey) {
            deleteNotification(currentNotificationKey);
        }
    });

    // Show welcome toast
    showToast({
        message: "Welcome to System Notifications",
        type: "info",
        duration: 3000
    });

    // Initial load
    fetchNotifications();
    
    // Auto-refresh every 5 seconds
    setInterval(fetchNotifications, 5000);
});
</script>

</body>
</html>


<?php
// Check if the request is a POST request for deleting a record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // Get the record key from the POST data
    $recordKey = isset($_POST['key']) ? $_POST['key'] : null;
    
    if ($recordKey) {
        // Firebase Realtime Database URL for the specific record
        $firebase_url = "https://notes-pro-f2823-default-rtdb.firebaseio.com/vehicles/{$recordKey}.json";
        
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
                echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete record. HTTP Code: ' . $httpCode]);
            }
        }
        
        // Close cURL session
        curl_close($ch);
    } else {
        echo json_encode(['success' => false, 'message' => 'Record key is required']);
    }
    
    // End execution after handling the delete request
    exit;
}

// If not a delete request, continue with the normal page display
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

// Get vehicle data
$vehicleData = fetchVehicleData($firebase_url);

// Sort data by timestamp (Latest first)
usort($vehicleData, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vehicle Number Plate Data</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Toastify CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  
  <style>
    :root {
      --primary-color: #007bff;
      --primary-gradient: linear-gradient(to right, #0078ff, #5b6af0);
      --secondary-color: #e0f7ff;
      --text-color: #333333;
      --background-color: #f5f5f5;
      --card-background: #ffffff;
      --border-color: #e0e0e0;
      --success-color: #10b981;
      --error-color: #ef4444;
      --warning-color: #f59e0b;
      --info-color: #3b82f6;
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
      --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
      --radius: 12px;
      --transition: all 0.3s ease;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--background-color);
      text-align: center;
      margin: 20px;
      color: var(--text-color);
      line-height: 1.6;
    }
    
    h2 {
      color: var(--primary-color);
      margin-bottom: 20px;
      font-weight: 600;
    }
    
    #analytics {
      margin: 20px auto;
      font-size: 18px;
      color: var(--text-color);
      background-color: var(--card-background);
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      display: inline-block;
    }
    
    #search-input {
      display: block;
      margin: 20px auto;
      padding: 12px;
      width: 300px;
      font-size: 16px;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease;
    }
    
    #search-input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }
    
    table {
      width: 80%;
      margin: 20px auto;
      border-collapse: collapse;
      background: var(--card-background);
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      overflow: hidden;
    }
    
    th, td {
      border: 1px solid var(--border-color);
      padding: 12px;
      text-align: center;
    }
    
    th {
      background: var(--primary-color);
      color: white;
      font-weight: 500;
    }
    
    tr:nth-child(even) {
      background-color: rgba(0, 0, 0, 0.02);
    }
    
    tr:hover {
      background-color: rgba(0, 123, 255, 0.05);
    }
    
    .delete-icon {
      cursor: pointer;
      color: var(--error-color);
      font-size: 18px;
      transition: all 0.3s ease;
    }
    
    .delete-icon:hover {
      color: darkred;
      transform: scale(1.2);
    }
    
    .no-results {
      text-align: center;
      font-size: 18px;
      color: var(--error-color);
      padding: 20px;
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
      background-color: var(--card-background);
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
      color: var(--error-color);
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
      background-color: var(--error-color);
      color: white;
    }

    .modal-btn-delete:hover {
      background-color: #dc2626;
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

    /* Delete button with animation */
    .delete-btn {
      background: none;
      border: none;
      cursor: pointer;
      padding: 5px;
      border-radius: 50%;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .delete-btn:hover {
      background-color: rgba(239, 68, 68, 0.1);
    }

    .delete-btn i {
      color: var(--error-color);
      font-size: 18px;
      transition: all 0.3s ease;
    }

    .delete-btn:hover i {
      transform: scale(1.1);
    }

    /* Go To Home Button Styles */
    .button-container {
      display: flex;
      justify-content: center;
      margin: 20px 0;
    }

    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--primary-gradient);
      color: white;
      text-decoration: none;
      padding: 14px 28px;
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
      table {
        width: 95%;
      }
      
      #search-input {
        width: 90%;
      }
      
      .toastify {
        max-width: 90%;
        padding: 12px 16px;
      }
    }
  </style>

</head>
<body>
  <h2>Vehicle Number Plate Records</h2>
   <!-- Go To Home Button -->
   <div class="button-container">
    <a href="admin_dashboard.php" class="btn-back">
      <i class="fas fa-arrow-left"></i>
      <span>Back to Home</span>
    </a>
  </div>
  <!-- Analytics Dashboard -->
  <div id="analytics">
    <p>Total Vehicles Detected: <span id="total-count"><?php echo count($vehicleData); ?></span></p>
  </div>
  
  <!-- Search Filter -->
  <input type="text" id="search-input" placeholder="Search by number plate">

  <!-- Data Table -->
  <table>
    <thead>
      <tr>
        <th>Number Plate</th>
        <th>Timestamp</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="vehicle-data">
      <?php 
      if (!empty($vehicleData)) {
        foreach ($vehicleData as $key => $data) {
            if (isset($data['plate_text'], $data['timestamp'])) { 
                echo "<tr data-key='$key'>
                          <td>" . htmlspecialchars($data['plate_text']) . "</td>
                          <td>" . htmlspecialchars($data['timestamp']) . "</td>
                          <td>
                              <button class='delete-btn' onclick='confirmDelete(\"$key\", \"" . htmlspecialchars($data['plate_text']) . "\")'>
                                <i class='fas fa-trash-alt'></i>
                              </button>
                          </td>
                      </tr>";
            }
        }
      } else {
          echo "<tr><td colspan='3' class='no-results'>No records found.</td></tr>";
      }
      ?>
    </tbody>
  </table>

 

  <!-- Delete Confirmation Modal -->
  <div id="delete-modal" class="modal-overlay">
    <div class="modal-container">
      <div class="modal-header">
        <i class="fas fa-exclamation-triangle"></i>
        <h3>Confirm Deletion</h3>
      </div>
      <div class="modal-content">
        <p>Are you sure you want to delete the record for plate <strong id="delete-plate-text"></strong>?</p>
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
      // Show welcome toast when page loads
      showToast({
        message: "Vehicle data loaded successfully!",
        type: "success",
        duration: 3000
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

      // Make showToast available globally
      window.showToast = showToast;

      // Search functionality with toast notification
      document.getElementById("search-input").addEventListener("input", function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll("#vehicle-data tr");
        let count = 0;
        
        rows.forEach(row => {
          let plateText = row.cells[0]?.textContent.toLowerCase();
          if (plateText && plateText.includes(searchTerm)) {
            row.style.display = "";
            count++;
          } else {
            row.style.display = "none";
          }
        });
        
        document.getElementById("total-count").textContent = count;
        
        // Show toast for search results
        if (searchTerm.length > 0) {
          if (count > 0) {
            showToast({
              message: `Found ${count} vehicle(s) matching "${searchTerm}"`,
              type: "info",
              duration: 2000
            });
          } else {
            showToast({
              message: `No vehicles found matching "${searchTerm}"`,
              type: "warning",
              duration: 2000
            });
          }
        }
      });

      // Modal elements
      const modal = document.getElementById('delete-modal');
      const cancelBtn = document.getElementById('cancel-delete');
      const confirmBtn = document.getElementById('confirm-delete');
      const plateTextEl = document.getElementById('delete-plate-text');
      const deleteSpinner = document.getElementById('delete-spinner');
      const deleteText = document.getElementById('delete-text');

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

      // Make confirmDelete function available globally
      window.confirmDelete = function(key, plateText) {
        plateTextEl.textContent = plateText;
        modal.classList.add('active');
        
        // Set up the confirm button to delete the record
        confirmBtn.onclick = function() {
          deleteRecord(key, plateText);
        };
      };

      // Function to delete a record
      window.deleteRecord = function(key, plateText) {
        // Show loading spinner
        deleteSpinner.style.display = 'inline-block';
        deleteText.textContent = 'Deleting...';
        confirmBtn.disabled = true;
        
        // Create form data
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('key', key);
        
        // Send delete request to the same file
        fetch(window.location.href, {
          method: 'POST',
          body: formData
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
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
                
                // Update the total count
                const totalCount = document.getElementById('total-count');
                totalCount.textContent = parseInt(totalCount.textContent) - 1;
                
                // Show success toast
                showToast({
                  message: `Record for plate ${plateText} deleted successfully!`,
                  type: 'success',
                  duration: 3000
                });
                
                // Check if table is empty
                const tbody = document.getElementById('vehicle-data');
                if (tbody.children.length === 0) {
                  tbody.innerHTML = '<tr><td colspan="3" class="no-results">No records found.</td></tr>';
                }
              }, 500);
            }
          } else {
            // Show error toast
            showToast({
              message: `Failed to delete record: ${data.message}`,
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
          
          console.error('Error:', error);
        });
      };
    });
  </script>

</body>
</html>
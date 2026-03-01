<?php
$firebase_url = "https://notes-pro-f2823-default-rtdb.firebaseio.com/vehicles.json";  

function fetchVehicleData($url) {     
    $ch = curl_init();     
    curl_setopt($ch, CURLOPT_URL, $url);     
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);     
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);     
    $response = curl_exec($ch);     
    curl_close($ch);      
    
    $data = json_decode($response, true);     
    return is_array($data) ? $data : []; 
}  

$vehicleData = fetchVehicleData($firebase_url);  

// Sort data by latest timestamp
usort($vehicleData, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});

// Generate updated table rows
$output = "";
if (!empty($vehicleData)) {         
    foreach ($vehicleData as $key => $data) {             
        if (isset($data['plate_text'], $data['timestamp'])) {                  
            $output .= "<tr data-key='$key'>                           
                            <td>" . htmlspecialchars($data['plate_text']) . "</td>                           
                            <td>" . htmlspecialchars($data['timestamp']) . "</td>                           
                            <td>                              
                                <span class='delete-icon' onclick='deleteRecord(\"$key\", \"" . htmlspecialchars($data['plate_text']) . "\")'>🗑</span>                           
                            </td>                       
                        </tr>";             
        }         
    }       
} else {           
    $output = "<tr><td colspan='3' class='no-results'>No records found.</td></tr>";       
}

echo $output;
?>

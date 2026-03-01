<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "number_plate_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp']) {
        // Insert user details into the database
        $user_data = $_SESSION['user_data'];
        $name = $user_data['name'];
        $mobile = $user_data['mobile'];
        $email = $user_data['email'];
        $address = $user_data['address'];
        $pincode = $user_data['pincode'];
        $password = password_hash($user_data['password'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (name, mobile, email, address, pincode, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $mobile, $email, $address, $pincode, $password);

        if ($stmt->execute()) {
            echo "Registration successful! Redirecting to Admin...";
            header("Location: user_login.php");
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        session_destroy(); // Clear session after successful registration
    } else {
        echo "Invalid OTP!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
       
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e3f2fd, #bbdefb); /* Light Blue Gradient */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Container Box */
        .otp-container {
            background: rgba(255, 255, 255, 0.3); /* Glassmorphism Effect */
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 30px;
            width: 350px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        .otp-container:hover {
            transform: scale(1.02);
        }

        h2 {
            margin-bottom: 15px;
            color: #01579b; /* Deep Blue */
            font-size: 24px;
        }

        /* Input Field */
        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.7);
            box-shadow: inset 3px 3px 8px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            text-align: center;
            outline: none;
            transition: all 0.3s ease-in-out;
        }

        input[type="number"]:focus {
            background: #fff;
            box-shadow: 0px 0px 10px rgba(0, 87, 179, 0.3);
        }

        /* Button Styling */
        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            background: linear-gradient(to right, #0288d1, #01579b); /* Blue Gradient */
            color: white;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        button:hover {
            background: linear-gradient(to right, #01579b, #0288d1);
            transform: scale(1.05);
        }

        /* Responsive */
        @media (max-width: 400px) {
            .otp-container {
                width: 90%;
            }
        }
    </style>

</head>
<body>
    <div class="otp-container">
        <h2>Enter OTP</h2>
        <form method="POST">
            <input type="number" name="otp" placeholder="Enter 6-digit OTP" required>
            <button type="submit">Verify</button>
        </form>
    </div>

</body>

</html>

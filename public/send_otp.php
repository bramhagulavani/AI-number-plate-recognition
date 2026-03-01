<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Include PHPMailer

$host = "localhost";
$user = "root";
$password = "";
$dbname = "number_plate_db";

// Database connection
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$mobile = $_POST['mobile'];
$email = $_POST['email'];
$address = $_POST['address'];
$pincode = $_POST['pincode'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

// Generate OTP
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['email'] = $email;
$_SESSION['user_data'] = $_POST; // Store user details for later insertion

// Send OTP via email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'bramhagulavani@gmail.com'; // Your email
    $mail->Password = 'hhke hdoe dhva xhge'; // Your email password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your-email@gmail.com', 'Number Plate System');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP for Registration';
    $mail->Body    = "Your OTP for verification is <b>$otp</b>. Please enter this OTP to verify your email.";

    $mail->send();
    echo "OTP sent successfully. Redirecting...";
    header("Location: verify_otp.php");
} catch (Exception $e) {
    echo "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

$conn->close();
?>

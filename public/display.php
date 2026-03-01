<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "number_plate_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch records from database
$sql = "SELECT number_plate, entry_time FROM vehicle_records ORDER BY entry_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Number Plate Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        table {
            width: 50%;
            margin: auto;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: orange;
            color: white;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            text-decoration: none;
            background-color: darkorange;
            color: white;
            border-radius: 5px;
        }
        a:hover {
            background-color: red;
        }
    </style>
</head>
<body>

    <h2>Number Plate Records</h2>
    <table>
        <tr>
            <th>Number Plate</th>
            <th>Entry Time</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>".$row["number_plate"]."</td><td>".$row["entry_time"]."</td></tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No records found</td></tr>";
        }
        ?>
    </table>

    <a href="index.php">Back to Home</a>

</body>
</html>

<?php
// Close connection
$conn->close();
?>

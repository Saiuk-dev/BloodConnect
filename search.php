<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bloodconnect";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$blood_type = trim($_POST['blood_type']);
echo "Searching for: " . $blood_type . "<br>";

$sql = "SELECT * FROM donors WHERE blood_type = '$blood_type'";
$result = $conn->query($sql);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Donor Matches</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffe6e6, #fff);
            padding: 40px;
            text-align: center;
        }
        h2 {
            color: #cc0000;
            margin-bottom: 30px;
        }
        .results-container {
            width: 90%;
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #d90429;
            color: white;
        }
        tr:hover {
            background-color: #fff0f0;
        }
        .no-data {
            color: #d90429;
            font-size: 18px;
            margin-top: 30px;
        }
        a.btn-home {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 25px;
            background: #d90429;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
        }
        a.btn-home:hover {
            background: #a7001f;
        }
    </style>
</head>
<body>
    <div class='results-container'>
        <h2>Matching Donors for Blood Type: <span style='color:#d90429;'>$blood_type</span></h2>";

if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Age</th>
                <th>Appointment</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['age']}</td>
                <td>{$row['appointment']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p class='no-data'>No matching donors found.</p>";
}

echo "<a class='btn-home' href='need.html'>Search Again</a>
    </div>
</body>
</html>";

$conn->close();
?>
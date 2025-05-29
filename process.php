<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bloodconnect";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and validate input data
$name = $conn->real_escape_string($_POST['name']);
$email = $conn->real_escape_string($_POST['email']);
$age = intval($_POST['age']);
$blood_type = $conn->real_escape_string($_POST['blood-type']);
$appointment = $conn->real_escape_string($_POST['appointment']);
$message = $conn->real_escape_string($_POST['message']);

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO donors (name, email, age, blood_type, appointment, message) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssisss", $name, $email, $age, $blood_type, $appointment, $message);

// Execute the statement
if ($stmt->execute()) {
    // Success message
    echo '<div style="padding: 20px; background: #d4edda; color: #155724; border-radius: 8px; text-align: center; margin: 20px auto; max-width: 600px;">
            <h2>Thank you for your donation registration! 🩸</h2>
            <p>We will contact you shortly to confirm your appointment.</p>
            <a href="http://localhost/bloodconect/" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #ff4d4d; color: white; text-decoration: none; border-radius: 5px;">Back to Form</a>
          </div>';
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

<!-- CREATE THE DATABASE USE OF THIS TEXT -->

<!-- 
CREATE DATABASE blood_donation;
USE blood_donation;

CREATE TABLE donors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    blood_type VARCHAR(3) NOT NULL,
    appointment DATETIME NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); -->